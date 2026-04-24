<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Fixed Header -->
    <div class="fixed top-0 left-0 right-0 z-30 bg-primary text-primary-content shadow">
      <div class="flex items-center gap-3 px-4 py-3">
        <button @click="$router.back()" class="btn btn-sm btn-ghost btn-circle text-primary-content">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <div class="flex-1">
          <h1 class="text-base font-bold leading-tight">อัพโหลดสลิป</h1>
          <p class="text-xs opacity-80">สูงสุด 5 รูป (JPG, PNG)</p>
        </div>
        <span class="text-sm opacity-80 font-medium">{{ files.length }}/5</span>
      </div>
    </div>

    <!-- Content (offset header) -->
    <div class="pt-16 pb-32 px-4">

      <!-- Success -->
      <div v-if="success" class="mt-4 alert alert-success text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <div>
          <p class="font-medium">ส่งใบเสร็จสำเร็จ!</p>
          <p class="text-xs opacity-80">ระบบจะอนุมัติภายใน 24 ชั่วโมง</p>
        </div>
      </div>

      <!-- Upload Zone -->
      <div class="mt-4">
        <div v-if="files.length < 5"
             class="border-2 border-dashed rounded-2xl p-8 text-center cursor-pointer transition-colors"
             :class="dragging ? 'border-primary bg-red-50' : 'border-base-300 bg-white hover:border-primary hover:bg-red-50'"
             @click="$refs.fileInput.click()"
             @dragover.prevent="dragging = true"
             @dragleave="dragging = false"
             @drop.prevent="onDrop">
          <div class="text-4xl mb-3">📎</div>
          <p class="text-sm font-medium text-gray-600">แตะเพื่อเลือกรูปใบเสร็จ</p>
          <p class="text-xs text-gray-400 mt-1">JPG, PNG ไม่เกิน 5MB ต่อรูป</p>
          <p class="text-xs text-primary mt-2 font-medium">เหลืออีก {{ 5 - files.length }} รูป</p>
        </div>
        <input ref="fileInput" type="file" class="hidden"
               accept="image/jpeg,image/png" multiple
               @change="onFileChange">
      </div>

      <!-- Previews -->
      <div v-if="files.length > 0" class="mt-4 grid grid-cols-2 gap-3">
        <div v-for="(f, i) in files" :key="i"
             class="relative bg-white rounded-2xl shadow overflow-hidden aspect-square">
          <img :src="f.preview" class="w-full h-full object-cover">
          <!-- remove -->
          <button class="absolute top-2 right-2 bg-black/50 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs"
                  @click="removeFile(i)">✕</button>
          <!-- index badge -->
          <div class="absolute bottom-2 left-2 bg-black/50 text-white text-xs rounded-full px-2 py-0.5">
            {{ i + 1 }}
          </div>
        </div>

        <!-- add more slot -->
        <div v-if="files.length < 5"
             class="border-2 border-dashed border-base-300 rounded-2xl aspect-square flex flex-col items-center justify-center cursor-pointer hover:border-primary transition-colors bg-white"
             @click="$refs.fileInput.click()">
          <span class="text-2xl text-gray-300">+</span>
          <span class="text-xs text-gray-400 mt-1">เพิ่มรูป</span>
        </div>
      </div>

      <!-- Error -->
      <p v-if="error" class="mt-3 text-error text-sm px-1">{{ error }}</p>

    </div>

    <!-- Fixed Submit Button -->
    <div class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-100 px-4 py-3 safe-area-bottom">

      <!-- Loading state — สีแดงชัดเจน -->
      <div v-if="loading"
           class="btn btn-block text-white font-bold pointer-events-none"
           style="background:#E2001A; border-color:#E2001A;">
        <span class="loading loading-spinner loading-sm mr-2"></span>
        กำลังอัพโหลด...
      </div>

      <!-- Normal state -->
      <button v-else
              class="btn btn-primary btn-block"
              :disabled="files.length === 0"
              @click="submit">
        ส่งใบเสร็จ ({{ files.length }} รูป)
      </button>

    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import api from '@/composables/useApi';

const router    = useRouter();
const authStore = useAuthStore();

const loading  = ref(false);
const success  = ref(false);
const error    = ref('');
const dragging = ref(false);
const files    = ref([]);  // [{ file, preview }]

// บีบอัดรูปให้ไม่เกิน 500KB ด้วย Canvas
function compressImage(file, maxBytes = 500 * 1024) {
    return new Promise((resolve) => {
        if (file.size <= maxBytes) { resolve(file); return; }

        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            const canvas = document.createElement('canvas');

            // ลดขนาด pixel ถ้าใหญ่เกิน 1920px
            let { width, height } = img;
            const MAX_PX = 1920;
            if (width > MAX_PX || height > MAX_PX) {
                const ratio = Math.min(MAX_PX / width, MAX_PX / height);
                width  = Math.round(width  * ratio);
                height = Math.round(height * ratio);
            }
            canvas.width  = width;
            canvas.height = height;
            canvas.getContext('2d').drawImage(img, 0, 0, width, height);

            // ลด quality ทีละ 0.05 จนได้ขนาดที่ต้องการ
            let quality = 0.85;
            const tryExport = () => {
                canvas.toBlob((blob) => {
                    if (blob.size <= maxBytes || quality <= 0.2) {
                        resolve(new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' }));
                    } else {
                        quality = Math.round((quality - 0.05) * 100) / 100;
                        tryExport();
                    }
                }, 'image/jpeg', quality);
            };
            tryExport();
        };
        img.src = url;
    });
}

async function addFiles(rawFiles) {
    error.value = '';
    for (const file of rawFiles) {
        if (files.value.length >= 5) { error.value = 'เพิ่มได้สูงสุด 5 รูป'; break; }
        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            error.value = `${file.name}: รองรับเฉพาะ JPG, PNG`; continue;
        }
        if (file.size > 20 * 1024 * 1024) {
            error.value = `${file.name}: ไฟล์ใหญ่เกินไป`; continue;
        }
        const compressed = await compressImage(file);
        files.value.push({ file: compressed, preview: URL.createObjectURL(compressed) });
    }
}

function onFileChange(e) {
    addFiles(Array.from(e.target.files));
    e.target.value = '';
}

function onDrop(e) {
    dragging.value = false;
    addFiles(Array.from(e.dataTransfer.files));
}

function removeFile(i) {
    URL.revokeObjectURL(files.value[i].preview);
    files.value.splice(i, 1);
    error.value = '';
}

async function submit() {
    if (files.value.length === 0) return;
    error.value   = '';
    loading.value = true;
    try {
        const fd = new FormData();
        files.value.forEach(f => fd.append('images[]', f.file));
        await api.post('/api/liff/receipts', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        await authStore.fetchUser();
        success.value = true;
        files.value.forEach(f => URL.revokeObjectURL(f.preview));
        files.value = [];
        setTimeout(() => router.replace('/receipt'), 1500);
    } catch (e) {
        error.value = e.response?.data?.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่';
    } finally {
        loading.value = false;
    }
}
</script>

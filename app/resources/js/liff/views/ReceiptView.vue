<template>
  <div class="pb-20">
    <div class="bg-primary text-primary-content py-4 px-4">
      <h1 class="text-lg font-bold">ส่งใบเสร็จ</h1>
      <p class="text-sm opacity-80">อัพโหลดรูปใบเสร็จเพื่อสะสมคะแนน</p>
    </div>

    <div v-if="success" class="alert alert-success mx-4 mt-4">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      <span>ส่งใบเสร็จสำเร็จ! รอการตรวจสอบจาก Admin</span>
    </div>

    <!-- Banner -->
    <div v-if="banner" class="mx-4 mt-4">
      <img :src="banner.image_url" alt="" class="w-full rounded-lg object-cover max-h-36">
    </div>

    <form @submit.prevent="submitReceipt" class="px-4 mt-4">
      <!-- Receipt Image -->
      <div class="card bg-base-200 shadow mb-4">
        <div class="card-body">
          <h2 class="card-title text-sm mb-2">รูปใบเสร็จ <span class="text-error">*</span></h2>
          <div class="border-2 border-dashed border-base-300 rounded-lg p-4 text-center cursor-pointer hover:border-primary transition-colors"
               @click="$refs.fileInput.click()">
            <img v-if="previewUrl" :src="previewUrl" alt="Preview" class="max-h-48 mx-auto rounded-lg object-contain">
            <div v-else class="py-6">
              <div class="text-4xl mb-2">📷</div>
              <p class="text-sm text-gray-500">แตะเพื่ออัพโหลดรูปใบเสร็จ</p>
              <p class="text-xs text-gray-400">JPG, PNG (ไม่เกิน 5MB)</p>
            </div>
          </div>
          <input ref="fileInput" type="file" class="hidden" accept="image/jpeg,image/png"
                 @change="handleFileChange">
        </div>
      </div>

      <!-- Receipt Details -->
      <div class="card bg-base-200 shadow mb-4">
        <div class="card-body">
          <h2 class="card-title text-sm mb-2">ข้อมูลใบเสร็จ</h2>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">วันที่ซื้อ <span class="text-error">*</span></span></label>
            <input v-model="form.purchase_date" type="date" class="input input-bordered input-sm" required>
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">ยอดรวม (บาท) <span class="text-error">*</span></span></label>
            <input v-model="form.total_amount" type="number" step="0.01" min="0" class="input input-bordered input-sm" placeholder="0.00" required>
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">สาขาที่ซื้อ</span></label>
            <select v-model="form.shop_name" class="select select-bordered select-sm">
              <option value="">-- เลือกสาขา --</option>
              <option v-for="b in branches" :key="b.id" :value="b.name">{{ b.name }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- SKU Items -->
      <div class="card bg-base-200 shadow mb-4">
        <div class="card-body">
          <div class="flex items-center justify-between mb-2">
            <h2 class="card-title text-sm">รายการสินค้า</h2>
            <button type="button" class="btn btn-xs btn-outline btn-primary" @click="addSku">+ เพิ่ม</button>
          </div>
          <div v-for="(sku, idx) in form.skus" :key="idx" class="flex gap-2 mb-2">
            <input v-model="sku.sku_code" type="text" class="input input-bordered input-xs flex-1" placeholder="SKU">
            <input v-model="sku.product_name" type="text" class="input input-bordered input-xs flex-1" placeholder="ชื่อสินค้า">
            <input v-model.number="sku.quantity" type="number" min="1" class="input input-bordered input-xs w-16" placeholder="จำนวน">
            <button type="button" class="btn btn-xs btn-ghost text-error" @click="removeSku(idx)">✕</button>
          </div>
          <p class="text-xs text-gray-400 mt-1">ระบุสินค้า Eucerin ที่ซื้อ (ไม่บังคับ)</p>
        </div>
      </div>

      <p v-if="error" class="text-error text-sm mb-3">{{ error }}</p>
      <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
        <span v-if="loading" class="loading loading-spinner loading-sm"></span>
        ส่งใบเสร็จ
      </button>
    </form>
  </div>
  <BottomNav />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import BottomNav from '../components/BottomNav.vue';
import api from '../composables/useApi';

const authStore = useAuthStore();
const loading = ref(false);
const success = ref(false);
const error = ref('');
const previewUrl = ref(null);
const banner = ref(null);
const branches = ref([]);
const fileInput = ref(null);
const selectedFile = ref(null);

const form = ref({
    purchase_date: new Date().toISOString().slice(0, 10),
    total_amount: '',
    shop_name: '',
    skus: [{ sku_code: '', product_name: '', quantity: 1 }],
});

onMounted(async () => {
    try {
        const [bannerRes, branchRes] = await Promise.all([
            api.get('/api/liff/banner/receipt'),
            api.get('/api/liff/branches'),
        ]);
        banner.value = bannerRes.data;
        branches.value = branchRes.data;
    } catch (e) {
        // ignore
    }
});

function handleFileChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
        error.value = 'ไฟล์ขนาดใหญ่เกินไป (ไม่เกิน 5MB)';
        return;
    }
    selectedFile.value = file;
    previewUrl.value = URL.createObjectURL(file);
    error.value = '';
}

function addSku() {
    form.value.skus.push({ sku_code: '', product_name: '', quantity: 1 });
}

function removeSku(idx) {
    form.value.skus.splice(idx, 1);
}

async function submitReceipt() {
    if (!selectedFile.value) {
        error.value = 'กรุณาอัพโหลดรูปใบเสร็จ';
        return;
    }
    error.value = '';
    loading.value = true;
    try {
        const fd = new FormData();
        fd.append('image', selectedFile.value);
        fd.append('purchase_date', form.value.purchase_date);
        fd.append('total_amount', form.value.total_amount);
        fd.append('shop_name', form.value.shop_name);
        const validSkus = form.value.skus.filter(s => s.sku_code || s.product_name);
        fd.append('skus', JSON.stringify(validSkus));

        await api.post('/api/liff/receipts', fd, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        success.value = true;
        selectedFile.value = null;
        previewUrl.value = null;
        form.value.total_amount = '';
        form.value.skus = [{ sku_code: '', product_name: '', quantity: 1 }];
        await authStore.fetchUser();
    } catch (e) {
        if (e.response && e.response.data && e.response.data.message) {
            error.value = e.response.data.message;
        } else {
            error.value = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
        }
    } finally {
        loading.value = false;
    }
}
</script>

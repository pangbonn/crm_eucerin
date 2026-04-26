<template>
  <div class="pb-24 bg-gray-50 min-h-screen">

    <!-- 1. Activity Banner (แทน header) -->
    <div v-if="bannerMain" class="w-full">
      <img :src="bannerMain.image_url" alt="กิจกรรม" class="w-full object-cover max-h-52">
    </div>
    <div v-else class="bg-primary text-primary-content py-4 px-4">
      <h1 class="text-lg font-bold">สะสมคะแนน</h1>
      <p class="text-sm opacity-80">Eucerin Beauty Advisor CRM</p>
    </div>

    <!-- 2. CTA Banner (กดไปหน้าอัพโหลดสลิป) -->
    <div class="mx-4 mt-3">
      <button class="w-full" @click="$router.push('/upload')">
        <img v-if="bannerMain && bannerMain.button_bg_url" :src="bannerMain.button_bg_url" alt="อัพโหลดสลิป"
             class="w-full rounded-2xl object-cover shadow active:opacity-80 transition-opacity">
        <div v-else
             class="w-full rounded-2xl bg-primary text-primary-content py-4 flex items-center justify-center gap-2 shadow active:opacity-80 transition-opacity">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
          </svg>
          <span class="font-bold text-base">อัพโหลดสลิปเพื่อสะสมคะแนน</span>
        </div>
      </button>
    </div>

    <!-- 3. เงื่อนไขการเข้าร่วมกิจกรรม -->
    <div class="mx-4 mt-3">
      <button class="w-full flex items-center justify-between bg-white rounded-2xl px-4 py-3 shadow text-sm"
              @click="showTerms = true">
        <div class="flex items-center gap-2 text-primary font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          เงื่อนไขการเข้าร่วมกิจกรรม
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>
    </div>

    <!-- 4. Points Card -->
    <div class="mx-4 mt-3">
      <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-red-700 px-5 py-5 text-white">
          <p class="text-sm opacity-80 mb-1">คะแนนสะสมของคุณ</p>
          <p class="text-4xl font-bold tracking-wide">
            {{ totalPoints.toLocaleString() }}
            <span class="text-lg font-normal opacity-80">คะแนน</span>
          </p>
        </div>
        <div class="px-4 py-3 flex items-start gap-2 bg-amber-50 border-t border-amber-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
          <p class="text-xs text-amber-700">ระบบจะทำการอนุมัติยอดขายของท่าน ภายใน 24 ชั่วโมง นับจากวันที่อัพโหลดสลิป</p>
        </div>
      </div>
    </div>

    <!-- 5. Ranking -->
    <div class="mx-4 mt-4">
      <div class="bg-white rounded-2xl shadow">
        <div class="px-4 pt-4 pb-2 flex items-center gap-2">
          <span class="text-lg">🏆</span>
          <h2 class="font-bold text-sm">ยอดขายสูงสุด 5 อันดับ</h2>
        </div>
        <div v-if="ranking.length === 0" class="px-4 pb-4 text-center text-sm text-gray-400">
          ยังไม่มีข้อมูล
        </div>
        <div v-for="(r, i) in ranking" :key="r.rank"
             class="flex items-center gap-3 px-4 py-2.5"
             :class="i < ranking.length - 1 ? 'border-b border-gray-100' : ''">
          <!-- rank badge -->
          <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold"
               :class="r.rank === 1 ? 'bg-yellow-100 text-yellow-600' :
                       r.rank === 2 ? 'bg-gray-100 text-gray-600' :
                       r.rank === 3 ? 'bg-orange-100 text-orange-600' : 'bg-base-200 text-gray-500'">
            {{ r.rank === 1 ? '🥇' : r.rank === 2 ? '🥈' : r.rank === 3 ? '🥉' : r.rank }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate">{{ r.name }}</p>
            <p class="text-xs text-gray-400">{{ r.level }}</p>
          </div>
          <div class="text-right flex-shrink-0">
            <p class="text-sm font-bold text-primary">{{ r.total_points.toLocaleString() }}</p>
            <p class="text-xs text-gray-400">คะแนน</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom spacing -->
    <div class="h-4"></div>
  </div>

  <!-- Terms Bottom Sheet -->
  <Teleport to="body">
    <Transition name="sheet">
      <div v-if="showTerms" class="fixed inset-0 z-50 flex flex-col justify-end">
        <div class="absolute inset-0 bg-black/50" @click="showTerms = false"></div>
        <div class="relative bg-white rounded-t-3xl max-h-[80vh] flex flex-col">
          <div class="flex items-center justify-between px-5 py-4 border-b">
            <h3 class="font-bold text-base">เงื่อนไขการเข้าร่วมกิจกรรม</h3>
            <button class="btn btn-sm btn-ghost btn-circle" @click="showTerms = false">✕</button>
          </div>
          <div class="overflow-y-auto px-5 py-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
            {{ termsText || 'ไม่พบข้อมูลเงื่อนไข' }}
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useBannerStore } from '@/stores/banner';
import BottomNav from '@/components/BottomNav.vue';
import api from '@/composables/useApi';

const authStore   = useAuthStore();
const bannerStore = useBannerStore();
const bannerMain  = computed(() => bannerStore.get('receipt'));
const ranking     = ref([]);
const showTerms   = ref(false);

const totalPoints = computed(() => authStore.user?.receipt_points || 0);
const termsText   = computed(() => bannerMain.value?.condition_text || '');

onMounted(async () => {
    const results = await Promise.allSettled([
        bannerStore.fetch('receipt'),
        api.get('/api/liff/ranking'),
    ]);

    if (results[1].status === 'fulfilled') ranking.value = results[1].value.data?.slice(0, 5) || [];
});
</script>

<style scoped>
.sheet-enter-active, .sheet-leave-active { transition: opacity 0.25s, transform 0.25s; }
.sheet-enter-from, .sheet-leave-to { opacity: 0; }
.sheet-enter-from .relative, .sheet-leave-to .relative { transform: translateY(100%); }
</style>

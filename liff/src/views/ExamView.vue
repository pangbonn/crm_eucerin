<template>
  <div style="padding-top:180px" class="pb-20">

    <!-- Fixed Banner หรือ Default Header -->
    <div class="fixed top-0 left-0 right-0 z-40 overflow-hidden" style="height:180px">
      <img v-if="examBanner" :src="examBanner.image_url" alt="" class="w-full h-full object-cover">
      <div v-else class="w-full h-full bg-primary flex flex-col items-center justify-center text-primary-content">
        <p class="text-xl font-bold">Exam &amp; Training</p>
        <p class="text-sm opacity-80 mt-1">ทดสอบความรู้และรับคะแนน EC Passport</p>
      </div>
    </div>

    <!-- CTA Banner / ปุ่มกด -->
    <div class="px-4 pt-3 pb-1 space-y-2">
      <img v-if="examCtaBanner"
           :src="examCtaBanner.image_url"
           alt=""
           class="w-full rounded-xl cursor-pointer shadow"
           @click="goToList">
     
    </div>

    <!-- EC Passport Stamp Card -->
    <div class="card bg-base-200 shadow mx-4 mt-4">
      <div class="card-body py-4">
        <h2 class="card-title text-sm mb-2">EC Passport Card</h2>
        <div v-if="loading" class="flex justify-center py-2">
          <span class="loading loading-spinner loading-sm"></span>
        </div>
        <div v-else class="flex flex-wrap gap-2">
          <div v-for="i in stampConfig.stamp_max" :key="i"
               class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-lg"
               :class="i <= stampCount ? 'border-primary bg-primary text-primary-content' : 'border-base-300 bg-base-100 text-base-300'">
            {{ i <= stampCount ? '⭐' : i }}
          </div>
        </div>
        <p v-if="!loading" class="text-xs text-gray-500 mt-2">{{ stampCount }}/{{ stampConfig.stamp_max }} Stamp</p>
      </div>
    </div>

  </div>
  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useBannerStore } from '@/stores/banner';
import BottomNav from '@/components/BottomNav.vue';
import api from '@/composables/useApi';

const router        = useRouter();
const bannerStore   = useBannerStore();
const loading       = ref(true);
const examBanner    = computed(() => bannerStore.get('exam'));
const examCtaBanner = computed(() => bannerStore.get('exam_cta'));
const exams         = ref([]);
const stampConfig   = ref({ stamp_max: 8, stamp_points: 10 });

const stampCount = computed(() => {
    return exams.value.reduce((sum, p) => sum + (p.vdo.passed ? 1 : 0), 0);
});

onMounted(async () => {
    try {
        const [examRes, , , configRes] = await Promise.all([
            api.get('/api/liff/exams'),
            bannerStore.fetch('exam'),
            bannerStore.fetch('exam_cta'),
            api.get('/api/liff/stamp-config'),
        ]);
        exams.value       = examRes.data;
        stampConfig.value = configRes.data;
    } catch (e) {
        // ignore
    } finally {
        loading.value = false;
    }
});

function goToList() {
    const url = examCtaBanner.value && examCtaBanner.value.link_url;
    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
        window.open(url, '_blank');
    } else {
        router.push('/exam/list');
    }
}
</script>

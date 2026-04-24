<template>
  <div class="pb-20">
    <!-- Banner -->
    <div v-if="banner" class="w-full">
      <img :src="banner.image_url" alt="Banner" class="w-full object-cover max-h-48">
      <p v-if="banner.condition_text" class="text-xs text-center text-gray-500 p-2">{{ banner.condition_text }}</p>
    </div>

    <!-- User Info Card -->
    <div class="card bg-primary text-primary-content mx-4 mt-4 shadow">
      <div class="card-body py-4">
        <div class="flex items-center gap-3">
          <div class="avatar">
            <div class="w-14 rounded-full ring ring-primary-content ring-offset-primary ring-offset-1">
              <img :src="authStore.lineProfile ? authStore.lineProfile.pictureUrl : '/images/default-avatar.png'" alt="avatar">
            </div>
          </div>
          <div>
            <p class="font-bold text-lg">{{ authStore.user ? authStore.user.first_name : '' }} {{ authStore.user ? authStore.user.last_name : '' }}</p>
            <p class="text-sm opacity-80">{{ authStore.user ? authStore.user.employee_code : '' }}</p>
            <div class="badge badge-warning mt-1">{{ authStore.user ? authStore.user.level : '' }}</div>
          </div>
          <div class="ml-auto text-right">
            <p class="text-2xl font-bold">{{ totalPoints }}</p>
            <p class="text-xs opacity-80">คะแนนสะสม</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stamp Card -->
    <div class="card bg-base-200 shadow mx-4 mt-4">
      <div class="card-body py-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="font-bold text-sm">การ์ดสะสม Stamp</h2>
          <span class="text-xs text-gray-500">{{ stampCount }}/{{ stampConfig.stamp_max }} Stamp</span>
        </div>
        <div class="flex flex-wrap gap-2">
          <div v-for="i in stampConfig.stamp_max" :key="i"
               class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-lg font-bold"
               :class="i <= stampCount ? 'border-primary bg-primary text-primary-content' : 'border-base-300 bg-base-100 text-base-300'">
            {{ i <= stampCount ? '⭐' : i }}
          </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">ทุก {{ stampConfig.stamp_points }} คะแนน = 1 Stamp</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-3 mx-4 mt-4">
      <router-link to="/receipt"
        class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer">
        <div class="card-body items-center text-center py-5">
          <div class="text-3xl mb-1">🧾</div>
          <p class="font-semibold text-sm">สะสมคะแนน</p>
          <p class="text-xs text-gray-500">ส่งใบเสร็จ</p>
        </div>
      </router-link>

      <router-link to="/products"
        class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer">
        <div class="card-body items-center text-center py-5">
          <div class="text-3xl mb-1">🧴</div>
          <p class="font-semibold text-sm">ข้อมูลสินค้า</p>
          <p class="text-xs text-gray-500">ข้อมูลสินค้า</p>
        </div>
      </router-link>

      <router-link to="/rewards"
        class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer">
        <div class="card-body items-center text-center py-5">
          <div class="text-3xl mb-1">🎁</div>
          <p class="font-semibold text-sm">แลกรางวัล</p>
          <p class="text-xs text-gray-500">{{ totalPoints }} คะแนน</p>
        </div>
      </router-link>

      <router-link to="/profile"
        class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer">
        <div class="card-body items-center text-center py-5">
          <div class="text-3xl mb-1">👤</div>
          <p class="font-semibold text-sm">โปรไฟล์</p>
          <p class="text-xs text-gray-500">ข้อมูลของฉัน</p>
        </div>
      </router-link>
    </div>
  </div>
  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import BottomNav from '@/components/BottomNav.vue';
import api from '@/composables/useApi';

const authStore  = useAuthStore();
const banner     = ref(null);
const stampConfig = ref({ stamp_max: 8, stamp_points: 10 });

const totalPoints = computed(() => {
    if (!authStore.user) return 0;
    return authStore.user.receipt_points || 0;
});

const stampCount = computed(() => {
    const earned = Math.floor(totalPoints.value / stampConfig.value.stamp_points);
    return Math.min(earned, stampConfig.value.stamp_max);
});

onMounted(async () => {
    try {
        const [bannerRes, configRes] = await Promise.all([
            api.get('/api/liff/banner/main'),
            api.get('/api/liff/stamp-config'),
        ]);
        banner.value      = bannerRes.data;
        stampConfig.value = configRes.data;
    } catch (e) {
        // ignore
    }
});
</script>

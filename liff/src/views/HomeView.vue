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

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-3 mx-4 mt-4">
      <router-link to="/liff/receipt"
        class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer">
        <div class="card-body items-center text-center py-5">
          <div class="text-3xl mb-1">🧾</div>
          <p class="font-semibold text-sm">ส่งใบเสร็จ</p>
          <p class="text-xs text-gray-500">เก็บคะแนน</p>
        </div>
      </router-link>

      <router-link to="/liff/exam"
        class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer">
        <div class="card-body items-center text-center py-5">
          <div class="text-3xl mb-1">📚</div>
          <p class="font-semibold text-sm">Exam & Training</p>
          <p class="text-xs text-gray-500">ทดสอบความรู้</p>
        </div>
      </router-link>

      <router-link to="/liff/rewards"
        class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer">
        <div class="card-body items-center text-center py-5">
          <div class="text-3xl mb-1">🎁</div>
          <p class="font-semibold text-sm">แลกรางวัล</p>
          <p class="text-xs text-gray-500">{{ totalPoints }} คะแนน</p>
        </div>
      </router-link>

      <router-link to="/liff/qa"
        class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer">
        <div class="card-body items-center text-center py-5">
          <div class="text-3xl mb-1">❓</div>
          <p class="font-semibold text-sm">Q&amp;A</p>
          <p class="text-xs text-gray-500">คำถามที่พบบ่อย</p>
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

const authStore = useAuthStore();
const banner = ref(null);

const totalPoints = computed(() => {
    if (!authStore.user) return 0;
    return authStore.user.total_points || 0;
});

onMounted(async () => {
    try {
        const { data } = await api.get('/api/liff/banner/main');
        banner.value = data;
    } catch (e) {
        // no banner
    }
});
</script>

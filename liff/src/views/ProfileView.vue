<template>
  <div class="pb-24">
    <div class="bg-primary text-primary-content py-6 px-4 text-center">
      <div class="avatar mb-3">
        <div class="w-20 rounded-full ring ring-primary-content ring-offset-primary ring-offset-2">
          <img :src="auth.lineProfile ? auth.lineProfile.pictureUrl : '/default-avatar.png'"
               alt="avatar" @error="e => e.target.src='/default-avatar.png'">
        </div>
      </div>
      <h1 class="text-xl font-bold">{{ user ? user.first_name + ' ' + user.last_name : '-' }}</h1>
      <p class="text-sm opacity-80 mt-1">{{ user ? user.employee_code : '' }}</p>
      <div class="badge badge-warning mt-2">{{ user ? user.level : '' }}</div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-px bg-base-300 mx-4 mt-4 rounded-xl overflow-hidden shadow">
      <div class="bg-base-100 p-4 text-center">
        <p class="text-2xl font-bold text-primary">{{ user ? user.total_points : 0 }}</p>
        <p class="text-xs text-gray-500">คะแนนสะสม</p>
      </div>
      <div class="bg-base-100 p-4 text-center">
        <p class="text-2xl font-bold text-success">{{ stampCount }}</p>
        <p class="text-xs text-gray-500">Stamps</p>
      </div>
      <div class="bg-base-100 p-4 text-center">
        <p class="text-2xl font-bold text-warning">{{ redemptionCount }}</p>
        <p class="text-xs text-gray-500">แลกแล้ว</p>
      </div>
    </div>

    <!-- Branch Info -->
    <div v-if="user && user.branch" class="card bg-base-200 shadow mx-4 mt-4">
      <div class="card-body py-3">
        <h2 class="card-title text-sm mb-1">สาขาที่ปฏิบัติงาน</h2>
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <p class="text-sm">{{ user.branch.name }}</p>
          <div class="badge badge-ghost badge-sm ml-auto">{{ user.branch.zone }}</div>
        </div>
      </div>
    </div>

    <!-- Redemption History -->
    <div class="px-4 mt-4">
      <h2 class="font-bold mb-3">ประวัติการแลกของรางวัล</h2>

      <div v-if="historyLoading" class="text-center py-6">
        <span class="loading loading-spinner loading-md"></span>
      </div>
      <div v-else-if="redemptions.length === 0" class="text-center py-6 text-gray-400">
        <div class="text-3xl mb-2">🎁</div>
        <p class="text-sm">ยังไม่มีประวัติการแลก</p>
      </div>
      <div v-else>
        <div v-for="r in redemptions" :key="r.id"
             class="flex items-center gap-3 py-3 border-b border-base-200 last:border-0">
          <div class="w-10 h-10 rounded-full bg-base-300 flex items-center justify-center text-xl flex-shrink-0">🎁</div>
          <div class="flex-1">
            <p class="text-sm font-medium">{{ r.reward_name }}</p>
            <p class="text-xs text-gray-400">{{ formatDate(r.created_at) }}</p>
          </div>
          <div class="badge badge-sm"
               :class="{ 'badge-warning': r.status === 'pending', 'badge-success': r.status === 'approved', 'badge-error': r.status === 'rejected' }">
            {{ statusLabel(r.status) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Logout -->
    <div class="px-4 mt-6">
      <button class="btn btn-outline btn-error btn-block" @click="handleLogout">ออกจากระบบ</button>
    </div>
  </div>
  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import BottomNav from '@/components/BottomNav.vue';
import api from '@/composables/useApi';

const auth            = useAuthStore();
const router          = useRouter();
const redemptions     = ref([]);
const historyLoading  = ref(true);

const user = computed(() => auth.user);

const stampCount = computed(() => {
    // ดึงจาก exam results ถ้ามี — ใช้ placeholder ก่อน
    return 0;
});

const redemptionCount = computed(() => redemptions.value.filter(r => r.status === 'approved').length);

onMounted(async () => {
    try {
        const { data } = await api.get('/api/liff/redemptions');
        redemptions.value = data;
    } catch (e) {
        // ignore
    } finally {
        historyLoading.value = false;
    }
});

function handleLogout() {
    auth.logout();
    router.replace('/register');
}

function statusLabel(status) {
    return { pending: 'รอดำเนินการ', approved: 'อนุมัติแล้ว', rejected: 'ไม่อนุมัติ' }[status] || status;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

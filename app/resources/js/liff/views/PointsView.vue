<template>
  <div class="pb-20">
    <div class="bg-primary text-primary-content py-4 px-4">
      <h1 class="text-lg font-bold">คะแนนสะสม</h1>
      <p class="text-sm opacity-80">ประวัติการสะสมและใช้คะแนน</p>
    </div>

    <!-- Summary Card -->
    <div class="card bg-gradient-to-r from-primary to-secondary text-primary-content mx-4 mt-4 shadow-lg">
      <div class="card-body py-4 flex-row items-center justify-between">
        <div>
          <p class="text-sm opacity-80">คะแนนสะสมทั้งหมด</p>
          <p class="text-4xl font-bold">{{ totalPoints }}</p>
        </div>
        <div class="text-right">
          <div class="badge badge-warning badge-lg">{{ authStore.user ? authStore.user.level : '' }}</div>
          <p class="text-xs opacity-75 mt-1">ระดับปัจจุบัน</p>
        </div>
      </div>
    </div>

    <!-- History -->
    <div class="px-4 mt-4">
      <h2 class="font-bold mb-3">ประวัติคะแนน</h2>

      <div v-if="loading" class="text-center py-8">
        <span class="loading loading-spinner loading-md"></span>
      </div>

      <div v-else-if="points.length === 0" class="text-center py-8 text-gray-400">
        <div class="text-4xl mb-2">📊</div>
        <p>ยังไม่มีประวัติคะแนน</p>
      </div>

      <div v-else>
        <div v-for="p in points" :key="p.id"
             class="flex items-center gap-3 py-3 border-b border-base-200 last:border-0">
          <div class="rounded-full p-2"
               :class="p.points > 0 ? 'bg-success/20 text-success' : 'bg-error/20 text-error'">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path v-if="p.points > 0" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium">{{ typeLabel(p.type) }}</p>
            <p class="text-xs text-gray-400">{{ formatDate(p.created_at) }}</p>
            <p v-if="p.note" class="text-xs text-gray-500">{{ p.note }}</p>
          </div>
          <div class="text-right">
            <p class="font-bold" :class="p.points > 0 ? 'text-success' : 'text-error'">
              {{ p.points > 0 ? '+' : '' }}{{ p.points }}
            </p>
            <p class="text-xs text-gray-400">คะแนน</p>
          </div>
        </div>

        <button v-if="hasMore" class="btn btn-outline btn-sm btn-block mt-4" @click="loadMore" :disabled="loadingMore">
          <span v-if="loadingMore" class="loading loading-spinner loading-xs"></span>
          โหลดเพิ่มเติม
        </button>
      </div>
    </div>
  </div>
  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import BottomNav from '../components/BottomNav.vue';
import api from '../composables/useApi';

const authStore = useAuthStore();
const loading = ref(true);
const loadingMore = ref(false);
const points = ref([]);
const page = ref(1);
const hasMore = ref(false);

const totalPoints = computed(() => {
    return authStore.user ? (authStore.user.total_points || 0) : 0;
});

onMounted(() => fetchPoints());

async function fetchPoints() {
    try {
        const { data } = await api.get('/api/liff/points?page=' + page.value);
        points.value = data.data;
        hasMore.value = !!data.next_page_url;
    } catch (e) {
        // ignore
    } finally {
        loading.value = false;
    }
}

async function loadMore() {
    loadingMore.value = true;
    page.value++;
    try {
        const { data } = await api.get('/api/liff/points?page=' + page.value);
        points.value.push(...data.data);
        hasMore.value = !!data.next_page_url;
    } finally {
        loadingMore.value = false;
    }
}

function typeLabel(type) {
    const labels = {
        receipt: 'สะสมจากใบเสร็จ',
        exam: 'ทดสอบ Exam',
        adjust: 'ปรับคะแนน (Admin)',
        redeem: 'แลกของรางวัล',
        refund: 'คืนคะแนน',
    };
    return labels[type] || type;
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

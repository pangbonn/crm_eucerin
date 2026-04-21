<template>
  <div class="pb-20">
    <div class="bg-primary text-primary-content py-4 px-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-lg font-bold">แลกรางวัล</h1>
          <p class="text-sm opacity-80">คะแนนของคุณ: <strong>{{ totalPoints }}</strong> คะแนน</p>
        </div>
      </div>
    </div>

    <!-- Banner -->
    <div v-if="banner" class="mx-4 mt-4">
      <img :src="banner.image_url" alt="" class="w-full rounded-lg object-cover max-h-36">
    </div>

    <!-- My Redemptions Toggle -->
    <div class="tabs tabs-boxed mx-4 mt-4">
      <a class="tab" :class="{ 'tab-active': activeTab === 'rewards' }" @click="activeTab = 'rewards'">ของรางวัล</a>
      <a class="tab" :class="{ 'tab-active': activeTab === 'history' }" @click="activeTab = 'history'">ประวัติแลก</a>
    </div>

    <!-- Rewards Grid -->
    <div v-if="activeTab === 'rewards'" class="px-4 mt-4">
      <div v-if="loading" class="text-center py-8"><span class="loading loading-spinner loading-md"></span></div>
      <div v-else-if="rewards.length === 0" class="text-center py-8 text-gray-400">
        <div class="text-4xl mb-2">🎁</div>
        <p>ยังไม่มีรางวัล</p>
      </div>
      <div v-else class="grid grid-cols-2 gap-3">
        <div v-for="reward in rewards" :key="reward.id"
             class="card bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer"
             @click="openReward(reward)">
          <figure class="pt-4 px-4">
            <img v-if="reward.image_url" :src="rewardImage(reward)" alt=""
                 class="rounded-xl w-full h-28 object-cover">
            <div v-else class="w-full h-28 rounded-xl bg-base-300 flex items-center justify-center text-4xl">🎁</div>
          </figure>
          <div class="card-body p-3">
            <p class="font-semibold text-sm line-clamp-2">{{ reward.name }}</p>
            <div class="flex items-center justify-between mt-1">
              <span class="text-primary font-bold text-sm">{{ reward.points_required }} คะแนน</span>
              <span class="text-xs text-gray-400">คงเหลือ {{ reward.stock }}</span>
            </div>
            <div class="badge badge-sm mt-1"
                 :class="reward.stock > 0 && totalPoints >= reward.points_required ? 'badge-success' : 'badge-ghost'">
              {{ reward.stock > 0 ? (totalPoints >= reward.points_required ? 'แลกได้' : 'คะแนนไม่พอ') : 'หมด' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Redemption History -->
    <div v-else class="px-4 mt-4">
      <div v-if="historyLoading" class="text-center py-8"><span class="loading loading-spinner loading-md"></span></div>
      <div v-else-if="redemptions.length === 0" class="text-center py-8 text-gray-400">
        <p>ยังไม่มีประวัติการแลกรางวัล</p>
      </div>
      <div v-else>
        <div v-for="r in redemptions" :key="r.id" class="card bg-base-200 shadow mb-3">
          <div class="card-body py-3">
            <div class="flex gap-3">
              <div class="w-12 h-12 rounded-lg bg-base-300 flex items-center justify-center text-2xl flex-shrink-0">🎁</div>
              <div class="flex-1">
                <p class="font-semibold text-sm">{{ r.reward_name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(r.created_at) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ r.points_used }} คะแนน</p>
              </div>
              <div class="badge badge-sm"
                   :class="{ 'badge-warning': r.status === 'pending', 'badge-success': r.status === 'approved', 'badge-error': r.status === 'rejected' }">
                {{ statusLabel(r.status) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Redeem Modal -->
    <div v-if="selectedReward" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">{{ selectedReward.name }}</h3>
        <img v-if="selectedReward.image_url" :src="rewardImage(selectedReward)"
             class="w-full rounded-lg h-40 object-cover my-3" alt="">
        <p class="text-gray-600 text-sm">{{ selectedReward.description }}</p>
        <div class="flex items-center justify-between my-3 p-3 bg-base-200 rounded-lg">
          <span class="text-sm">คะแนนที่ต้องใช้</span>
          <span class="font-bold text-primary text-lg">{{ selectedReward.points_required }} คะแนน</span>
        </div>
        <div class="flex items-center justify-between my-1 p-3 bg-base-200 rounded-lg">
          <span class="text-sm">คะแนนของคุณ</span>
          <span class="font-bold">{{ totalPoints }} คะแนน</span>
        </div>
        <p v-if="redeemError" class="text-error text-sm mt-2">{{ redeemError }}</p>
        <div class="modal-action">
          <button class="btn btn-ghost" @click="selectedReward = null">ยกเลิก</button>
          <button class="btn btn-primary" @click="confirmRedeem"
                  :disabled="selectedReward.stock === 0 || totalPoints < selectedReward.points_required || redeeming">
            <span v-if="redeeming" class="loading loading-spinner loading-xs"></span>
            ยืนยันแลกรางวัล
          </button>
        </div>
      </div>
      <div class="modal-backdrop" @click="selectedReward = null"></div>
    </div>
  </div>
  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import BottomNav from '../components/BottomNav.vue';
import api from '../composables/useApi';

const authStore = useAuthStore();
const loading = ref(true);
const historyLoading = ref(false);
const rewards = ref([]);
const redemptions = ref([]);
const banner = ref(null);
const selectedReward = ref(null);
const redeemError = ref('');
const redeeming = ref(false);
const activeTab = ref('rewards');

const totalPoints = computed(() => authStore.user ? (authStore.user.total_points || 0) : 0);

onMounted(async () => {
    try {
        const [rRes, bRes] = await Promise.all([
            api.get('/api/liff/rewards'),
            api.get('/api/liff/banner/reward'),
        ]);
        rewards.value = rRes.data;
        banner.value = bRes.data;
    } catch (e) {
        // ignore
    } finally {
        loading.value = false;
    }
});

watch(activeTab, async (val) => {
    if (val === 'history' && redemptions.value.length === 0) {
        historyLoading.value = true;
        try {
            const { data } = await api.get('/api/liff/redemptions');
            redemptions.value = data;
        } finally {
            historyLoading.value = false;
        }
    }
});

function rewardImage(reward) {
    if (!reward.image_url) return '';
    if (reward.image_url.startsWith('http')) return reward.image_url;
    return '/storage/' + reward.image_url;
}

function openReward(reward) {
    selectedReward.value = reward;
    redeemError.value = '';
}

async function confirmRedeem() {
    redeeming.value = true;
    redeemError.value = '';
    try {
        await api.post('/api/liff/redeem', { reward_id: selectedReward.value.id });
        await authStore.fetchUser();
        selectedReward.value = null;
        activeTab.value = 'history';
        redemptions.value = [];
    } catch (e) {
        redeemError.value = e.response && e.response.data && e.response.data.message
            ? e.response.data.message : 'เกิดข้อผิดพลาด';
    } finally {
        redeeming.value = false;
    }
}

function statusLabel(status) {
    return { pending: 'รอดำเนินการ', approved: 'อนุมัติแล้ว', rejected: 'ไม่อนุมัติ' }[status] || status;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

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
        <div v-for="section in redemptionsByMonth" :key="section.key" class="mb-4">
          <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ section.label }}</h3>
          <div class="bg-base-100 rounded-xl border border-base-200 overflow-hidden">
            <div v-for="r in section.items" :key="r.id"
                 class="flex items-center gap-3 py-3 px-2 border-b border-base-200 last:border-0 cursor-pointer"
                 @click="openDetail(r)">
              <div class="w-10 h-10 rounded-full bg-base-300 flex items-center justify-center text-xl flex-shrink-0">🎁</div>
              <div class="flex-1">
                <p class="text-sm font-medium">
                  วันที่ {{ formatDate(r.created_at) }} แลก{{ r.reward_name }} {{ r.quantity }} ชิ้น
                </p>
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

    <!-- Redemption Detail Dialog -->
    <div v-if="selectedRedemption" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-3">รายละเอียดการแลกของรางวัล</h3>
        <div class="space-y-2 text-sm">
          <p><b>วันที่:</b> {{ formatDate(selectedRedemption.created_at) }}</p>
          <p><b>รายการ:</b> {{ selectedRedemption.reward_name }} {{ selectedRedemption.quantity }} ชิ้น</p>
          <p><b>สถานะ:</b> {{ statusLabel(selectedRedemption.status) }}</p>
          <div class="pt-2 border-t border-base-200">
            <p class="font-semibold mb-1">ที่อยู่จัดส่ง</p>
            <p>{{ selectedRedemption.shipping_name }} | {{ selectedRedemption.shipping_phone }}</p>
            <p>
              {{ selectedRedemption.shipping_address }}
              {{ selectedRedemption.shipping_subdistrict }}
              {{ selectedRedemption.shipping_district }}
              {{ selectedRedemption.shipping_province }}
              {{ selectedRedemption.shipping_postal_code }}
            </p>
          </div>
          <div class="pt-2 border-t border-base-200">
            <p><b>ขนส่ง:</b> {{ selectedRedemption.shipping_carrier || '-' }}</p>
            <p><b>เลข Tracking:</b> {{ selectedRedemption.tracking_number || '-' }}</p>
          </div>
        </div>
        <div class="modal-action">
          <button class="btn btn-primary" @click="selectedRedemption = null">ปิด</button>
        </div>
      </div>
      <div class="modal-backdrop" @click="selectedRedemption = null"></div>
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
const selectedRedemption = ref(null);

const user = computed(() => auth.user);
const redemptionsByMonth = computed(() => {
    var grouped = {};
    redemptions.value.forEach(function (r) {
        var d = new Date(r.created_at);
        var key = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(r);
    });

    return Object.keys(grouped)
        .sort(function (a, b) { return a < b ? 1 : -1; })
        .map(function (key) {
            var parts = key.split('-');
            var year = Number(parts[0]);
            var month = Number(parts[1]);
            var label = new Date(year, month - 1, 1).toLocaleDateString('th-TH', {
                month: 'long',
                year: 'numeric',
            });
            return {
                key: key,
                label: label,
                items: grouped[key],
            };
        });
});

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
    auth.logoutAndLoginWithLine();
    router.replace('/register');
}

function openDetail(redemption) {
    selectedRedemption.value = redemption;
}

function statusLabel(status) {
    return { pending: 'รอดำเนินการ', approved: 'อนุมัติแล้ว', rejected: 'ไม่อนุมัติ' }[status] || status;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

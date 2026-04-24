<template>
  <div class="pb-20">

    <!-- Banner (แทน header) -->
    <div v-if="banner" class="w-full">
      <img :src="banner.image_url" alt="" class="w-full object-cover max-h-48">
    </div>
    <div v-else class="bg-primary text-primary-content py-4 px-4">
      <h1 class="text-lg font-bold">แลกรางวัล</h1>
    </div>

    <!-- Points bar -->
    <div class="bg-base-200 px-4 py-2 flex items-center justify-between text-sm">
      <span class="text-gray-500">คะแนนของคุณ</span>
      <span class="font-bold text-primary text-base">{{ totalPoints.toLocaleString() }} คะแนน</span>
    </div>

    <!-- Rewards List -->
    <div class="px-4 mt-4">
      <p class="font-semibold text-base mb-3">รายการของรางวัล</p>
      <div v-if="loading" class="text-center py-8">
        <span class="loading loading-spinner loading-md"></span>
      </div>
      <div v-else-if="rewards.length === 0" class="text-center py-8 text-gray-400">
        <div class="text-4xl mb-2">🎁</div>
        <p>ยังไม่มีรางวัล</p>
      </div>
      <div v-else class="flex flex-col gap-3">
        <div v-for="reward in rewards" :key="reward.id"
             class="card card-side bg-base-200 shadow hover:shadow-md transition-shadow cursor-pointer"
             @click="openReward(reward)">
          <figure class="w-28 flex-shrink-0">
            <img v-if="reward.image_url" :src="reward.image_url" alt=""
                 class="w-28 h-28 object-cover rounded-l-2xl">
            <div v-else class="w-28 h-28 bg-base-300 flex items-center justify-center text-4xl rounded-l-2xl">🎁</div>
          </figure>
          <div class="card-body p-3 justify-center">
            <p class="font-semibold text-sm">{{ reward.name }}</p>
            <p v-if="reward.description" class="text-xs text-gray-400 line-clamp-2">{{ reward.description }}</p>
            <div class="flex items-center gap-2 mt-1 flex-wrap">
              <span class="text-primary font-bold text-sm">{{ reward.points_required.toLocaleString() }} คะแนน</span>
              <span class="text-xs text-gray-400">คงเหลือ {{ reward.stock }}</span>
              <div class="badge badge-sm"
                   :class="reward.stock > 0 && totalPoints >= reward.points_required ? 'badge-success' : 'badge-ghost'">
                {{ reward.stock > 0 ? (totalPoints >= reward.points_required ? 'แลกได้' : 'คะแนนไม่พอ') : 'หมด' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 1: Reward Confirm (แยกจากที่อยู่) -->
    <div v-if="selectedReward && redeemStep === 'reward'" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg text-center mb-4">ยืนยันการแลกสินค้า</h3>

        <!-- รูปสินค้า -->
        <div class="flex justify-center mb-4">
          <img v-if="selectedReward.image_url" :src="selectedReward.image_url"
               class="w-36 h-36 rounded-xl object-cover" alt="">
          <div v-else class="w-36 h-36 rounded-xl bg-base-300 flex items-center justify-center text-5xl">🎁</div>
        </div>

        <!-- ชื่อสินค้า -->
        <p class="font-semibold text-base text-center">{{ selectedReward.name }}</p>

        <!-- จำนวน -->
        <div class="flex items-center justify-between mt-4 px-2">
          <span class="text-sm text-gray-500">จำนวน</span>
          <span class="font-semibold">1 ชิ้น</span>
        </div>
        <div class="flex items-center justify-between mt-2 px-2">
          <span class="text-sm text-gray-500">คะแนนที่ใช้</span>
          <span class="font-bold text-primary">{{ selectedReward.points_required.toLocaleString() }} คะแนน</span>
        </div>

        <p v-if="redeemError" class="text-error text-sm mt-3 text-center">{{ redeemError }}</p>

        <div class="modal-action mt-4 gap-2">
          <button class="btn btn-outline flex-1" @click="closeRedeemFlow">ยกเลิก</button>
          <button class="btn btn-primary flex-1" @click="goToAddress"
                  :disabled="selectedReward.stock === 0 || totalPoints < selectedReward.points_required">
            ถัดไป
          </button>
        </div>
      </div>
      <div class="modal-backdrop" @click="closeRedeemFlow"></div>
    </div>

    <!-- Step 2: Address Form -->
    <div v-if="selectedReward && redeemStep === 'address'" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-3">ที่อยู่จัดส่ง</h3>

        <div class="p-3 rounded-xl bg-base-200">
          <label class="flex items-start gap-2 cursor-pointer">
            <input type="checkbox" class="checkbox checkbox-sm mt-1"
                   v-model="form.use_registered_address"
                   @change="onToggleRegisteredAddress">
            <span class="text-sm">ใช้ที่อยู่เดียวกับตอนลงทะเบียน</span>
          </label>
        </div>

        <div class="mt-3 space-y-2">
          <div class="form-control">
            <label class="label"><span class="label-text text-xs">ชื่อผู้รับ</span></label>
            <input v-model="form.shipping_name" type="text" class="input input-bordered input-sm"
                   :disabled="form.use_registered_address" required>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text text-xs">เบอร์โทรผู้รับ</span></label>
            <input v-model="form.shipping_phone" type="text" class="input input-bordered input-sm"
                   :disabled="form.use_registered_address" required>
          </div>
          <div class="form-control">
            <label class="label"><span class="label-text text-xs">ที่อยู่</span></label>
            <textarea v-model="form.shipping_address" rows="2" class="textarea textarea-bordered textarea-sm"
                      :disabled="form.use_registered_address" required></textarea>
          </div>
          <div class="space-y-2">
            <template v-if="form.use_registered_address">
              <div class="form-control">
                <label class="label"><span class="label-text">จังหวัด</span></label>
                <input type="text" class="input input-bordered input-sm" :value="form.shipping_province" disabled>
              </div>
              <div class="form-control">
                <label class="label"><span class="label-text">อำเภอ / เขต</span></label>
                <input type="text" class="input input-bordered input-sm" :value="form.shipping_district" disabled>
              </div>
              <div class="form-control">
                <label class="label"><span class="label-text">ตำบล / แขวง</span></label>
                <input type="text" class="input input-bordered input-sm" :value="form.shipping_subdistrict" disabled>
              </div>
            </template>
            <template v-else>
              <AddressTypeahead
                v-model="shippingProvinceId"
                :options="shippingProvinceOptions"
                label="จังหวัด"
                placeholder="พิมพ์ชื่อจังหวัด..."
                :required="true"
                @select="onShippingProvinceSelect"
              />

              <AddressTypeahead
                v-model="shippingDistrictId"
                :options="shippingDistrictOptions"
                label="อำเภอ / เขต"
                placeholder="พิมพ์ชื่ออำเภอ..."
                :required="true"
                :disabled="!shippingProvinceId"
                @select="onShippingDistrictSelect"
              />

              <AddressTypeahead
                v-model="shippingSubdistrictId"
                :options="shippingSubdistrictOptions"
                label="ตำบล / แขวง"
                placeholder="พิมพ์ชื่อตำบล..."
                :required="true"
                :disabled="!shippingDistrictId"
                @select="onShippingSubdistrictSelect"
              />
            </template>

            <div class="form-control">
              <label class="label"><span class="label-text text-xs">รหัสไปรษณีย์</span></label>
              <div v-if="form.shipping_postal_code" class="flex items-center gap-2">
                <div class="badge badge-primary badge-lg font-bold px-4">{{ form.shipping_postal_code }}</div>
                <span class="text-xs text-gray-500">(อัพเดทอัตโนมัติ)</span>
              </div>
              <div v-else class="text-sm text-gray-400 py-2">จะแสดงเมื่อเลือกตำบล</div>
            </div>
          </div>
        </div>

        <p v-if="redeemError" class="text-error text-sm mt-3">{{ redeemError }}</p>

        <div class="modal-action mt-4 gap-2">
          <button class="btn btn-outline flex-1" @click="redeemStep = 'reward'">ย้อนกลับ</button>
          <button class="btn btn-primary flex-1" @click="goToSummary"
                  :disabled="selectedReward.stock === 0 || totalPoints < selectedReward.points_required">
            ถัดไป
          </button>
        </div>
      </div>
      <div class="modal-backdrop" @click="closeRedeemFlow"></div>
    </div>

    <!-- Step 2: Summary -->
    <div v-if="selectedReward && redeemStep === 'summary'" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-3">สรุปที่อยู่จัดส่ง</h3>
        <div class="text-sm space-y-1">
          <p><b>สินค้า:</b> {{ selectedReward.name }}</p>
          <p><b>ผู้รับ:</b> {{ form.shipping_name }}</p>
          <p><b>เบอร์โทร:</b> {{ form.shipping_phone }}</p>
          <p><b>ที่อยู่:</b> {{ fullAddressText }}</p>
        </div>
        <div class="modal-action gap-2">
          <button class="btn btn-outline flex-1" @click="redeemStep = 'address'">แก้ไข</button>
          <button class="btn btn-primary flex-1" @click="redeemStep = 'notice'">ยืนยัน</button>
        </div>
      </div>
      <div class="modal-backdrop" @click="closeRedeemFlow"></div>
    </div>

    <!-- Step 3: Notice -->
    <div v-if="selectedReward && redeemStep === 'notice'" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-2">ยืนยันอีกครั้ง</h3>
        <p class="text-sm text-gray-600">หากต้องการเปลี่ยนแปลงข้อมูลจัดส่ง กรุณาติดต่อ Admin</p>
        <div class="modal-action gap-2">
          <button class="btn btn-outline flex-1" @click="redeemStep = 'summary'">ย้อนกลับ</button>
          <button class="btn btn-primary flex-1" @click="redeemStep = 'final'">ยืนยัน</button>
        </div>
      </div>
      <div class="modal-backdrop" @click="closeRedeemFlow"></div>
    </div>

    <!-- Step 4: Final Confirm -->
    <div v-if="selectedReward && redeemStep === 'final'" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-3">ตรวจสอบก่อนยืนยัน</h3>
        <div class="text-sm space-y-1">
          <p><b>สินค้า:</b> {{ selectedReward.name }}</p>
          <p><b>คะแนนที่ใช้:</b> {{ selectedReward.points_required.toLocaleString() }} คะแนน</p>
          <p><b>ผู้รับ:</b> {{ form.shipping_name }}</p>
          <p><b>ที่อยู่:</b> {{ fullAddressText }}</p>
        </div>
        <p v-if="redeemError" class="text-error text-sm mt-3">{{ redeemError }}</p>
        <div class="modal-action gap-2">
          <button class="btn btn-outline flex-1" @click="closeRedeemFlow">ยกเลิก</button>
          <button class="btn btn-primary flex-1" @click="confirmRedeem" :disabled="redeeming">
            <span v-if="redeeming" class="loading loading-spinner loading-xs"></span>
            ยืนยัน
          </button>
        </div>
      </div>
      <div class="modal-backdrop" @click="closeRedeemFlow"></div>
    </div>

    <!-- Success Dialog -->
    <div v-if="successDialog.open" class="modal modal-open">
      <div class="modal-box text-center">
        <div class="text-5xl mb-2">✅</div>
        <h3 class="font-bold text-lg">แลกสำเร็จ</h3>
        <p class="text-sm text-gray-600 mt-2">
          ระบบได้รับคำขอแลกรางวัลเรียบร้อยแล้ว
        </p>
        <div class="modal-action justify-center">
          <button class="btn btn-primary px-8" @click="successDialog.open = false">ตกลง</button>
        </div>
      </div>
      <div class="modal-backdrop" @click="successDialog.open = false"></div>
    </div>
  </div>
  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import BottomNav from '@/components/BottomNav.vue';
import AddressTypeahead from '@/components/AddressTypeahead.vue';
import api from '@/composables/useApi';

const authStore = useAuthStore();
const loading = ref(true);
const rewards = ref([]);
const banner = ref(null);
const selectedReward = ref(null);
const redeemError = ref('');
const redeeming = ref(false);
const redeemStep = ref('reward');
const successDialog = ref({
    open: false,
});

const form = ref({
    use_registered_address: true,
    shipping_name: '',
    shipping_phone: '',
    shipping_address: '',
    shipping_province: '',
    shipping_district: '',
    shipping_subdistrict: '',
    shipping_postal_code: '',
});
const shippingProvinceId = ref(null);
const shippingDistrictId = ref(null);
const shippingSubdistrictId = ref(null);
const shippingProvinceOptions = ref([]);
const shippingDistrictOptions = ref([]);
const shippingSubdistrictOptions = ref([]);

const totalPoints = computed(() => authStore.user ? (authStore.user.total_points || 0) : 0);
const fullAddressText = computed(() => {
    return [
        form.value.shipping_address,
        form.value.shipping_subdistrict,
        form.value.shipping_district,
        form.value.shipping_province,
        form.value.shipping_postal_code,
    ].filter(Boolean).join(' ');
});

onMounted(async () => {
    try {
        const [rRes, bRes, provinceRes] = await Promise.all([
            api.get('/api/liff/rewards'),
            api.get('/api/liff/banner/reward'),
            api.get('/api/liff/provinces'),
        ]);
        rewards.value = rRes.data;
        banner.value = bRes.data;
        shippingProvinceOptions.value = provinceRes.data.map(p => ({ id: p.id, name: p.name_th }));
    } catch (e) {
        // ignore
    } finally {
        loading.value = false;
    }
});

function openReward(reward) {
    selectedReward.value = reward;
    redeemStep.value = 'reward';
    redeemError.value = '';
    resetForm();
}

function closeRedeemFlow() {
    selectedReward.value = null;
    redeemStep.value = 'reward';
    redeemError.value = '';
}

function goToAddress() {
    redeemError.value = '';
    redeemStep.value = 'address';
}

function onToggleRegisteredAddress() {
    if (form.value.use_registered_address) {
        applyRegisteredAddress();
        return;
    }
    clearManualAddress();
}

function resetForm() {
    form.value = {
        use_registered_address: true,
        shipping_name: '',
        shipping_phone: '',
        shipping_address: '',
        shipping_province: '',
        shipping_district: '',
        shipping_subdistrict: '',
        shipping_postal_code: '',
    };
    applyRegisteredAddress();
    shippingProvinceId.value = null;
    shippingDistrictId.value = null;
    shippingSubdistrictId.value = null;
    shippingDistrictOptions.value = [];
    shippingSubdistrictOptions.value = [];
}

function applyRegisteredAddress() {
    const address = authStore.user && authStore.user.address ? authStore.user.address : null;
    if (!address) {
        form.value.use_registered_address = false;
        redeemError.value = 'ไม่พบที่อยู่ลงทะเบียน กรุณากรอกที่อยู่จัดส่ง';
        return;
    }

    form.value.shipping_name = address.name || '';
    form.value.shipping_phone = address.phone || '';
    form.value.shipping_address = address.address_line || '';
    form.value.shipping_province = address.province_name || '';
    form.value.shipping_district = address.district_name || '';
    form.value.shipping_subdistrict = address.subdistrict_name || '';
    form.value.shipping_postal_code = address.zipcode || '';
    shippingProvinceId.value = null;
    shippingDistrictId.value = null;
    shippingSubdistrictId.value = null;
    shippingDistrictOptions.value = [];
    shippingSubdistrictOptions.value = [];
}

function clearManualAddress() {
    form.value.shipping_name = '';
    form.value.shipping_phone = '';
    form.value.shipping_address = '';
    form.value.shipping_province = '';
    form.value.shipping_district = '';
    form.value.shipping_subdistrict = '';
    form.value.shipping_postal_code = '';
    shippingProvinceId.value = null;
    shippingDistrictId.value = null;
    shippingSubdistrictId.value = null;
    shippingDistrictOptions.value = [];
    shippingSubdistrictOptions.value = [];
}

async function onShippingProvinceSelect(opt) {
    form.value.shipping_province = opt ? opt.name : '';
    shippingDistrictId.value = null;
    shippingSubdistrictId.value = null;
    form.value.shipping_district = '';
    form.value.shipping_subdistrict = '';
    form.value.shipping_postal_code = '';
    shippingSubdistrictOptions.value = [];
    if (!opt) {
        shippingDistrictOptions.value = [];
        return;
    }
    const { data } = await api.get(`/api/liff/districts/${opt.id}`);
    shippingDistrictOptions.value = data.map(d => ({ id: d.id, name: d.name_th }));
}

async function onShippingDistrictSelect(opt) {
    form.value.shipping_district = opt ? opt.name : '';
    shippingSubdistrictId.value = null;
    form.value.shipping_subdistrict = '';
    form.value.shipping_postal_code = '';
    if (!opt) {
        shippingSubdistrictOptions.value = [];
        return;
    }
    const { data } = await api.get(`/api/liff/subdistricts/${opt.id}`);
    shippingSubdistrictOptions.value = data.map(t => ({
        id: t.id,
        name: t.name_th,
        zip: t.postal_code,
    }));
}

function onShippingSubdistrictSelect(opt) {
    form.value.shipping_subdistrict = opt ? opt.name : '';
    form.value.shipping_postal_code = opt && opt.zip ? String(opt.zip) : '';
}

function validateAddressForm() {
    redeemError.value = '';
    const requiredFields = [
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_province',
        'shipping_district',
        'shipping_subdistrict',
        'shipping_postal_code',
    ];

    for (const key of requiredFields) {
        if (!form.value[key]) {
            redeemError.value = 'กรุณากรอกข้อมูลจัดส่งให้ครบทุกช่อง';
            return false;
        }
    }

    return true;
}

function goToSummary() {
    if (!validateAddressForm()) return;
    redeemStep.value = 'summary';
}

async function confirmRedeem() {
    redeeming.value = true;
    redeemError.value = '';
    try {
        await api.post('/api/liff/redeem', {
            reward_id: selectedReward.value.id,
            use_registered_address: form.value.use_registered_address ? 1 : 0,
            shipping_name: form.value.shipping_name,
            shipping_phone: form.value.shipping_phone,
            shipping_address: form.value.shipping_address,
            shipping_province: form.value.shipping_province,
            shipping_district: form.value.shipping_district,
            shipping_subdistrict: form.value.shipping_subdistrict,
            shipping_postal_code: form.value.shipping_postal_code,
        });
        await authStore.fetchUser();
        closeRedeemFlow();
        successDialog.value.open = true;
    } catch (e) {
        redeemError.value = e.response && e.response.data && e.response.data.message
            ? e.response.data.message : 'เกิดข้อผิดพลาด';
    } finally {
        redeeming.value = false;
    }
}
</script>

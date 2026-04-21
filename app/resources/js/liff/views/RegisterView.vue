<template>
  <div class="min-h-screen bg-base-100 pb-8">
    <!-- Header -->
    <div class="bg-primary text-primary-content py-6 px-4 text-center">
      <img src="/images/eucerin-logo.png" alt="Eucerin" class="h-10 mx-auto mb-2" onerror="this.style.display='none'">
      <h1 class="text-xl font-bold">ลงทะเบียน BA Eucerin</h1>
      <p class="text-sm opacity-80 mt-1">BT Eucerin CRM</p>
    </div>

    <!-- Steps -->
    <div class="px-4 py-3">
      <ul class="steps steps-horizontal w-full text-xs">
        <li class="step" :class="step >= 1 ? 'step-primary' : ''">ข้อมูล</li>
        <li class="step" :class="step >= 2 ? 'step-primary' : ''">ที่อยู่</li>
        <li class="step" :class="step >= 3 ? 'step-primary' : ''">สาขา</li>
        <li class="step" :class="step >= 4 ? 'step-primary' : ''">เสร็จสิ้น</li>
      </ul>
    </div>

    <!-- Step 1: Personal Info -->
    <div v-if="step === 1" class="px-4">
      <div class="card bg-base-200 shadow">
        <div class="card-body">
          <h2 class="card-title text-base mb-3">ข้อมูลส่วนตัว</h2>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">ชื่อ <span class="text-error">*</span></span></label>
            <input v-model="form.first_name" type="text" class="input input-bordered" placeholder="ชื่อ" required>
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">นามสกุล <span class="text-error">*</span></span></label>
            <input v-model="form.last_name" type="text" class="input input-bordered" placeholder="นามสกุล" required>
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">เบอร์โทรศัพท์ <span class="text-error">*</span></span></label>
            <input v-model="form.phone" type="tel" class="input input-bordered" placeholder="0812345678" maxlength="10">
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">รหัสพนักงาน</span></label>
            <input v-model="form.employee_code" type="text" class="input input-bordered" placeholder="รหัสพนักงาน (ถ้ามี)">
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">วันเกิด</span></label>
            <input v-model="form.birthday" type="date" class="input input-bordered">
          </div>
          <p v-if="errors.step1" class="text-error text-sm mt-1">{{ errors.step1 }}</p>
          <div class="card-actions justify-end mt-2">
            <button class="btn btn-primary w-full" @click="nextStep(1)">ถัดไป →</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 2: Address -->
    <div v-if="step === 2" class="px-4">
      <div class="card bg-base-200 shadow">
        <div class="card-body">
          <h2 class="card-title text-base mb-3">ที่อยู่จัดส่งรางวัล</h2>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">ที่อยู่ <span class="text-error">*</span></span></label>
            <input v-model="form.address_line" type="text" class="input input-bordered" placeholder="บ้านเลขที่ ถนน">
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">จังหวัด <span class="text-error">*</span></span></label>
            <select v-model="form.province_id" class="select select-bordered" @change="loadDistricts">
              <option value="">-- เลือกจังหวัด --</option>
              <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name_th }}</option>
            </select>
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">อำเภอ/เขต <span class="text-error">*</span></span></label>
            <select v-model="form.district_id" class="select select-bordered" @change="loadSubdistricts">
              <option value="">-- เลือกอำเภอ --</option>
              <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name_th }}</option>
            </select>
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">ตำบล/แขวง <span class="text-error">*</span></span></label>
            <select v-model="form.subdistrict_id" class="select select-bordered" @change="setZipcode">
              <option value="">-- เลือกตำบล --</option>
              <option v-for="s in subdistricts" :key="s.id" :value="s.id">{{ s.name_th }}</option>
            </select>
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">รหัสไปรษณีย์</span></label>
            <input v-model="form.zipcode" type="text" class="input input-bordered" readonly>
          </div>
          <p v-if="errors.step2" class="text-error text-sm mt-1">{{ errors.step2 }}</p>
          <div class="flex gap-2 mt-2">
            <button class="btn btn-ghost flex-1" @click="step = 1">← ย้อนกลับ</button>
            <button class="btn btn-primary flex-1" @click="nextStep(2)">ถัดไป →</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 3: Branch -->
    <div v-if="step === 3" class="px-4">
      <div class="card bg-base-200 shadow">
        <div class="card-body">
          <h2 class="card-title text-base mb-3">สาขาที่ปฏิบัติงาน</h2>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">ค้นหาสาขา</span></label>
            <input v-model="branchSearch" type="text" class="input input-bordered" placeholder="ชื่อสาขา...">
          </div>
          <div class="overflow-y-auto max-h-64">
            <div v-for="branch in filteredBranches" :key="branch.id"
                 class="flex items-center gap-3 p-3 border rounded-lg mb-2 cursor-pointer hover:bg-base-300 transition-colors"
                 :class="form.branch_id === branch.id ? 'border-primary bg-primary/10' : 'border-base-300'"
                 @click="form.branch_id = branch.id">
              <div class="flex-1">
                <p class="font-medium text-sm">{{ branch.name }}</p>
                <p class="text-xs text-gray-500">{{ branch.zone }}</p>
              </div>
              <div v-if="form.branch_id === branch.id" class="text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
          <p v-if="errors.step3" class="text-error text-sm mt-1">{{ errors.step3 }}</p>
          <div class="flex gap-2 mt-2">
            <button class="btn btn-ghost flex-1" @click="step = 2">← ย้อนกลับ</button>
            <button class="btn btn-primary flex-1" @click="submitRegister" :disabled="loading">
              <span v-if="loading" class="loading loading-spinner loading-xs"></span>
              ลงทะเบียน
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 4: Complete -->
    <div v-if="step === 4" class="px-4 text-center py-12">
      <div class="text-6xl mb-4">🎉</div>
      <h2 class="text-xl font-bold mb-2">ลงทะเบียนสำเร็จ!</h2>
      <p class="text-gray-500 mb-6">ยินดีต้อนรับสู่ BT Eucerin CRM</p>
      <router-link to="/liff" class="btn btn-primary btn-wide">เริ่มต้นใช้งาน</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../composables/useApi';

const router = useRouter();
const authStore = useAuthStore();

const step = ref(1);
const loading = ref(false);
const errors = ref({});
const branchSearch = ref('');

const form = ref({
    first_name: '',
    last_name: '',
    phone: '',
    employee_code: '',
    birthday: '',
    address_line: '',
    province_id: '',
    district_id: '',
    subdistrict_id: '',
    zipcode: '',
    branch_id: null,
});

const provinces = ref([]);
const districts = ref([]);
const subdistricts = ref([]);
const branches = ref([]);

const filteredBranches = computed(() => {
    if (!branchSearch.value) return branches.value;
    return branches.value.filter(b =>
        b.name.toLowerCase().includes(branchSearch.value.toLowerCase())
    );
});

onMounted(async () => {
    const [pRes, bRes] = await Promise.all([
        api.get('/api/liff/provinces'),
        api.get('/api/liff/branches'),
    ]);
    provinces.value = pRes.data;
    branches.value = bRes.data;

    if (authStore.lineProfile) {
        form.value.first_name = authStore.lineProfile.displayName.split(' ')[0] || '';
        form.value.last_name = authStore.lineProfile.displayName.split(' ').slice(1).join(' ') || '';
    }
});

async function loadDistricts() {
    form.value.district_id = '';
    form.value.subdistrict_id = '';
    form.value.zipcode = '';
    if (!form.value.province_id) { districts.value = []; return; }
    const { data } = await api.get('/api/liff/districts/' + form.value.province_id);
    districts.value = data;
}

async function loadSubdistricts() {
    form.value.subdistrict_id = '';
    form.value.zipcode = '';
    if (!form.value.district_id) { subdistricts.value = []; return; }
    const { data } = await api.get('/api/liff/subdistricts/' + form.value.district_id);
    subdistricts.value = data;
}

function setZipcode() {
    const sub = subdistricts.value.find(s => s.id === form.value.subdistrict_id);
    if (sub) form.value.zipcode = sub.zipcode;
}

function nextStep(current) {
    errors.value = {};
    if (current === 1) {
        if (!form.value.first_name || !form.value.last_name || !form.value.phone) {
            errors.value.step1 = 'กรุณากรอกชื่อ นามสกุล และเบอร์โทรศัพท์';
            return;
        }
        if (!/^0\d{9}$/.test(form.value.phone)) {
            errors.value.step1 = 'เบอร์โทรศัพท์ไม่ถูกต้อง (ต้องเป็น 10 หลัก)';
            return;
        }
    }
    if (current === 2) {
        if (!form.value.address_line || !form.value.province_id || !form.value.district_id || !form.value.subdistrict_id) {
            errors.value.step2 = 'กรุณากรอกข้อมูลที่อยู่ให้ครบ';
            return;
        }
    }
    step.value = current + 1;
}

async function submitRegister() {
    if (!form.value.branch_id) {
        errors.value.step3 = 'กรุณาเลือกสาขา';
        return;
    }
    loading.value = true;
    errors.value = {};
    try {
        const lineProfile = authStore.lineProfile;
        const { data } = await api.post('/api/liff/register', {
            ...form.value,
            line_uid: lineProfile ? lineProfile.userId : '',
            line_display_name: lineProfile ? lineProfile.displayName : '',
            line_picture_url: lineProfile ? lineProfile.pictureUrl : '',
        });
        authStore.setToken(data.token);
        authStore.user = data.user;
        step.value = 4;
    } catch (e) {
        if (e.response && e.response.data && e.response.data.errors) {
            const serverErrors = e.response.data.errors;
            const firstKey = Object.keys(serverErrors)[0];
            errors.value.step3 = serverErrors[firstKey][0];
        } else {
            errors.value.step3 = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
        }
    } finally {
        loading.value = false;
    }
}
</script>

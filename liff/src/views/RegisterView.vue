<template>
  <div class="min-h-screen bg-base-100 pb-8">
    <!-- Header -->
    <div class="bg-primary text-primary-content py-6 px-4 text-center">
      <p class="font-bold text-lg tracking-widest">EUCERIN</p>
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
            <input v-model="form.first_name" type="text" class="input input-bordered" placeholder="ชื่อ">
          </div>
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">นามสกุล <span class="text-error">*</span></span></label>
            <input v-model="form.last_name" type="text" class="input input-bordered" placeholder="นามสกุล">
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

    <!-- Step 2: Address (Typeahead) -->
    <div v-if="step === 2" class="px-4">
      <div class="card bg-base-200 shadow">
        <div class="card-body">
          <h2 class="card-title text-base mb-3">ที่อยู่จัดส่งรางวัล</h2>

          <div class="form-control mb-3">
            <label class="label"><span class="label-text">ที่อยู่ <span class="text-error">*</span></span></label>
            <input v-model="form.address_line" type="text" class="input input-bordered" placeholder="บ้านเลขที่ ถนน หมู่บ้าน">
          </div>

          <!-- จังหวัด typeahead -->
          <div class="mb-3">
            <AddressTypeahead
              v-model="form.province_id"
              :options="provinceOptions"
              label="จังหวัด"
              placeholder="พิมพ์ชื่อจังหวัด..."
              :required="true"
              :error="errors.province"
              @select="onProvinceSelect"
            />
          </div>

          <!-- อำเภอ typeahead -->
          <div class="mb-3">
            <AddressTypeahead
              v-model="form.district_id"
              :options="amphureOptions"
              label="อำเภอ / เขต"
              placeholder="พิมพ์ชื่ออำเภอ..."
              :required="true"
              :disabled="!form.province_id"
              :error="errors.district"
              @select="onAmphureSelect"
            />
            <p v-if="!form.province_id" class="text-xs text-gray-400 mt-1">เลือกจังหวัดก่อน</p>
          </div>

          <!-- ตำบล typeahead -->
          <div class="mb-3">
            <AddressTypeahead
              v-model="form.subdistrict_id"
              :options="tambonOptions"
              label="ตำบล / แขวง"
              placeholder="พิมพ์ชื่อตำบล..."
              :required="true"
              :disabled="!form.district_id"
              :error="errors.subdistrict"
              @select="onTambonSelect"
            />
            <p v-if="!form.district_id" class="text-xs text-gray-400 mt-1">เลือกอำเภอก่อน</p>
          </div>

          <!-- รหัสไปรษณีย์ — auto fill -->
          <div class="form-control mb-3">
            <label class="label"><span class="label-text">รหัสไปรษณีย์</span></label>
            <div v-if="form.zipcode" class="flex items-center gap-2">
              <div class="badge badge-primary badge-lg font-bold px-4">{{ form.zipcode }}</div>
              <span class="text-xs text-gray-500">(อัพเดทอัตโนมัติ)</span>
            </div>
            <div v-else class="text-sm text-gray-400 py-2">จะแสดงเมื่อเลือกตำบล</div>
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
                 class="flex items-center gap-3 p-3 border rounded-lg mb-2 cursor-pointer transition-colors"
                 :class="form.branch_id === branch.id ? 'border-primary bg-primary/10' : 'border-base-300'"
                 @click="form.branch_id = branch.id">
              <div class="flex-1">
                <p class="font-medium text-sm">{{ branch.name }}</p>
                <p class="text-xs text-gray-500">{{ branch.zone }}</p>
              </div>
              <div v-if="form.branch_id === branch.id" class="text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
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
      <router-link to="/home" class="btn btn-primary btn-wide">เริ่มต้นใช้งาน</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useThaiAddress } from '@/composables/useThaiAddress';
import AddressTypeahead from '@/components/AddressTypeahead.vue';
import api from '@/composables/useApi';

const router    = useRouter();
const authStore = useAuthStore();
const thai      = useThaiAddress();

const step    = ref(1);
const loading = ref(false);
const errors  = ref({});

const branchSearch  = ref('');
const branches      = ref([]);

// Address state
const provinceOptions  = ref([]);
const amphureOptions   = ref([]);
const tambonOptions    = ref([]);

const form = ref({
    first_name: '',
    last_name: '',
    phone: '',
    employee_code: '',
    birthday: '',
    address_line: '',
    province_id: null,
    province_name: '',
    district_id: null,
    district_name: '',
    subdistrict_id: null,
    subdistrict_name: '',
    zipcode: '',
    branch_id: null,
});

const filteredBranches = computed(() => {
    if (!branchSearch.value) return branches.value;
    return branches.value.filter(b =>
        b.name.toLowerCase().includes(branchSearch.value.toLowerCase())
    );
});

onMounted(async () => {
    provinceOptions.value = thai.getProvinces();

    // โหลด branches จาก API
    try {
        const { data } = await api.get('/api/liff/branches');
        branches.value = data;
    } catch (e) { /* ignore */ }

    // Pre-fill ชื่อจาก LINE profile
    if (authStore.lineProfile) {
        const parts = (authStore.lineProfile.displayName || '').split(' ');
        form.value.first_name = parts[0] || '';
        form.value.last_name  = parts.slice(1).join(' ') || '';
    }
});

// ---- Province ----
watch(() => form.value.province_id, (id) => {
    if (!id) {
        amphureOptions.value      = [];
        tambonOptions.value       = [];
        form.value.district_id    = null;
        form.value.subdistrict_id = null;
        form.value.zipcode        = '';
    }
});

function onProvinceSelect(opt) {
    if (!opt) { amphureOptions.value = []; return; }
    form.value.province_name    = opt.name;
    amphureOptions.value        = thai.getAmphuresByProvince(opt.id);
    form.value.district_id      = null;
    form.value.district_name    = '';
    form.value.subdistrict_id   = null;
    form.value.subdistrict_name = '';
    form.value.zipcode          = '';
    tambonOptions.value         = [];
}

// ---- Amphure ----
watch(() => form.value.district_id, (id) => {
    if (!id) {
        tambonOptions.value       = [];
        form.value.subdistrict_id = null;
        form.value.zipcode        = '';
    }
});

function onAmphureSelect(opt) {
    if (!opt) { tambonOptions.value = []; return; }
    form.value.district_name    = opt.name;
    tambonOptions.value         = thai.getTambonsByAmphure(opt.id);
    form.value.subdistrict_id   = null;
    form.value.subdistrict_name = '';
    form.value.zipcode          = '';
}

// ---- Tambon ----
function onTambonSelect(opt) {
    if (!opt) { form.value.zipcode = ''; return; }
    form.value.subdistrict_name = opt.name;
    form.value.zipcode          = opt.zip ? String(opt.zip) : '';
}

// ---- Validation ----
function nextStep(current) {
    errors.value = {};
    if (current === 1) {
        if (!form.value.first_name || !form.value.last_name) {
            errors.value.step1 = 'กรุณากรอกชื่อและนามสกุล'; return;
        }
        if (!/^0\d{9}$/.test(form.value.phone)) {
            errors.value.step1 = 'เบอร์โทรศัพท์ไม่ถูกต้อง (10 หลัก เริ่มต้นด้วย 0)'; return;
        }
    }
    if (current === 2) {
        if (!form.value.address_line) {
            errors.value.step2 = 'กรุณากรอกที่อยู่'; return;
        }
        if (!form.value.province_id) {
            errors.value.step2 = 'กรุณาเลือกจังหวัด'; return;
        }
        if (!form.value.district_id) {
            errors.value.step2 = 'กรุณาเลือกอำเภอ'; return;
        }
        if (!form.value.subdistrict_id) {
            errors.value.step2 = 'กรุณาเลือกตำบล'; return;
        }
    }
    step.value = current + 1;
}

async function submitRegister() {
    errors.value = {};
    if (!form.value.branch_id) {
        errors.value.step3 = 'กรุณาเลือกสาขา'; return;
    }
    loading.value = true;
    try {
        const lineProfile = authStore.lineProfile;
        const { data } = await api.post('/api/liff/register', {
            line_uid:           lineProfile ? lineProfile.userId : '',
            line_display_name:  lineProfile ? lineProfile.displayName : '',
            line_picture_url:   lineProfile ? lineProfile.pictureUrl : '',
            first_name:         form.value.first_name,
            last_name:          form.value.last_name,
            phone:              form.value.phone,
            employee_code:      form.value.employee_code,
            birthday:           form.value.birthday,
            address_line:       form.value.address_line,
            province_name:      form.value.province_name,
            district_name:      form.value.district_name,
            subdistrict_name:   form.value.subdistrict_name,
            zipcode:            form.value.zipcode,
            branch_id:          form.value.branch_id,
        });
        authStore.setToken(data.token);
        authStore.user = data.user;
        step.value = 4;
    } catch (e) {
        const resp = e.response && e.response.data;
        if (resp && resp.errors) {
            const fieldErrors = Object.entries(resp.errors)
                .map(([k, v]) => `${k}: ${Array.isArray(v) ? v[0] : v}`)
                .join(' | ');
            errors.value.step3 = fieldErrors;
        } else {
            errors.value.step3 = (resp && resp.message) || 'เกิดข้อผิดพลาด กรุณาลองใหม่';
        }
    } finally {
        loading.value = false;
    }
}
</script>

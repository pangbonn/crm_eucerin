<template>
  <div>
    <label v-if="label" class="label">
      <span class="label-text">{{ label }}</span>
    </label>
    <!-- Trigger -->
    <div
      class="input input-bordered flex items-center cursor-pointer select-none"
      @click="openSheet"
    >
      <span :class="displayValue ? 'text-base-content' : 'text-base-content/30'">
        {{ displayValue || 'เลือกวันเกิด' }}
      </span>
      <svg class="ml-auto h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
    </div>
  </div>

  <!-- Bottom Sheet -->
  <Teleport to="body">
    <Transition name="sheet">
      <div v-if="open" class="fixed inset-0 z-[999] flex flex-col justify-end">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" @click="cancel" />

        <!-- Sheet -->
        <div class="relative bg-base-100 rounded-t-2xl shadow-xl">
          <!-- Toolbar -->
          <div class="flex justify-between items-center px-5 py-3 border-b border-base-300">
            <button class="text-gray-400 font-medium py-1 px-2" @click="cancel">ยกเลิก</button>
            <span class="font-semibold text-sm">วันเกิด</span>
            <button class="text-primary font-semibold py-1 px-2" @click="confirm">ยืนยัน</button>
          </div>

          <!-- Wheels -->
          <div class="relative flex h-52 overflow-hidden px-2">
            <!-- Highlight band (z-1 อยู่หลัง text) -->
            <div class="pointer-events-none absolute inset-x-2 top-1/2 -translate-y-1/2 h-10 rounded-lg z-[1]" style="background:#fff; border:1.5px solid #C00000;" />

            <!-- Day (z-[5] อยู่หน้า highlight) -->
            <div
              ref="dayCol"
              class="relative z-[5] flex-1 overflow-y-scroll snap-y snap-mandatory scrollbar-hide"
              style="padding-top: 96px; padding-bottom: 96px;"
              @scroll="onScroll('day', $event)"
            >
              <div
                v-for="d in days" :key="d"
                class="snap-center h-10 flex items-center justify-center text-base"
                :style="d === tempDay ? 'color:#C00000;font-weight:700;font-size:1.05rem' : 'color:rgba(0,0,0,0.25)'"
              >{{ d }}</div>
            </div>

            <!-- Month -->
            <div
              ref="monthCol"
              class="relative z-[5] flex-[2] overflow-y-scroll snap-y snap-mandatory scrollbar-hide"
              style="padding-top: 96px; padding-bottom: 96px;"
              @scroll="onScroll('month', $event)"
            >
              <div
                v-for="(m, i) in months" :key="i"
                class="snap-center h-10 flex items-center justify-center text-base"
                :style="(i + 1) === tempMonth ? 'color:#C00000;font-weight:700;font-size:1.05rem' : 'color:rgba(0,0,0,0.25)'"
              >{{ m }}</div>
            </div>

            <!-- Year -->
            <div
              ref="yearCol"
              class="relative z-[5] flex-1 overflow-y-scroll snap-y snap-mandatory scrollbar-hide"
              style="padding-top: 96px; padding-bottom: 96px;"
              @scroll="onScroll('year', $event)"
            >
              <div
                v-for="y in years" :key="y"
                class="snap-center h-10 flex items-center justify-center text-base"
                :style="y === tempYear ? 'color:#C00000;font-weight:700;font-size:1.05rem' : 'color:rgba(0,0,0,0.25)'"
              >{{ y }}</div>
            </div>

            <!-- Fade top (z-[10] อยู่หน้า text เพื่อ fade ขอบ) -->
            <div class="pointer-events-none absolute inset-x-0 top-0 h-16 z-[10]" style="background:linear-gradient(to bottom, #fff 0%, transparent 100%);" />
            <!-- Fade bottom -->
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 z-[10]" style="background:linear-gradient(to top, #fff 0%, transparent 100%);" />
          </div>

          <!-- Safe area bottom -->
          <div class="h-6" />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    label:      { type: String, default: '' },
    minYear:    { type: Number, default: 1960 },
    maxYear:    { type: Number, default: new Date().getFullYear() - 15 },
});
const emits = defineEmits(['update:modelValue']);

const open = ref(false);

const now = new Date();
const tempDay   = ref(now.getDate());
const tempMonth = ref(now.getMonth() + 1);
const tempYear  = ref(props.maxYear);

const dayCol   = ref(null);
const monthCol = ref(null);
const yearCol  = ref(null);

const months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.',
                 'ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

const days = computed(() => {
    const max = new Date(tempYear.value, tempMonth.value, 0).getDate();
    return Array.from({ length: max }, (_, i) => i + 1);
});

const years = computed(() => {
    const arr = [];
    for (let y = props.maxYear; y >= props.minYear; y--) arr.push(y);
    return arr;
});

const displayValue = computed(() => {
    if (!props.modelValue) return '';
    const [y, m, d] = props.modelValue.split('-');
    if (!y || !m || !d) return '';
    return `${parseInt(d)} ${months[parseInt(m) - 1]} ${parseInt(y) + 543}`;
});

function scrollTo(colRef, index) {
    if (colRef.value) {
        colRef.value.scrollTop = index * 40;
    }
}

function openSheet() {
    if (props.modelValue) {
        const [y, m, d] = props.modelValue.split('-');
        tempDay.value   = parseInt(d) || now.getDate();
        tempMonth.value = parseInt(m) || now.getMonth() + 1;
        tempYear.value  = parseInt(y) || props.maxYear;
    }
    open.value = true;
    nextTick(() => {
        scrollTo(dayCol,   tempDay.value - 1);
        scrollTo(monthCol, tempMonth.value - 1);
        scrollTo(yearCol,  years.value.indexOf(tempYear.value));
    });
}

let scrollTimers = {};
function onScroll(col, e) {
    clearTimeout(scrollTimers[col]);
    scrollTimers[col] = setTimeout(() => {
        const index = Math.round(e.target.scrollTop / 40);
        if (col === 'day')   tempDay.value   = days.value[index]   ?? days.value[0];
        if (col === 'month') tempMonth.value = index + 1;
        if (col === 'year')  tempYear.value  = years.value[index]  ?? years.value[0];
    }, 80);
}

function confirm() {
    const d = String(tempDay.value).padStart(2, '0');
    const m = String(tempMonth.value).padStart(2, '0');
    emits('update:modelValue', `${tempYear.value}-${m}-${d}`);
    open.value = false;
}

function cancel() {
    open.value = false;
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

.sheet-enter-active,
.sheet-leave-active { transition: transform 0.3s ease; }
.sheet-enter-from,
.sheet-leave-to { transform: translateY(100%); }
</style>

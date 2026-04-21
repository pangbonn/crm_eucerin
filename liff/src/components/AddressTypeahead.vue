<template>
  <div class="relative" ref="wrapper">
    <label v-if="label" class="label pb-1">
      <span class="label-text">{{ label }} <span v-if="required" class="text-error">*</span></span>
    </label>
    <div class="relative">
      <input
        ref="inputEl"
        v-model="query"
        type="text"
        class="input input-bordered w-full pr-8"
        :class="{ 'input-error': error }"
        :placeholder="placeholder"
        :disabled="disabled"
        autocomplete="off"
        @input="onInput"
        @focus="onFocus"
        @keydown.down.prevent="moveDown"
        @keydown.up.prevent="moveUp"
        @keydown.enter.prevent="selectHighlighted"
        @keydown.esc="close"
      />
      <button v-if="query && !disabled" type="button"
              class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              @click.prevent="clear">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
      </button>
    </div>

    <!-- Dropdown -->
    <div v-if="open && displayOptions.length > 0"
         class="absolute z-50 w-full bg-base-100 border border-base-300 rounded-xl shadow-lg mt-1 max-h-52 overflow-y-auto">
      <div v-for="(opt, i) in displayOptions" :key="opt.id"
           class="px-4 py-2.5 text-sm cursor-pointer transition-colors"
           :class="i === highlighted ? 'bg-primary text-primary-content' : 'hover:bg-base-200'"
           @mousedown.prevent="selectOption(opt)">
        {{ opt.name }}
        <span v-if="opt.zip" class="ml-2 text-xs opacity-60">{{ opt.zip }}</span>
      </div>
    </div>

    <!-- No results (only when user typed something) -->
    <div v-else-if="open && query.length >= 1 && displayOptions.length === 0 && !disabled"
         class="absolute z-50 w-full bg-base-100 border border-base-300 rounded-xl shadow-lg mt-1 px-4 py-3 text-sm text-gray-400">
      ไม่พบ "{{ query }}"
    </div>

    <p v-if="error" class="text-error text-xs mt-1">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue:   { type: [Number, null], default: null },
    // options = full list สำหรับ context นี้ เช่น ทุก amphures ของ province ที่เลือก
    options:      { type: Array, default: () => [] },
    label:        { type: String, default: '' },
    placeholder:  { type: String, default: 'พิมพ์เพื่อค้นหา...' },
    required:     { type: Boolean, default: false },
    disabled:     { type: Boolean, default: false },
    error:        { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue', 'select']);

const query       = ref('');
const open        = ref(false);
const highlighted = ref(-1);
const wrapper     = ref(null);
const inputEl     = ref(null);

// filter options จาก query ภายใน component
const displayOptions = computed(() => {
    const q = query.value.trim();
    let list = props.options;
    if (q.length > 0) {
        list = list.filter(opt => opt.name.includes(q));
    }
    return list.slice(0, 20);
});

watch(() => props.options, (newOpts) => {
    if (props.modelValue) {
        const opt = newOpts.find(o => o.id === props.modelValue);
        if (opt) query.value = opt.name;
    } else {
        query.value       = '';
        highlighted.value = -1;
    }
});

// เมื่อ parent reset modelValue เป็น null → ล้าง query
watch(() => props.modelValue, (val) => {
    if (!val) query.value = '';
});

// เมื่อ disabled หาย → reset
watch(() => props.disabled, (dis) => {
    if (dis) { query.value = ''; open.value = false; }
});

function onInput() {
    open.value = true;
    highlighted.value = 0;
    // user กำลังพิมพ์ → invalidate selection
    emit('update:modelValue', null);
    emit('select', null);
}

function onFocus() {
    if (!props.disabled) open.value = true;
}

function moveDown() {
    if (!open.value) { open.value = true; return; }
    highlighted.value = Math.min(highlighted.value + 1, displayOptions.value.length - 1);
}

function moveUp() {
    highlighted.value = Math.max(highlighted.value - 1, 0);
}

function selectHighlighted() {
    const opt = displayOptions.value[highlighted.value];
    if (opt) selectOption(opt);
}

function selectOption(opt) {
    query.value = opt.name;
    open.value  = false;
    highlighted.value = -1;
    emit('update:modelValue', opt.id);
    emit('select', opt);
}

function clear() {
    query.value = '';
    open.value  = false;
    emit('update:modelValue', null);
    emit('select', null);
    inputEl.value && inputEl.value.focus();
}

function close() { open.value = false; }

function onClickOutside(e) {
    if (wrapper.value && !wrapper.value.contains(e.target)) {
        close();
        // ถ้าพิมพ์แล้วไม่ได้เลือก → ล้างข้อความ
        if (!props.modelValue) query.value = '';
    }
}

onMounted(() => {
    if (props.modelValue) {
        const opt = props.options.find(o => o.id === props.modelValue);
        if (opt) query.value = opt.name;
    }
    document.addEventListener('mousedown', onClickOutside);
});
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside));
</script>

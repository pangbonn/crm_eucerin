<template>
  <div class="pb-10">
    <div class="fixed top-0 left-0 right-0 z-40 bg-primary text-primary-content py-4 px-4 max-w-[430px] mx-auto">
      <h1 class="text-lg font-bold">ข้อมูลสินค้า</h1>
      <p class="text-sm opacity-80">รายละเอียดผลิตภัณฑ์</p>
    </div>

    <div class="px-4 mt-20">
      <input v-model="search" type="text" class="input input-bordered w-full"
             placeholder="ค้นหาชื่อสินค้า...">
    </div>

    <div v-if="loading" class="text-center py-8">
      <span class="loading loading-spinner loading-md"></span>
    </div>

    <div v-else-if="filteredProducts.length === 0" class="text-center py-8 text-gray-400">
      <div class="text-4xl mb-2">🔍</div>
      <p>{{ search ? 'ไม่พบสินค้าที่ค้นหา' : 'ยังไม่มีข้อมูลสินค้า' }}</p>
    </div>

    <div v-else class="px-4 mt-4 space-y-4">
      <h2 class="font-bold text-sm uppercase tracking-wide mb-2 bg-red-600 text-white px-3 py-2 rounded-lg">
        สินค้าทั้งหมด
      </h2>

      <div class="space-y-3">
        <button
          v-for="item in filteredProducts"
          :key="item.id"
          type="button"
          class="w-full text-left bg-base-200 rounded-xl border border-base-300 p-3 flex gap-3 items-start"
          @click="openDetail(item)"
        >
          <img
            v-if="item.image_url"
            :src="item.image_url"
            :alt="item.name"
            class="w-20 h-20 rounded-lg object-cover shrink-0"
          >
          <div
            v-else
            class="w-20 h-20 rounded-lg bg-base-300 text-base-content/60 flex items-center justify-center text-xl shrink-0"
          >
            🧴
          </div>

          <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold leading-5">{{ item.name }}</p>
            <p class="text-xs text-gray-600 mt-1 line-clamp-3">{{ item.description || '-' }}</p>
          </div>
        </button>
      </div>
    </div>

    <div
      v-if="selectedProduct"
      class="fixed inset-0 z-50 bg-black/50 flex items-end"
      @click.self="closeDetail"
    >
      <div class="bg-white w-full max-w-[430px] mx-auto rounded-t-2xl p-4 max-h-[80vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-3">
          <h3 class="text-base font-bold text-gray-900">รายละเอียดสินค้า</h3>
          <button type="button" class="btn btn-sm btn-circle btn-ghost" @click="closeDetail">✕</button>
        </div>

        <img
          v-if="selectedProduct.image_url"
          :src="selectedProduct.image_url"
          :alt="selectedProduct.name"
          class="w-full h-52 object-cover rounded-xl mt-3"
        >
        <div
          v-else
          class="w-full h-52 rounded-xl mt-3 bg-base-200 text-base-content/60 flex items-center justify-center text-4xl"
        >
          🧴
        </div>

        <p class="mt-4 text-base font-semibold text-gray-900">{{ selectedProduct.name }}</p>
        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ selectedProduct.description || '-' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/composables/useApi';

const loading = ref(true);
const products = ref([]);
const search = ref('');
const selectedProduct = ref(null);

const filteredProducts = computed(() => {
    if (!search.value) return products.value;
    const q = search.value.toLowerCase();
    return products.value.filter(item => item.name.toLowerCase().includes(q));
});

onMounted(async () => {
    try {
        const { data } = await api.get('/api/liff/products');
        products.value = Array.isArray(data) ? data : [];
    } catch (e) {
        products.value = [];
    } finally {
        loading.value = false;
    }
});

function openDetail(item) {
    selectedProduct.value = item;
}

function closeDetail() {
    selectedProduct.value = null;
}
</script>

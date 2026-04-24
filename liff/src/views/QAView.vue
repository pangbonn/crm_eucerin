<template>
  <div class="pb-24">
    <div class="fixed top-0 left-0 right-0 z-40 bg-primary text-primary-content py-4 px-4 max-w-[430px] mx-auto">
      <h1 class="text-lg font-bold">Q&amp;A</h1>
      <p class="text-sm opacity-80">คำถามที่พบบ่อย</p>
    </div>

    <!-- Search -->
    <div class="px-4 mt-20">
      <input v-model="search" type="text" class="input input-bordered w-full"
             placeholder="ค้นหาคำถาม...">
    </div>

    <div v-if="loading" class="text-center py-8">
      <span class="loading loading-spinner loading-md"></span>
    </div>

    <div v-else-if="filteredCategories.length === 0" class="text-center py-8 text-gray-400">
      <div class="text-4xl mb-2">🔍</div>
      <p>{{ search ? 'ไม่พบคำถามที่ค้นหา' : 'ยังไม่มีข้อมูล Q&A' }}</p>
    </div>

    <div v-else class="px-4 mt-4 space-y-4">
      <div v-for="cat in filteredCategories" :key="cat.id">
        <h2 class="font-bold text-sm uppercase tracking-wide mb-2 bg-red-600 text-white px-3 py-2 rounded-lg">
          {{ cat.name }}
        </h2>
        <div class="space-y-2">
          <div v-for="item in cat.items" :key="item.id"
               class="bg-base-200 rounded-xl border border-base-300 overflow-hidden">
            <button type="button"
                    class="w-full text-left p-4 flex items-center justify-between"
                    @click="toggleItem(item.id)">
              <span class="text-sm font-medium pr-3">{{ item.question }}</span>
              <span class="text-lg leading-none">{{ isOpen(item.id) ? '−' : '+' }}</span>
            </button>
            <div v-if="isOpen(item.id)" class="px-4 pb-4 text-sm text-gray-600">
              <p>{{ item.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/composables/useApi';

const loading = ref(true);
const categories = ref([]);
const search = ref('');
const openItemIds = ref([]);

const filteredCategories = computed(() => {
    if (!search.value) return categories.value;
    const q = search.value.toLowerCase();
    return categories.value
        .map(cat => ({
            ...cat,
            items: cat.items.filter(i =>
                i.question.toLowerCase().includes(q) || i.answer.toLowerCase().includes(q)
            ),
        }))
        .filter(cat => cat.items.length > 0);
});

onMounted(async () => {
    try {
        const { data } = await api.get('/api/liff/qa');
        categories.value = data;
    } catch (e) {
        // ignore
    } finally {
        loading.value = false;
    }
});

function isOpen(id) {
    return openItemIds.value.includes(id);
}

function toggleItem(id) {
    if (isOpen(id)) {
        openItemIds.value = openItemIds.value.filter(itemId => itemId !== id);
        return;
    }
    openItemIds.value.push(id);
}
</script>

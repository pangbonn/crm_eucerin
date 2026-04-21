<template>
  <div class="pb-24">
    <div class="bg-primary text-primary-content py-4 px-4">
      <h1 class="text-lg font-bold">Q&amp;A</h1>
      <p class="text-sm opacity-80">คำถามที่พบบ่อย</p>
    </div>

    <!-- Search -->
    <div class="px-4 mt-4">
      <input v-model="search" type="text" class="input input-bordered w-full"
             placeholder="ค้นหาคำถาม...">
    </div>

    <div v-if="loading" class="text-center py-8">
      <span class="loading loading-spinner loading-md"></span>
    </div>

    <div v-else-if="filteredCategories.length === 0" class="text-center py-8 text-gray-400">
      <div class="text-4xl mb-2">🔍</div>
      <p>ไม่พบคำถามที่ค้นหา</p>
    </div>

    <div v-else class="px-4 mt-4 space-y-4">
      <div v-for="cat in filteredCategories" :key="cat.id">
        <h2 class="font-bold text-sm text-gray-500 uppercase tracking-wide mb-2">{{ cat.name }}</h2>
        <div class="space-y-2">
          <div v-for="item in cat.items" :key="item.id"
               class="collapse collapse-arrow bg-base-200 rounded-xl">
            <input type="checkbox" class="peer">
            <div class="collapse-title text-sm font-medium peer-checked:bg-primary/10 peer-checked:text-primary rounded-xl">
              {{ item.question }}
            </div>
            <div class="collapse-content text-sm text-gray-600">
              <p class="pt-1">{{ item.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import BottomNav from '@/components/BottomNav.vue';
import api from '@/composables/useApi';

const loading    = ref(true);
const categories = ref([]);
const search     = ref('');

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
</script>

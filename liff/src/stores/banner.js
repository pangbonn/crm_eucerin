import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/composables/useApi';

export const useBannerStore = defineStore('banner', () => {
    const cache = ref({});         // { [type]: bannerData | null }
    const loading = ref({});       // { [type]: true/false }

    async function fetch(type) {
        if (type in cache.value) return cache.value[type];
        if (loading.value[type]) return null;

        loading.value[type] = true;
        try {
            const { data } = await api.get(`/api/liff/banner/${type}`);
            cache.value[type] = data;
            return data;
        } catch {
            cache.value[type] = null;
            return null;
        } finally {
            loading.value[type] = false;
        }
    }

    function get(type) {
        return cache.value[type] ?? null;
    }

    return { fetch, get };
});

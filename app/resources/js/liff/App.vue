<template>
  <div class="max-w-md mx-auto min-h-screen bg-base-100">
    <router-view />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();

onMounted(async () => {
    await authStore.initLiff();
});
</script>

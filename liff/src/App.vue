<template>
  <div v-if="auth.loading" class="flex items-center justify-center min-h-screen">
    <span class="loading loading-spinner loading-lg text-primary"></span>
  </div>
  <router-view v-else />
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const auth   = useAuthStore();
const router = useRouter();

onMounted(async () => {
    await auth.initLiff();

    if (auth.isLoggedIn) {
        if (router.currentRoute.value.path === '/register') {
            router.replace('/home');
        }
    } else {
        router.replace('/register');
    }
});
</script>

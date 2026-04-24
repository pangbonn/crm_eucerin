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
    const currentRoute = router.currentRoute.value;
    const currentPath = currentRoute.path;
    const redirectQuery = typeof currentRoute.query.redirect === 'string' ? currentRoute.query.redirect : '';
    const requiresAuth = !!currentRoute.meta?.requiresAuth;
    await auth.initLiff({ forceLogin: requiresAuth });

    if (auth.isLoggedIn) {
        if (currentPath === '/register' && redirectQuery && redirectQuery !== '/register') {
            router.replace(redirectQuery);
            return;
        }
        if (currentPath === '/') {
            router.replace('/receipt');
        }
        // path อื่น (เช่น /exam จาก Rich Menu) → ค้างไว้ตาม URL
    } else {
        // Route public (เช่น /qa, /products) ให้อยู่ต่อได้โดยไม่ต้อง login
        if (!requiresAuth) return;
        // Route ที่ต้อง auth ค่อยส่งไป register และเก็บปลายทางเดิมไว้
        if (currentPath !== '/register') {
            router.replace({ path: '/register', query: { redirect: currentRoute.fullPath } });
            return;
        }
        router.replace('/register');
    }
});
</script>

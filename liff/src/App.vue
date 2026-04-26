<template>
  <div v-if="auth.loading" class="flex items-center justify-center min-h-screen">
    <span class="loading loading-spinner loading-lg text-primary"></span>
  </div>
  <!-- LIFF init ล้มเหลว (URL ไม่ตรง endpoint) — แสดงปุ่ม fallback -->
  <div v-else-if="auth.liffInitFailed" class="flex flex-col items-center justify-center min-h-screen gap-4 p-6 text-center">
    <p class="text-sm text-gray-500">ไม่สามารถเชื่อมต่อ LINE ได้</p>
    <button class="btn btn-primary w-full max-w-xs" @click="auth.retryLiffLogin()">
      เข้าสู่ระบบด้วย LINE
    </button>
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
    // อ่าน path ก่อน initLiff เพื่อตรวจสอบว่า route นี้ต้องการ forceLogin ไหม
    // ใช้ window.location.pathname เพราะ router อาจยัง redirect ไม่เสร็จ
    const intendedPath = window.location.pathname;
    const intendedRoute = router.resolve(intendedPath);
    const requiresAuth = !!intendedRoute.meta?.requiresAuth;
    const forceLogin   = requiresAuth || intendedPath === '/register';

    await auth.initLiff({ forceLogin });

    // อ่าน currentPath หลัง initLiff เสร็จ (router redirect ประมวลผลแล้ว)
    const currentPath = router.currentRoute.value.path;

    if (auth.isLoggedIn) {
        // อ่าน redirect destination จาก sessionStorage (เก็บก่อน LINE login)
        const savedRedirect = sessionStorage.getItem('liff_redirect');
        if (savedRedirect && savedRedirect !== '/register') {
            sessionStorage.removeItem('liff_redirect');
            router.replace(savedRedirect);
            return;
        }
        if (currentPath === '/register') {
            router.replace('/receipt');
            return;
        }
        if (currentPath === '/') {
            router.replace('/receipt');
        }
    } else {
        if (!requiresAuth) return;
        if (currentPath !== '/register') {
            // เก็บ destination ใน sessionStorage แทน query param
            // เพราะ query param ทำให้ liff.state มี nested params → LINE OAuth 400
            sessionStorage.setItem('liff_redirect', intendedPath);
            router.replace('/register');
            return;
        }
        router.replace('/register');
    }
});
</script>

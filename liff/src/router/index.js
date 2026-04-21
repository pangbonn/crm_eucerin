import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    { path: '/',         redirect: '/home' },
    { path: '/register', component: () => import('@/views/RegisterView.vue'), meta: { requiresAuth: false } },
    { path: '/home',     component: () => import('@/views/HomeView.vue'),     meta: { requiresAuth: true } },
    { path: '/points',   component: () => import('@/views/PointsView.vue'),   meta: { requiresAuth: true } },
    { path: '/receipt',  component: () => import('@/views/ReceiptView.vue'),  meta: { requiresAuth: true } },
    { path: '/exam',     component: () => import('@/views/ExamView.vue'),     meta: { requiresAuth: true } },
    { path: '/rewards',  component: () => import('@/views/RewardsView.vue'),  meta: { requiresAuth: true } },
    { path: '/profile',  component: () => import('@/views/ProfileView.vue'),  meta: { requiresAuth: true } },
    { path: '/qa',       component: () => import('@/views/QAView.vue'),       meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior: () => ({ top: 0 }),
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    // รอให้ LIFF init เสร็จก่อน
    if (!auth.ready) return;

    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return { path: '/register' };
    }
    if (!to.meta.requiresAuth && auth.isLoggedIn && to.path === '/register') {
        return { path: '/home' };
    }
});

export default router;

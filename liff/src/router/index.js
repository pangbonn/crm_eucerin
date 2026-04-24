import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    { path: '/',         redirect: '/receipt' },
    { path: '/register', component: () => import('@/views/RegisterView.vue'), meta: { requiresAuth: false } },
    { path: '/home',     component: () => import('@/views/HomeView.vue'),     meta: { requiresAuth: true } },
    { path: '/points',   component: () => import('@/views/PointsView.vue'),   meta: { requiresAuth: true } },
    { path: '/receipt',  component: () => import('@/views/ReceiptView.vue'),  meta: { requiresAuth: true } },
    { path: '/upload',   component: () => import('@/views/UploadReceiptView.vue'), meta: { requiresAuth: true } },
    { path: '/exam',      component: () => import('@/views/ExamView.vue'),     meta: { requiresAuth: true } },
    { path: '/exam/list', component: () => import('@/views/ExamListView.vue'), meta: { requiresAuth: true } },
    { path: '/rewards',  component: () => import('@/views/RewardsView.vue'),  meta: { requiresAuth: true } },
    { path: '/products', component: () => import('@/views/ProductView.vue'),   meta: { requiresAuth: false } },
    { path: '/profile',  component: () => import('@/views/ProfileView.vue'),  meta: { requiresAuth: true } },
    { path: '/qa',       component: () => import('@/views/QAView.vue'),       meta: { requiresAuth: false } },
];

const base = '/';

const router = createRouter({
    history: createWebHistory(base),
    routes,
    scrollBehavior: () => ({ top: 0 }),
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    // รอให้ LIFF init เสร็จก่อน
    if (!auth.ready) return;

    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return { path: '/register', query: { redirect: to.fullPath } };
    }
    if (!to.meta.requiresAuth && auth.isLoggedIn && to.path === '/register') {
        const redirectTo = typeof to.query.redirect === 'string' ? to.query.redirect : '';
        if (redirectTo && redirectTo !== '/register') {
            return { path: redirectTo };
        }
        return { path: '/receipt' };
    }
});

export default router;

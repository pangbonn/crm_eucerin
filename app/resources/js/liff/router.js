import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from './stores/auth';

const routes = [
    {
        path: '/liff',
        component: () => import('./views/HomeView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/liff/register',
        component: () => import('./views/RegisterView.vue'),
        meta: { requiresAuth: false },
    },
    {
        path: '/liff/points',
        component: () => import('./views/PointsView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/liff/receipt',
        component: () => import('./views/ReceiptView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/liff/exam',
        component: () => import('./views/ExamView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/liff/rewards',
        component: () => import('./views/RewardsView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/liff/profile',
        component: () => import('./views/ProfileView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/liff/qa',
        component: () => import('./views/QAView.vue'),
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isLoggedIn) {
        return { path: '/liff/register' };
    }

    if (!to.meta.requiresAuth && authStore.isLoggedIn && to.path === '/liff/register') {
        return { path: '/liff' };
    }
});

export default router;

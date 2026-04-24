import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { getLiff, isMock } from '@/plugins/liff';
import api from '@/composables/useApi';

export const useAuthStore = defineStore('auth', () => {
    const token     = ref(localStorage.getItem('liff_token') || null);
    const user      = ref(null);
    const lineProfile = ref(null);
    const ready     = ref(false);
    const loading   = ref(true);

    const isLoggedIn = computed(() => !!token.value && !!user.value);

    async function initLiff({ forceLogin = true } = {}) {
        loading.value = true;
        try {
            const liff = getLiff();
            await liff.init({ liffId: import.meta.env.VITE_LIFF_ID || 'mock' });

            if (!liff.isLoggedIn()) {
                if (isMock) {
                    // Mock: set logged in ทันที ไม่ต้อง redirect
                    liff._loggedIn = true;
                } else if (!forceLogin) {
                    return;
                } else {
                    liff.login({ redirectUri: window.location.href });
                    return;
                }
            }

            const profile = await liff.getProfile();
            lineProfile.value = profile;

            if (token.value) {
                await fetchUser();
            } else {
                await loginWithLine(profile, liff.getAccessToken());
            }
        } catch (e) {
            console.error('[Auth] initLiff error:', e);
        } finally {
            ready.value = true;
            loading.value = false;
        }
    }

    async function loginWithLine(profile, accessToken) {
        try {
            const { data } = await api.post('/api/liff/login', {
                line_uid:           profile.userId,
                line_access_token:  accessToken,
                display_name:       profile.displayName,
                picture_url:        profile.pictureUrl,
            });

            if (data.token) {
                setToken(data.token);
                user.value = data.user;
            }
            // needs_register: true → user.value ยังเป็น null → App.vue ดีดไป /register
        } catch (e) {
            // LINE access token หมดอายุ (401) → re-auth ผ่าน LINE ใหม่
            if (e.response && e.response.status === 401 && !isMock) {
                getLiff().login({ redirectUri: window.location.href });
            } else {
                console.error('[Auth] loginWithLine error:', e);
            }
        }
    }

    async function fetchUser() {
        try {
            const { data } = await api.get('/api/liff/me');
            user.value = data;
        } catch (e) {
            // JWT หมดอายุ → ล้างแค่ JWT (ไม่ logout LINE)
            // แล้วลอง re-login ด้วย LINE token ปัจจุบัน
            token.value = null;
            user.value  = null;
            localStorage.removeItem('liff_token');
            delete api.defaults.headers.common['Authorization'];

            if (lineProfile.value) {
                const liff = getLiff();
                await loginWithLine(lineProfile.value, liff.getAccessToken());
            }
        }
    }

    function setToken(t) {
        token.value = t;
        localStorage.setItem('liff_token', t);
        api.defaults.headers.common['Authorization'] = 'Bearer ' + t;
    }

    function logout() {
        token.value = null;
        user.value  = null;
        localStorage.removeItem('liff_token');
        delete api.defaults.headers.common['Authorization'];
        try { getLiff().logout(); } catch (e) { /* ignore */ }
    }

    function logoutAndLoginWithLine() {
        logout();
        if (!isMock) {
            try {
                getLiff().login({ redirectUri: window.location.href });
            } catch (e) {
                // fallback ไป register ถ้า login() เรียกไม่ได้
            }
        }
    }

    return { token, user, lineProfile, ready, loading, isLoggedIn, initLiff, loginWithLine, fetchUser, setToken, logout, logoutAndLoginWithLine };
});

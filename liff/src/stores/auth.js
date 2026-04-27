import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { getLiff, isMock } from '@/plugins/liff';
import api from '@/composables/useApi';

export const useAuthStore = defineStore('auth', () => {
    const token          = ref(localStorage.getItem('liff_token') || null);
    const user           = ref(null);
    const lineProfile    = ref(null);
    const ready          = ref(false);
    const loading        = ref(true);
    const liffInitFailed = ref(false);

    const isLoggedIn = computed(() => !!token.value && !!user.value);

    async function initLiff({ forceLogin = true } = {}) {
        loading.value = true;
        try {
            const liff = getLiff();
            await liff.init({ liffId: import.meta.env.VITE_LIFF_ID || 'mock' });

            if (!liff.isLoggedIn()) {
                if (isMock) {
                    liff._loggedIn = true;
                } else if (!forceLogin) {
                    return;
                } else {
                    // ส่งแค่ origin ไม่เอา pathname หรือ query string
                    // pathname='/' จะทำให้ได้ trailing slash → redirect_uri mismatch
                    liff.login({ redirectUri: window.location.origin });
                    return;
                }
            }

            const profile = await liff.getProfile();
            lineProfile.value = profile;

            if (token.value) {
                await fetchUser();
            } else {
                const accessToken = liff.getAccessToken();
                if (!accessToken) {
                    // token null = LINE session ยังไม่ได้ process — ไม่ทำอะไรต่อ
                    console.warn('[Auth] liff.getAccessToken() returned null');
                    return;
                }
                await loginWithLine(profile, accessToken);
            }
        } catch (e) {
            console.error('[Auth] initLiff error:', e);
            // ไม่ call liff.login() ที่นี่เพราะจะ infinite loop
            // ถ้า init ล้มเหลวให้แสดงปุ่ม fallback ใน App.vue แทน
            liffInitFailed.value = true;
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
            // 401 จาก /login หมายถึง backend verify token กับ LINE ไม่ผ่าน
            // ไม่ call liff.login() ซ้ำเพราะจะ infinite loop
            console.error('[Auth] loginWithLine error:', e.response?.status, e.response?.data || e.message);
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
                getLiff().login({ redirectUri: window.location.origin });
            } catch (e) {
                window.location.href = window.location.origin;
            }
        }
    }

    function retryLiffLogin() {
        liffInitFailed.value = false;
        try {
            getLiff().login({ redirectUri: window.location.origin });
        } catch (_) {
            window.location.reload();
        }
    }

    return { token, user, lineProfile, ready, loading, liffInitFailed, isLoggedIn, initLiff, loginWithLine, fetchUser, setToken, logout, logoutAndLoginWithLine, retryLiffLogin };
});

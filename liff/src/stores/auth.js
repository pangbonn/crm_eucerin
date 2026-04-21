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

    async function initLiff() {
        loading.value = true;
        try {
            const liff = getLiff();
            await liff.init({ liffId: import.meta.env.VITE_LIFF_ID || 'mock' });

            if (!liff.isLoggedIn()) {
                if (isMock) {
                    // Mock: set logged in ทันที ไม่ต้อง redirect
                    liff._loggedIn = true;
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
            // ถ้า needs_register → router guard จัดการ
        } catch (e) {
            console.error('[Auth] loginWithLine error:', e);
        }
    }

    async function fetchUser() {
        try {
            const { data } = await api.get('/api/liff/me');
            user.value = data;
        } catch (e) {
            // token หมดอายุ — logout
            logout();
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

    return { token, user, lineProfile, ready, loading, isLoggedIn, initLiff, loginWithLine, fetchUser, setToken, logout };
});

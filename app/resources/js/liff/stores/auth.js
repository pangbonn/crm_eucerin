import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../composables/useApi';

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('liff_token') || null);
    const user = ref(null);
    const lineProfile = ref(null);
    const liffReady = ref(false);

    const isLoggedIn = computed(() => !!token.value && !!user.value);

    async function initLiff() {
        try {
            await liff.init({ liffId: window.LIFF_ID });
            liffReady.value = true;

            if (!liff.isLoggedIn()) {
                liff.login({ redirectUri: window.location.href });
                return;
            }

            const profile = await liff.getProfile();
            lineProfile.value = profile;

            if (token.value) {
                await fetchUser();
            } else {
                await loginWithLine(profile);
            }
        } catch (e) {
            console.error('LIFF init error', e);
        }
    }

    async function loginWithLine(profile) {
        try {
            const accessToken = liff.getAccessToken();
            const { data } = await api.post('/api/liff/login', {
                line_uid: profile.userId,
                line_access_token: accessToken,
                display_name: profile.displayName,
                picture_url: profile.pictureUrl,
            });

            if (data.token) {
                setToken(data.token);
                user.value = data.user;
            } else if (data.needs_register) {
                // Redirect to register flow; router guard will handle
            }
        } catch (e) {
            console.error('Login error', e);
        }
    }

    async function fetchUser() {
        try {
            const { data } = await api.get('/api/liff/me');
            user.value = data;
        } catch (e) {
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
        user.value = null;
        localStorage.removeItem('liff_token');
        delete api.defaults.headers.common['Authorization'];
    }

    if (token.value) {
        api.defaults.headers.common['Authorization'] = 'Bearer ' + token.value;
    }

    return { token, user, lineProfile, liffReady, isLoggedIn, initLiff, loginWithLine, fetchUser, setToken, logout };
});

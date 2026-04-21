/**
 * LIFF wrapper — ใช้ mock เมื่อ VITE_MOCK_LIFF=true (local dev)
 * ใช้ LINE LIFF SDK จริงเมื่อ production
 */

const isMock = import.meta.env.VITE_MOCK_LIFF === 'true';

const mockLiff = {
    _loggedIn: false,

    async init({ liffId }) {
        console.log('[Mock LIFF] init liffId:', liffId);
        // ถ้ามี token ใน localStorage ถือว่า logged in แล้ว
        this._loggedIn = !!localStorage.getItem('liff_token');
    },

    isLoggedIn() {
        return this._loggedIn;
    },

    login({ redirectUri } = {}) {
        // Mock: ใส่ flag แล้ว reload — simulate LINE login สำเร็จ
        localStorage.setItem('mock_liff_authed', '1');
        this._loggedIn = true;
        console.log('[Mock LIFF] login() called — mock auth set');
        // ไม่ redirect จริง แค่ set flag
    },

    async getProfile() {
        return {
            userId:      import.meta.env.VITE_MOCK_LINE_UID      || 'Umockdev001',
            displayName: import.meta.env.VITE_MOCK_DISPLAY_NAME  || 'BA ทดสอบ',
            pictureUrl:  import.meta.env.VITE_MOCK_PICTURE        || '',
        };
    },

    getAccessToken() {
        return 'mock-access-token';
    },

    logout() {
        localStorage.removeItem('mock_liff_authed');
        localStorage.removeItem('liff_token');
        this._loggedIn = false;
    },
};

// ถ้า mock ให้ใช้ mockLiff, ถ้า production ใช้ window.liff จาก SDK
export function getLiff() {
    if (isMock) return mockLiff;
    if (typeof window !== 'undefined' && window.liff) return window.liff;
    throw new Error('LIFF SDK not loaded');
}

export { isMock };

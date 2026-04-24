import axios from 'axios';

const apiBaseURL = import.meta.env.PROD
    ? (import.meta.env.VITE_API_URL || '')
    : '';

const api = axios.create({
    baseURL: apiBaseURL,  // prod ใช้ VITE_API_URL, dev ใช้ vite proxy
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
});

// Auto-inject JWT จาก localStorage
const token = localStorage.getItem('liff_token');
if (token) {
    api.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}

export default api;

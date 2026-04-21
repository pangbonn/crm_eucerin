import axios from 'axios';

const api = axios.create({
    baseURL: '',  // proxy จาก vite.config.js ส่งต่อ /api → Laravel
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

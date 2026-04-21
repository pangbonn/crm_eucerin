import axios from 'axios';

const api = axios.create({
    baseURL: window.APP_URL || '',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    },
});

export default api;

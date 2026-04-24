import { createApp } from 'vue';
import { createPinia } from 'pinia';
import liff from '@line/liff';
import App from './App.vue';
import router from './router/index.js';
import './style.css';

window.liff = liff;

const pinia = createPinia();
const app   = createApp(App);

app.use(pinia);
app.use(router);
app.mount('#app');

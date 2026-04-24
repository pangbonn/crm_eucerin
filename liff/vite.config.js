import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const isProd = mode === 'production';
    return {
        base: "/",
        plugins: [vue()],
        resolve: {
            alias: { '@': resolve(__dirname, 'src') },
        },
        server: {
            port: 5173,
            allowedHosts: true,
            proxy: {
                '/api': {
                    target: env.VITE_API_URL || 'http://127.0.0.1:8000',
                    changeOrigin: true,
                },
                '/storage': {
                    target: env.VITE_API_URL || 'http://127.0.0.1:8000',
                    changeOrigin: true,
                },
            },
        },
    };
});

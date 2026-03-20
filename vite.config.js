import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/frontend/app.js',
                'resources/js/admin/admin.js',
            ],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    server: {
        host: 'localhost',
        port: 5173,
        https: false,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            clientPort: 5173,
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        proxy: {
            '/admin': {
                target: 'http://armtrip.test',
                changeOrigin: true,
            },
        },
    },
});

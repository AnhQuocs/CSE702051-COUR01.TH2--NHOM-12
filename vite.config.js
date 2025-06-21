import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: false, // Tắt hot reload để tránh lỗi WebSocket
        }),
    ],
    server: {
        hmr: false, // Tắt Hot Module Replacement
        watch: {
            usePolling: false, // Tắt file watching
        },
        cors: false, // Tắt CORS để tránh WebSocket
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined, // Tắt code splitting
            }
        }
    }
});

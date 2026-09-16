import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [react(), tailwindcss()],
    server: {
        host: true,
        port: 5173,
        allowedHosts: ['builder', 'localhost', '127.0.0.1'],
        proxy: {
            '/api': {
                target: 'http://repair-business-app:80',
                changeOrigin: true,
            },
        },
    },
    build: {
        outDir: 'dist',
    },
});
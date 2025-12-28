import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/global.css',
                'resources/css/main.css',
                'resources/css/welcome.css',
                'resources/css/login.css',
                'resources/css/signup.css',
                'resources/css/dashboard.css',
                'resources/css/tuteur-dashboard.css',
                'resources/js/app.js',
                'resources/js/welcome.js',
                'resources/js/login.js',
                'resources/js/signup.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});

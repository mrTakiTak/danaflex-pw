import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import path from 'path';
import {watch} from "vite-plugin-watch";

export default defineConfig(({isSsrBuild}) => ({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        watch({
            pattern: "routes/*.php",
            command: "php artisan ziggy:generate",
            onInit: !isSsrBuild
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            'ziggy-js': path.resolve('vendor/tightenco/ziggy'),
        },
    },
}));

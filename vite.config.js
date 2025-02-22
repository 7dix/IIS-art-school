import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    // server: {
    //     host: 'localhost',
    //     port: 5173,
    //     hmr: {
    //       host: 'localhost',
    //     },
    //   },
    plugins: [
        laravel({
            input: "resources/js/app.js",
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
    ],
    resolve: {
        alias: {
            ziggy: path.resolve("vendor/tightenco/ziggy/dist"),
            "ziggy-vue": path.resolve("vendor/tightenco/ziggy"), // Alias for ZiggyVue
        },
    },
});

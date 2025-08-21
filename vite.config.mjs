import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
        host: true, // Biar bisa diakses dari LAN / HP
    },
    plugins: [
        laravel({
            input: ["resources/sass/app.scss", "resources/js/app.js"],
            refresh: true,
            buildDirectory: "build", // default: "build"
        }),
    ],
    build: {
        outDir: "public/build",
        emptyOutDir: true,
    },
});

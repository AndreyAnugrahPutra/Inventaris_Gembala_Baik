import '../css/app.css';
import './bootstrap';
import 'primeicons/primeicons.css'

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import Primevue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import {definePreset} from '@primevue/themes'
import { ToastService, ConfirmationService } from 'primevue';

const appName = import.meta.env.VITE_APP_NAME;

const customTheme = definePreset(Aura, {
    semantic: {
        primary: {
            50: "#f0f9ff",
            100: "#e0f2fe",
            200: "#bae6fd",
            300: "#7dd3fc",
            400: "#38bdf8",
            500: "#0ea5e9",
            600: "#0284c7",
            700: "#0369a1",
            800: "#075985",
            900: "#0c4a6e",
            950: "#082f49",
        },
    },
});

createInertiaApp({
    title: (title) => `${appName} | ${title}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Primevue, {
                theme: {
                    preset: customTheme,
                    options: {
                        darkModeSelector: false,
                    },
                },
            })
            .use(ToastService)
            .use(ConfirmationService)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#0ea5e9",
    },
});

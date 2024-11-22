import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-vue'; 
import { addCollection, Icon } from '@iconify/vue';
import solarIcons from '@iconify-json/solar/icons.json';
import lucideIcons from '@iconify-json/lucide/icons.json';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

addCollection(solarIcons);
addCollection(lucideIcons);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Icon', Icon)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

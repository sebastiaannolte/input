require('./bootstrap');

// Import modules...
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import axios from 'axios'
import VueAxios from 'vue-axios'
import mitt from 'mitt';
const emitter = mitt();

const el = document.getElementById('app');

createInertiaApp({
    title: title => `${title} | bet.test`,
    resolve: (name) => require(`./Pages/${name}`),
    setup({ el, app, props, plugin }) {
        const apps = createApp({ render: () => h(app, props) })
            .mixin({ methods: { route } })
            .use(plugin)
            .component('Head', Head)
            .component('InertiaLink', Link)
            .use(VueAxios, axios);

        apps.config.globalProperties.emitter = emitter;
        apps.mount(el);
    },
});


InertiaProgress.init({ color: '#4B5563' });

import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import axios from 'axios'
import VueAxios from 'vue-axios'
import Toaster from '@meforma/vue-toaster'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { Chart, LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend, Tooltip } from 'chart.js'

Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend, Tooltip)

const el = document.getElementById('app')

createInertiaApp({
  title: title => `${title} | ${import.meta.env.VITE_APP_NAME}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, app, props, plugin }) {
    const apps = createApp({ render: () => h(app, props) })
      .mixin({ methods: { route } })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .component('InertiaLink', Link)
      .use(VueAxios, axios)
      .use(Toaster)

    apps.mount(el)
  },
})

InertiaProgress.init({ color: '#ef4443' })
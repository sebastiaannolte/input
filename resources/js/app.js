import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import axios from 'axios'
import VueAxios from 'vue-axios'
import mitt from 'mitt'
import Toaster from '@meforma/vue-toaster'
const emitter = mitt()
import { Chart, LineController, LineElement, PointElement, LinearScale, Title, CategoryScale } from 'chart.js'
Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale)

const regeneratorRuntime = require('regenerator-runtime')
const el = document.getElementById('app')

createInertiaApp({
  title: title => `${title} | ${process.env.MIX_APP_NAME}`,
  resolve: (name) => require(`./Pages/${name}`),
  setup({ el, app, props, plugin }) {
    const apps = createApp({ render: () => h(app, props) })
      .mixin({ methods: { route } })
      .use(plugin)
      .component('Head', Head)
      .component('InertiaLink', Link)
      .use(VueAxios, axios)
      .use(Toaster)

    apps.config.globalProperties.emitter = emitter
    apps.mount(el)
  },
})

InertiaProgress.init({ color: '#4B5563' })
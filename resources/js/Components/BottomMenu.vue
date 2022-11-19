<template>
  <section
    id="bottom-navigation"
    class="sm:hidden block fixed inset-x-0 bottom-0 z-10 bg-white shadow"
    style="padding-bottom: env(safe-area-inset-bottom)"
  >
    <div id="tabs" class="flex justify-between">
      <span
        v-for="item in menu"
        :key="item.name"
        class="w-full justify-center inline-block text-center pt-2 pb-1"
        @click="clickAction(item)"
      >
        <component :is="item.icon" class="w-6 h-6 inline-block mb-1" :class="activeItem == item.name ? 'text-black' : 'text-gray-400'" />
        <span class="tab tab-home block text-xs">{{ item.name }}</span>
      </span>
    </div>
  </section>
</template>

<script>
import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

import {
  HomeIcon,
  PlusCircleIcon,
  CogIcon,
  SearchCircleIcon,
  DocumentReportIcon,
} from '@heroicons/vue/solid'
import emitter from '@/Plugins/mitt'

export default {
  components: {
    PlusCircleIcon,
    HomeIcon,
    CogIcon,
    SearchCircleIcon,
    DocumentReportIcon,
  },
  props: {
    errors: Object,
    bet: Object,
  },

  setup() {
    const user = computed(() => usePage().props.value.auth.user)

    let menu = [
      {
        name: 'Your bets',
        url: '/' + user.value.username,
        icon: 'HomeIcon',
      },
      {
        name: 'Your stats',
        url: '/' + user.value.username + '/stats',
        icon: 'DocumentReportIcon',
      },
      {
        name: 'New bet',
        url: '',
        click: 'openBet',
        icon: 'PlusCircleIcon',
      },
      {
        name: 'Special stats',
        url: '/' + user.value.username + '/special',
        icon: 'SearchCircleIcon',
      },
      {
        name: 'Settings',
        url: '/' + user.value.username + '/settings',
        icon: 'CogIcon',
      },
    ]

    return {
      user,
      menu,
    }
  },

  data() {
    return {
      activeItem: null,
    }
  },

  created() {
    this.getActiveItem()
  },

  methods: {
    openBet() {
      emitter.emit('betForm:show')
    },

    getActiveItem(item) {
      if (item && item.url) {
        this.activeItem = item.name
        return
      }
      this.menu.forEach((item) => {
        if (item.url == '/' + window.location.pathname.substr(1)) {
          this.activeItem = item.name
          return
        }
      })
    },

    clickAction(item) {
      this.getActiveItem(item)
      if (item.url) {
        this.$inertia.visit(item.url)
        return
      }
      this[item.click]()
    },
  },
}
</script>
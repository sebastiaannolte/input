<template>
  <section id="bottom-navigation" class="fixed inset-x-0 bottom-0 z-10 block bg-white shadow dark:bg-slate-800 sm:hidden" style="padding-bottom: env(safe-area-inset-bottom)">
    <div id="tabs" class="flex justify-between">
      <span v-for="item in menu" :key="item.name" class="relative inline-block w-full justify-center pt-2 pb-1 text-center" @click="clickAction(item)">
        <span v-if="item.counter" class="absolute top-1 right-1 rounded-md bg-red-500 px-0.5 text-[10px] text-white">{{ item.counter }}</span>
        <component :is="item.icon" class="mb-1 inline-block h-6 w-6" :class="activeItem == item.name ? 'text-black dark:text-slate-200' : 'text-gray-400 dark:text-slate-400'" />
        <span class="tab tab-home block text-[10px]">{{ item.name }}</span>
      </span>
    </div>
  </section>
</template>

<script>
import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

import { HomeIcon, PlusCircleIcon, CogIcon, MagnifyingGlassIcon, DocumentChartBarIcon } from '@heroicons/vue/20/solid'
import emitter from '@/Plugins/mitt'

export default {
  components: {
    PlusCircleIcon,
    HomeIcon,
    CogIcon,
    MagnifyingGlassIcon,
    DocumentChartBarIcon,
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
        counter: usePage().props.value.openBetCount,
      },
      {
        name: 'Your stats',
        url: '/' + user.value.username + '/stats',
        icon: 'DocumentChartBarIcon',
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
        icon: 'MagnifyingGlassIcon',
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

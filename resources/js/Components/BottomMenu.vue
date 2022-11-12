<template>
  <section
    id="bottom-navigation"
    class="sm:hidden block fixed inset-x-0 bottom-0 z-10 bg-white shadow"
  >
    <section
      id="bottom-navigation"
      class="block fixed inset-x-0 bottom-0 z-10 bg-white shadow"
    >
      <div id="tabs" class="flex justify-between">
        <span
          v-for="item in menu"
          :key="item.name"
          class="w-full justify-center inline-block text-center pt-2 pb-1"
          @click="clickAction(item)"
        >
          <component :is=" activeItem == item.name ? item.icon+'Solid' : item.icon" class="w-6 h-6 inline-block mb-1" />
          <span class="tab tab-home block text-xs">{{ item.name }}</span>
        </span>
      </div>
    </section>
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
} from '@heroicons/vue/outline'

import {
  HomeIcon as HomeIconSolid,
  PlusCircleIcon as PlusCircleIconSolid,
  CogIcon as CogIconSolid,
  SearchCircleIcon as SearchCircleIconSolid,
  DocumentReportIcon as DocumentReportIconSolid,
} from '@heroicons/vue/solid'

export default {
  components: {
    PlusCircleIcon,
    HomeIcon,
    CogIcon,
    SearchCircleIcon,
    DocumentReportIcon,

    HomeIconSolid,
    PlusCircleIconSolid,
    CogIconSolid,
    SearchCircleIconSolid,
    DocumentReportIconSolid,
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
        name: 'Special stats',
        url: '/' + user.value.username + '/special',
        icon: 'SearchCircleIcon',
      },
      {
        name: 'Settings',
        url: '/' + user.value.username + '/settings',
        icon: 'CogIcon',
      },
      {
        name: 'New bet',
        url: '',
        click: 'openBet',
        icon: 'PlusCircleIcon',
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
      this.emitter.emit('betForm:show')
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
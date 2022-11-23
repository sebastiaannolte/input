<template>
  <div class="pt-20 pb-5 sm:mx-auto sm:w-full sm:max-w-md">
    <logo class="mx-auto h-16 w-auto fill-current text-blue-600" />
  </div>
  <div class="mx-auto max-w-md px-4 pt-4 pb-12 lg:pb-16">
    <slot />
  </div>
</template>

<script>
import { ref } from 'vue'
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { MenuIcon, XIcon } from '@heroicons/vue/outline'
import Logo from '@/Components/Logo.vue'

export default {
  components: {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    MenuIcon,
    XIcon,
    Logo,
  },

  setup() {
    const open = ref(false)

    const menu = [
      {
        name: 'Login',
        url: 'login',
      },
    ]

    return {
      open,
      menu,
    }
  },

  methods: {
    url() {
      return location.pathname.substr(1)
    },
  },
}
</script>

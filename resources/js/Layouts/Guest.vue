
<template>
  <div class="sm:mx-auto sm:w-full sm:max-w-md pt-20 pb-5">
    <logo class="h-8 fill-current mx-auto h-16 w-auto text-blue-600"></logo>
  </div>
  <div class="max-w-md mx-auto pt-4 pb-12 px-4 lg:pb-16">
    <slot></slot>
  </div>
</template>

<script>
import { ref } from "vue";
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from "@headlessui/vue";
import { MenuIcon, XIcon } from "@heroicons/vue/outline";
import Logo from "@/Components/Logo";

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
    const open = ref(false);

    const menu = [
      {
        name: "Login",
        url: "login",
      },
    ];

    return {
      open,
      menu,
    };
  },

  methods: {
    url() {
      return location.pathname.substr(1);
    },
  },
};
</script>
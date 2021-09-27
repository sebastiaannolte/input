<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <Disclosure as="nav" class="bg-white shadow" v-slot="{ open }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <inertia-link :href="route('index')">
              <logo class="h-8 w-auto"></logo>
            </inertia-link>
          </div>
        </div>
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
            <inertia-link
              v-for="(item, key) in menu"
              :key="key"
              :href="'/' + item.url"
              class="
                border-transparent
                text-gray-500
                hover:border-gray-300
                hover:text-gray-700
                inline-flex
                items-center
                px-1
                pt-1
                border-b-2
                text-sm
                font-medium
              "
              :class="{
                'border-indigo-500 text-gray-900': url() == item.url,
              }"
            >
              {{ item.name }}
            </inertia-link>
          </div>
        </div>
        <div class="-mr-2 flex items-center sm:hidden">
          <!-- Mobile menu button -->
          <DisclosureButton
            class="
              inline-flex
              items-center
              justify-center
              p-2
              rounded-md
              text-gray-400
              hover:text-gray-500
              hover:bg-gray-100
              focus:outline-none
              focus:ring-2 focus:ring-inset focus:ring-indigo-500
            "
          >
            <span class="sr-only">Open main menu</span>
            <MenuIcon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
            <XIcon v-else class="block h-6 w-6" aria-hidden="true" />
          </DisclosureButton>
        </div>
      </div>
    </div>

    <DisclosurePanel class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
       <inertia-link
          v-for="(item, key) in menu"
          :key="key"
          :href="'/' + item.url"
          :class="{
            'bg-indigo-50 border-indigo-500 text-indigo-700': url() == item.url,
          }"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          >{{ item.name }}</inertia-link
        >
      </div>
    </DisclosurePanel>
  </Disclosure>
  <div class="max-w-7xl mx-auto pt-4 pb-12 px-4 lg:pb-16">
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
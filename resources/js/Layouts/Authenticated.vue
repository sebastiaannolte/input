<!-- This example requires Tailwind CSS v2.0+ -->
<template>
<flash-messages/>
  <Disclosure as="nav" class="bg-white shadow" v-slot="{ open }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <inertia-link v-if="user" :href="route('userhome', user.username)">
              <logo class="hidden lg:block h-8 w-auto"></logo>
            </inertia-link>
            <inertia-link v-else :href="route('index')">
              <logo class="hidden lg:block h-8 w-auto"></logo>
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
                'border-indigo-500 text-white': url() == item.url,
              }"
            >
              {{ item.name }}
            </inertia-link>
          </div>

          <!-- Profile dropdown -->
          <Menu as="div" class="ml-3 relative" v-if="user">
            <div>
              <MenuButton
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
              >
                <span class="sr-only">Open user menu</span>
                {{ user.name }}
              </MenuButton>
            </div>
            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems
                class="
                  origin-top-right
                  absolute
                  right-0
                  mt-2
                  w-48
                  rounded-md
                  shadow-lg
                  py-1
                  bg-white
                  ring-1 ring-black ring-opacity-5
                  focus:outline-none
                "
              >
                <MenuItem v-slot="{ active }">
                  <inertia-link
                    :href="route('profile.index', user.username)"
                    :class="[
                      active ? 'bg-gray-100' : '',
                      'block px-4 py-2 text-sm text-gray-700',
                    ]"
                    >Your Profile</inertia-link
                  >
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <inertia-link
                    :href="route('userSettings.index', user.username)"
                    :class="[
                      active ? 'bg-gray-100' : '',
                      'block px-4 py-2 text-sm text-gray-700',
                    ]"
                  >
                    Settings
                  </inertia-link>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <inertia-link
                    href="/logout"
                    method="post"
                    :class="[
                      active ? 'bg-gray-100' : '',
                      'block px-4 py-2 text-sm text-gray-700',
                    ]"
                  >
                    Sign out
                  </inertia-link>
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu>
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
        <a
          href="#"
          class="
            bg-indigo-50
            border-indigo-500
            text-indigo-700
            block
            pl-3
            pr-4
            py-2
            border-l-4
            text-base
            font-medium
          "
          >Dashboard</a
        >
        <a
          href="#"
          class="
            border-transparent
            text-gray-500
            hover:bg-gray-50
            hover:border-gray-300
            hover:text-gray-700
            block
            pl-3
            pr-4
            py-2
            border-l-4
            text-base
            font-medium
          "
          >Team</a
        >
        <a
          href="#"
          class="
            border-transparent
            text-gray-500
            hover:bg-gray-50
            hover:border-gray-300
            hover:text-gray-700
            block
            pl-3
            pr-4
            py-2
            border-l-4
            text-base
            font-medium
          "
          >Projects</a
        >
        <a
          href="#"
          class="
            border-transparent
            text-gray-500
            hover:bg-gray-50
            hover:border-gray-300
            hover:text-gray-700
            block
            pl-3
            pr-4
            py-2
            border-l-4
            text-base
            font-medium
          "
          >Calendar</a
        >
      </div>
      <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="flex items-center px-4">
          <div class="flex-shrink-0">
            <img
              class="h-10 w-10 rounded-full"
              src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
              alt=""
            />
          </div>
          <div class="ml-3">
            <div class="text-base font-medium text-gray-800">Tom Cook</div>
            <div class="text-sm font-medium text-gray-500">tom@example.com</div>
          </div>
          <button
            class="
              ml-auto
              flex-shrink-0
              bg-white
              p-1
              rounded-full
              text-gray-400
              hover:text-gray-500
              focus:outline-none
              focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
            "
          >
            <span class="sr-only">View notifications</span>
            <BellIcon class="h-6 w-6" aria-hidden="true" />
          </button>
        </div>
        <div class="mt-3 space-y-1">
          <a
            href="#"
            class="
              block
              px-4
              py-2
              text-base
              font-medium
              text-gray-500
              hover:text-gray-800
              hover:bg-gray-100
            "
            >Your Profile</a
          >
          <a
            href="#"
            class="
              block
              px-4
              py-2
              text-base
              font-medium
              text-gray-500
              hover:text-gray-800
              hover:bg-gray-100
            "
            >Settings</a
          >
          <a
            href="#"
            class="
              block
              px-4
              py-2
              text-base
              font-medium
              text-gray-500
              hover:text-gray-800
              hover:bg-gray-100
            "
            >Sign out</a
          >
        </div>
      </div>
    </DisclosurePanel>
  </Disclosure>
  <div class="max-w-7xl mx-auto pt-10 pb-12 px-4 lg:pb-16">
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
import { BellIcon, MenuIcon, XIcon } from "@heroicons/vue/outline";
import Logo from "@/Components/Logo";
import FlashMessages from "@/Components/FlashMessages";
import { computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";

export default {
  components: {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    BellIcon,
    MenuIcon,
    XIcon,
    Logo,
    FlashMessages,
  },

  setup() {
    const open = ref(false);
    const user = computed(() => usePage().props.value.auth.user);

    let menu = [
      {
        name: "Your bets",
        url: user.value.username,
      },
      {
        name: "Your stats",
        url: user.value.username + "/stats",
      },
    ];

    return {
      open,
      user,
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
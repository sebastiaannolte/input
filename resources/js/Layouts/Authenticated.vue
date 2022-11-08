<template>
  <flash-messages />
  <inertia-link
    :href="this.route('import.index')"
    class="
      hidden
      md:block
      fixed
      bottom-4
      right-4
      sm:bottom-24
      sm:right-10
      inline-flex
      items-center
      p-2
      border border-transparent
      rounded-full
      shadow-sm
      text-white
      bg-green-600
      hover:bg-green-700
      focus:outline-none
      z-10
      focus:ring-2 focus:ring-offset-2 focus:ring-green-500
    "
  >
   <div class="w-6 h-6 flex justify-center">{{$page.props.importCounter}}</div>
  </inertia-link>
  <button
    type="button"
    @click="openBet"
    class="
      hidden
      md:block
      fixed
      bottom-4
      right-4
      sm:bottom-10
      sm:right-10
      inline-flex
      items-center
      p-3
      border border-transparent
      rounded-full
      shadow-sm
      text-white
      bg-indigo-600
      hover:bg-indigo-700
      focus:outline-none
      z-10
      focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
    "
  >
    <PlusIconOutline class="h-6 w-6" aria-hidden="true" />
  </button>
  <bet-form-slide-over :errors="errors" />
  <Disclosure as="nav" class="bg-white shadow" v-slot="{ open }">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex justify-center sm:justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <inertia-link v-if="user" :href="route('userhome', user.username)">
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
                'border-indigo-500 text-white': url() == item.url,
              }"
            >
              {{ item.name }}
            </inertia-link>
          </div>

          <!-- Profile dropdown -->
          <Menu as="div" class="ml-8 relative" v-if="user">
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
                  z-10
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
                    as="button"
                    :class="[
                      active ? 'bg-gray-100' : '',
                      'block px-4 py-2 text-sm text-gray-700 w-full flex justify-start',
                    ]"
                  >
                    Sign out
                  </inertia-link>
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu>
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
      <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="flex items-center px-4">
          <div class="text-base font-medium text-gray-800">
            {{ $page.props.auth.user.name }}
          </div>
        </div>
        <div class="mt-3 space-y-1">
          <inertia-link
            :href="route('profile.index', user.username)"
            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
            :class="{
              'bg-indigo-50 border-indigo-500 text-indigo-700':
                url() == user.username + '/profile',
            }"
          >
            Your profile
          </inertia-link>
          <inertia-link
            :href="route('userSettings.index', user.username)"
            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
            :class="{
              'bg-indigo-50 border-indigo-500 text-indigo-700':
                url() == user.username + '/settings',
            }"
          >
            Settings
          </inertia-link>
          <inertia-link
            href="/logout"
            method="post"
            as="button"
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
            >Sign out</inertia-link
          >
        </div>
      </div>
    </DisclosurePanel>
  </Disclosure>
  <div class="max-w-7xl mx-auto pt-4 pb-12 px-4 lg:pb-16">
    <slot></slot>
    <bottom-menu/>
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
import {
  BellIcon,
  MenuIcon,
  XIcon,
  PlusIcon as PlusIconOutline,
} from "@heroicons/vue/outline";
import Logo from "@/Components/Logo";
import FlashMessages from "@/Components/FlashMessages";
import { computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import BetFormSlideOver from "@/PageComponents/BetFormSlideOver";
import BottomMenu from '@/Components/BottomMenu.vue';

export default {
  props: {
    errors: Object,
    bet: Object,
  },
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
    BetFormSlideOver,
    PlusIconOutline,
    BottomMenu,
  },

  data() {
    return {};
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
      {
        name: "Special stats",
        url: user.value.username + "/special",
      },
    ];

    return {
      open,
      user,
      menu,
    };
  },

  methods: {
    openBet() {
      this.emitter.emit("betForm:show");
    },
    url() {
      return location.pathname.substr(1);
    },
  },
};
</script>
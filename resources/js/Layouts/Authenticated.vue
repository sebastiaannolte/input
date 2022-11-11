<template>
  <FlashMessages />
  <inertia-link
    :href="route('import.index')"
    class="fixed bottom-4 right-4 z-10 inline-flex hidden items-center rounded-full border border-transparent bg-green-600 p-2 text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:bottom-24 sm:right-10 md:block"
  >
    <div class="flex h-6 w-6 justify-center">
      {{ $page.props.importCounter }}
    </div>
  </inertia-link>
  <button
    type="button"
    class="fixed bottom-4 right-4 z-10 inline-flex hidden items-center rounded-full border border-transparent bg-indigo-600 p-3 text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:bottom-10 sm:right-10 md:block"
    @click="openBet"
  >
    <PlusIconOutline class="h-6 w-6" aria-hidden="true" />
  </button>
  <BetFormSlideOver :errors="errors" />
  <Disclosure v-slot="{ open }" as="nav" class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4">
      <div class="flex h-16 justify-center sm:justify-between">
        <div class="flex">
          <div class="flex flex-shrink-0 items-center">
            <inertia-link v-if="user" :href="route('userhome', user.username)">
              <Logo class="h-8 w-auto" />
            </inertia-link>
          </div>
        </div>
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
            <inertia-link
              v-for="(item, key) in menu" :key="key" :href="'/' + item.url"
              class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700"
              :class="[url() == item.url ? 'border-indigo-500' : 'hover:border-gray-300']"
            >
              {{ item.name }}
            </inertia-link>
          </div>

          <!-- Profile dropdown -->
          <Menu v-if="user" as="div" class="relative ml-8 mr-4">
            <div>
              <MenuButton
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
              >
                <span class="sr-only">Open user menu</span>
                {{ user.name }}
              </MenuButton>
            </div>
            <transition
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="scale-95 transform opacity-0"
              enter-to-class="scale-100 transform opacity-100"
              leave-active-class="transition duration-75 ease-in"
              leave-from-class="scale-100 transform opacity-100"
              leave-to-class="scale-95 transform opacity-0"
            >
              <MenuItems
                class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <MenuItem v-slot="{ active }">
                  <inertia-link
                    :href="route('profile.index', user.username)"
                    :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']"
                  >
                    Your Profile
                  </inertia-link>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <inertia-link
                    :href="route('userSettings.index', user.username)"
                    :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']"
                  >
                    Settings
                  </inertia-link>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <inertia-link
                    href="/logout" method="post" as="button" :class="[
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

          <span class="isolate inline-flex rounded-md shadow-sm">

            <inertia-link
              type="button" :href="route('import.index')"
              class="rounded--md relative -mr-px inline-flex w-10 items-center justify-center border border-gray-300 bg-white py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
              {{ $page.props.importCounter }}</inertia-link>
            <button
              type="button"
              class="relative inline-flex items-center rounded-r-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
              @click="openBet"
            >
              <BookmarkIcon class="-ml-1 mr-2 h-5 w-5 text-gray-400" aria-hidden="true" />
              New
            </button>
          </span>
        </div>
      </div>
    </div>

    <DisclosurePanel class="sm:hidden">
      <div class="space-y-1 pt-2 pb-3">
        <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
        <inertia-link
          v-for="(item, key) in menu" :key="key" :href="'/' + item.url" :class="{
            'bg-indigo-50 border-indigo-500 text-indigo-700': url() == item.url,
          }" class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
        >
          {{ item.name }}
        </inertia-link>
      </div>
      <div class="border-t border-gray-200 pt-4 pb-3">
        <div class="flex items-center px-4">
          <div class="text-base font-medium text-gray-800">
            {{ $page.props.auth.user.name }}
          </div>
        </div>
        <div class="mt-3 space-y-1">
          <inertia-link
            :href="route('profile.index', user.username)"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium" :class="{
              'bg-indigo-50 border-indigo-500 text-indigo-700': url() == user.username + '/profile',
            }"
          >
            Your profile
          </inertia-link>
          <inertia-link
            :href="route('userSettings.index', user.username)"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium" :class="{
              'bg-indigo-50 border-indigo-500 text-indigo-700': url() == user.username + '/settings',
            }"
          >
            Settings
          </inertia-link>
          <inertia-link
            href="/logout" method="post" as="button"
            class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800"
          >
            Sign out
          </inertia-link>
        </div>
      </div>
    </DisclosurePanel>
  </Disclosure>
  <div class="mx-auto max-w-7xl px-4 pt-4 pb-12 lg:pb-16">
    <slot />
    <BottomMenu />
  </div>
</template>

<script>
export default {
  methods: {
    openBet() {
      this.emitter.emit('betForm:show')
    },
    url() {
      return location.pathname.substr(1)
    },
  },
}
</script>

<script setup>
import { ref, computed } from 'vue'
import { BookmarkIcon } from '@heroicons/vue/solid'
import {
  Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems,
} from '@headlessui/vue'
import {
  BellIcon, MenuIcon, XIcon, PlusIcon as PlusIconOutline,
} from '@heroicons/vue/outline'
import { usePage } from '@inertiajs/inertia-vue3'
import BottomMenu from '@/Components/BottomMenu.vue'
import Logo from '@/Components/Logo.vue'
import FlashMessages from '@/Components/FlashMessages.vue'
import BetFormSlideOver from '@/PageComponents/BetFormSlideOver.vue'

defineProps({
  errors: Object,
  bet: Object,
})

const user = computed(() => usePage().props.value.auth.user)

const menu = [
  {
    name: 'Your bets',
    url: user.value.username,
  },
  {
    name: 'Your stats',
    url: `${user.value.username}/stats`,
  },
  {
    name: 'Special stats',
    url: `${user.value.username}/special`,
  },
]
</script>

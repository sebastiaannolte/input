<template>
  <Head :title="title" />
  <FlashMessages />
  <Search :open="searchOpen" @close="closeSearch" />
  <BetFormSlideOver :errors="errors" />
  <Disclosure v-slot="{ open }" as="nav" class="mx-auto w-full max-w-7xl px-2 sm:px-4 pt-2 pb-4 sm:px-0 sm:pt-8">
    <Card :hover="false" class="bg-white px-4">
      <div class="mx-auto max-w-7xl">
        <div class="flex h-16 justify-center sm:justify-between">
          <div class="flex flex-1">
            <div class="flex w-full flex-shrink-0 items-center justify-between">
              <div class="flex-1 sm:hidden">
                <Card :hover="true" @click="searchOpen = true" class="mr-4 inline-flex cursor-pointer bg-blue-400 px-2 py-2 text-white">
                  <MagnifyingGlassIcon class="h-5 w-5" />
                </Card>
              </div>
              <Card :hover="true">
                <inertia-link v-if="user" :href="route('userhome', user.username)" class="inline-flex items-start justify-start rounded bg-red-500 px-4 py-1.5 hover:bg-red-600">
                  <p class="font-archivo text-xl uppercase tracking-wider text-white">input</p>
                </inertia-link>
              </Card>
              <div class="flex flex-1 sm:hidden justify-end font-bold">
                <Card :hover="true">
                  <inertia-link type="button" :href="route('import.index')" class="relative -mr-px inline-flex w-10 items-center justify-center rounded-md border border-gray-300 bg-white py-2 text-sm text-gray-700 hover:bg-gray-50 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-200/20 dark:bg-slate-400/10 dark:text-slate-200 dark:hover:bg-slate-400/20"> {{ $page.props.importCounter }}</inertia-link>
                </Card>
              </div>
            </div>
          </div>
          <div class="hidden font-semibold sm:ml-6 sm:flex sm:items-center">
            <div class="hidden sm:ml-6 sm:flex sm:space-x-5">
              <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
              <inertia-link v-for="(item, key) in menu" :key="key" :href="'/' + item.url" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm text-gray-700 hover:text-gray-700 dark:text-slate-200" :class="[url() == item.url ? 'border-indigo-500' : 'hover:border-gray-300']">
                {{ item.name }}
              </inertia-link>
            </div>

            <!-- Profile dropdown -->
            <Menu v-if="user" as="div" class="relative ml-5 mr-4">
              <div>
                <MenuButton class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-slate-200">
                  <span class="sr-only">Open user menu</span>
                  {{ user.name }}
                </MenuButton>
              </div>
              <transition enter-active-class="transition ease-out duration-200" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-slate-800">
                  <MenuItem v-slot="{ active }">
                    <inertia-link :href="route('profile.index', user.username)" :class="[active ? 'bg-gray-100 dark:hover:bg-slate-400/20' : '', 'block px-4 py-2 text-sm text-gray-700 dark:text-slate-400']"> Your Profile </inertia-link>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <inertia-link :href="route('userSettings.index', user.username)" :class="[active ? 'bg-gray-100 dark:hover:bg-slate-400/20' : '', 'block px-4 py-2 text-sm text-gray-700 dark:text-slate-400']"> Settings </inertia-link>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <inertia-link href="/logout" method="post" as="button" :class="[active ? 'bg-gray-100 dark:hover:bg-slate-400/20' : '', 'flex w-full justify-start px-4 py-2 text-sm text-gray-700 dark:text-slate-400']"> Sign out </inertia-link>
                  </MenuItem>
                </MenuItems>
              </transition>
            </Menu>

            <Card :hover="true" @click="searchOpen = true" class="mr-4 inline-flex cursor-pointer bg-blue-400 px-2 py-2 text-white">
              <MagnifyingGlassIcon class="h-5 w-5" />
            </Card>

            <Card class="flex">
              <inertia-link type="button" :href="route('import.index')" class="flex items-center justify-center px-3"> {{ $page.props.importCounter }}</inertia-link>
              <button type="button" class="flex border-l-4 border-black px-3 py-2 text-sm text-gray-700" @click="openBet">
                <BookmarkIcon class="mr-2 h-5 w-5" aria-hidden="true" />
                <span class="dark:text-slate-200">New</span>
              </button>
            </Card>
          </div>
        </div>
      </div>

      <DisclosurePanel class="sm:hidden">
        <div class="space-y-1 pt-2 pb-3">
          <inertia-link
            v-for="(item, key) in menu"
            :key="key"
            :href="'/' + item.url"
            :class="{
              'border-indigo-500 bg-indigo-50 text-indigo-700': url() == item.url,
            }"
            class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
          >
            {{ item.name }}
          </inertia-link>
        </div>
        <div class="border-t border-gray-200 pt-4 pb-3">
          <div class="flex items-center px-4">
            <div class="text-base text-gray-800">
              {{ $page.props.auth.user.name }}
            </div>
          </div>
          <div class="mt-3 space-y-1">
            <inertia-link
              :href="route('profile.index', user.username)"
              class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
              :class="{
                'border-indigo-500 bg-indigo-50 text-indigo-700': url() == user.username + '/profile',
              }"
            >
              Your profile
            </inertia-link>
            <inertia-link
              :href="route('userSettings.index', user.username)"
              class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium"
              :class="{
                'border-indigo-500 bg-indigo-50 text-indigo-700': url() == user.username + '/settings',
              }"
            >
              Settings
            </inertia-link>
            <inertia-link href="/logout" method="post" as="button" class="block px-4 py-2 text-base text-gray-500 hover:bg-gray-100 hover:text-gray-800"> Sign out </inertia-link>
          </div>
        </div>
      </DisclosurePanel>
    </Card>
  </Disclosure>
  <div class="mx-auto max-w-7xl p-2 sm:p-4 pt-0 pb-[55px] sm:pb-16 lg:px-0">
    <slot />
    <BottomMenu />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { BookmarkIcon, MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Head, usePage } from '@inertiajs/inertia-vue3'
import BottomMenu from '@/Components/BottomMenu.vue'
import FlashMessages from '@/Components/FlashMessages.vue'
import BetFormSlideOver from '@/PageComponents/BetFormSlideOver.vue'
import emitter from '@/Plugins/mitt'
import Search from '@/PageComponents/Search.vue'
import Card from '@/PageComponents/Card.vue'

const props = defineProps({
  errors: Object,
  bet: Object,
  title: String,
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

const searchOpen = ref(false)

const openBet = () => {
  emitter.emit('betForm:show')
}

const url = () => {
  return location.pathname.substr(1)
}

const closeSearch = () => {
  searchOpen.value = false
}
</script>

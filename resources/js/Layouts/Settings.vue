<template>
  <div class="mb-2 sm:hidden">
    <label for="tabs" class="sr-only">Select a tab</label>
    <select id="tabs" name="tabs" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-200/5 dark:bg-slate-800 sm:text-sm" @change="onDropdownTabChange">
      <option v-for="tab in tabs" :key="tab.name" :selected="tab.current">
        {{ tab.name }}
      </option>
    </select>
  </div>
  <div class="flex flex-col items-center">
    <div class="mb-4 w-full rounded-md bg-white leading-4 shadow dark:bg-slate-800 sm:overflow-hidden">
      <div class="hidden sm:block">
        <div class="border-b border-gray-200 dark:border-slate-200/5">
          <nav class="-mb-px flex justify-center space-x-8" aria-label="Tabs">
            <span v-for="tab in tabs" :key="tab.name" class="cursor-pointer capitalize" :class="[tab.current ? 'border-red-500 text-red-500' : 'border-transparent font-semibold text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-slate-200 dark:hover:border-slate-700', 'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium']" :aria-current="tab.current ? 'page' : undefined" @click="openTab(tab.name, tab)">
              {{ tab.name }}
            </span>
          </nav>
        </div>
      </div>

      <div class=""><slot /></div>
    </div>
  </div>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import pickBy from 'lodash/pickBy'
import { ref } from 'vue'

const currentTab = ref(null)
const tabs = ref([
  {
    name: 'Settings',
    route: route('userSettings.index', usePage().props.value.auth.user.username),
    current: false,
  },
  {
    name: 'Profile',
    route: route('profile.index', usePage().props.value.auth.user.username),
    current: false,
  },
])

const url = () => {
  return location.pathname.substr(1).split('/').pop()
}

const changeTab = (value, tab) => {
  currentTab.value = tabs.value.find((tab) => tab.name == value)
  tabs.value.forEach((tab) => {
    if (tab.name.toLowerCase() == url().toLowerCase()) {
      tab.current = false
    }
  })
  tab.current = true
  Inertia.get(
    tab.route,
    pickBy({
      type: currentTab.value.option,
    }),
  )
}

const openTab = (value, tab) => {
  changeTab(value, tab)
}

const setCurrentTab = () => {
  tabs.value.forEach((tab) => {
    if (tab.name.toLowerCase() == url().toLowerCase()) {
      tab.current = true
    }
  })
}
setCurrentTab()

const onDropdownTabChange = (event) => {
  var tab = tabs.value.find((tab) => tab.name == event.target.value)
  changeTab(event.target.value, tab)
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>

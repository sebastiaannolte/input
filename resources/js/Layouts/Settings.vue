<template>
  <div class="sm:hidden mb-2">
    <label for="tabs" class="sr-only">Select a tab</label>
    <select
      id="tabs"
      name="tabs"
      class="
        block
        w-full
        pl-3
        pr-10
        py-2
        text-base
        border-gray-300
        focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
        sm:text-sm
        rounded-md
      "
      @change="onDropdownTabChange"
    >
      <option v-for="tab in tabs" :key="tab.name" :selected="tab.current">
        {{ tab.name }}
      </option>
    </select>
  </div>
  <div class="flex flex-col items-center">
    <div
      class="
        mb-4
        leading-4
        bg-white
        shadow
        rounded-md
        sm:overflow-hidden
        w-full
      "
    >
      <div class="hidden sm:block">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8 justify-center" aria-label="Tabs">
            <span
              v-for="tab in tabs"
              :key="tab.name"
              class="cursor-pointer capitalize"
              :class="[
                tab.current
                  ? 'border-indigo-500 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              ]"
              :aria-current="tab.current ? 'page' : undefined"
              @click="openTab(tab.name, tab)"
            >
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
    route: route(
      'userSettings.index',
      usePage().props.value.auth.user.username,
    ),
    current: false,
  },
  {
    name: 'Profile',
    route: route(
      'profile.index',
      usePage().props.value.auth.user.username,
    ),
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

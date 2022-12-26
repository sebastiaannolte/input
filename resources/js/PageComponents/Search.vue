<template>
  <TransitionRoot :show="open" as="template" @after-leave="filters.query = ''" appear>
    <Dialog as="div" class="relative z-10" @close="close">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="ease-in duration-200" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
          <DialogPanel class="mx-auto max-w-3xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all dark:divide-gray-700 dark:bg-gray-800">
            <Combobox v-slot="{ activeOption }" @update:modelValue="onSelect" v-model="filters.query">
              <div class="relative">
                <MagnifyingGlassIcon class="pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-400" aria-hidden="true" />
                <ComboboxInput autocomplete="off" class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-800 placeholder-gray-400 focus:ring-0 dark:text-gray-400 sm:text-sm" placeholder="Search..." @input="search($event.target.value)" />
                <span v-if="loading" class="btn-spinner pointer-events-none !absolute top-4 right-4 h-5 w-5 dark:text-gray-400" aria-hidden="true"></span>
              </div>

              <div class="flex items-center justify-between px-4 py-2" v-if="filteredItems.length > 0 || filters.query">
                <div>
                  <p class="hidden text-xs text-gray-700 dark:text-slate-400 sm:block" v-if="filteredItems.length > 0 && !loading">
                    Showing
                    {{ ' ' }}
                    <span class="font-medium">{{ pagination.from }}</span>
                    {{ ' ' }}
                    to
                    {{ ' ' }}
                    <span class="font-medium">{{ pagination.to }}</span>
                    {{ ' ' }}
                    of
                    {{ ' ' }}
                    <span class="font-medium">{{ pagination.total }}</span>
                    {{ ' ' }}
                    results
                  </p>
                </div>

                <span class="flex items-center gap-3">
                  <ul class="flex flex-wrap text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                    <li class="mr-2">
                      <span @click="filters.type = 'event'" class="inline-block cursor-pointer rounded-lg py-1.5 px-2" :class="filters.type == 'event' ? 'bg-blue-600 dark:text-gray-300' : 'dark:text-gray-400 hover:dark:text-gray-300'">Event</span>
                    </li>
                    <li class="mr-2">
                      <span @click="filters.type = 'competition'" class="inline-block cursor-pointer rounded-lg py-1.5 px-2" :class="filters.type == 'competition' ? 'bg-blue-600 dark:text-gray-300' : 'dark:text-gray-400 hover:dark:text-gray-300'">Competition</span>
                    </li>
                  </ul>
                  <span>
                    <Switch v-model="filters.allBets" :class="filters.allBets ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'" class="relative inline-flex h-3 w-6 items-center rounded-full">
                      <span class="sr-only">Enable notifications</span>
                      <span :class="filters.allBets ? 'translate-x-3' : 'translate-x-1'" class="inline-block h-2 w-2 transform rounded-full bg-white transition" />
                    </Switch>
                    <span class="ml-1 text-xs">All bets</span>
                  </span>
                </span>
              </div>

              <ComboboxOptions v-if="filteredItems.length > 0 && !loading" class="flex divide-x divide-gray-100 dark:divide-gray-700 sm:flex" as="div" static hold>
                <div :class="['flex max-h-96 min-w-0 flex-auto scroll-py-4 flex-col justify-between overflow-y-auto px-6 py-4', activeOption && 'sm:h-96']">
                  <div hold class="-mx-2 text-sm text-gray-700">
                    <ComboboxOption v-for="item in filteredItems" :key="item.id" :value="item" as="template" v-slot="{ active }">
                      <div :class="['group flex cursor-default select-none items-center rounded-md p-2 dark:text-gray-400', active && 'bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-400']">
                        <img v-if="item.icon" :src="item.icon" alt="" class="h-6 w-6 flex-none" />
                        <div v-else class="h-6 w-6 flex-none"></div>
                        <span class="ml-3 flex-auto truncate">{{ item.match }}</span>
                        <ChevronRightIcon v-if="active" class="ml-3 h-5 w-5 flex-none text-gray-400" aria-hidden="true" />
                      </div>
                    </ComboboxOption>
                  </div>
                  <div class="flex justify-between">
                    <div v-if="filters.page == 1" class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-2 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-200/20 dark:bg-slate-400/20 dark:text-slate-400 dark:hover:bg-slate-400/20"><ChevronLeftIcon class="h-3 w-3" aria-hidden="true" /></div>
                    <div v-else preserve-scroll @click="filters.page = filters.page - 1" class="relative inline-flex cursor-pointer items-center rounded-md border border-gray-300 bg-white px-2 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-200/20 dark:bg-slate-400/10 dark:text-slate-400 dark:hover:bg-slate-400/20"><ChevronLeftIcon class="h-3 w-3" aria-hidden="true" /></div>
                    <div v-if="filters.page == pagination.lastPage" class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-2 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-200/20 dark:bg-slate-400/20 dark:text-slate-400 dark:hover:bg-slate-400/20"><ChevronRightIcon class="h-3 w-3" aria-hidden="true" /></div>
                    <div v-else preserve-scroll @click="filters.page = filters.page + 1" class="relative inline-flex cursor-pointer items-center rounded-md border border-gray-300 bg-white px-2 py-1 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-200/20 dark:bg-slate-400/10 dark:text-slate-400 dark:hover:bg-slate-400/20"><ChevronRightIcon class="h-3 w-3" aria-hidden="true" /></div>
                  </div>
                </div>
                <div v-if="activeOption" class="hidden h-96 w-1/2 flex-none flex-col divide-y divide-gray-100 overflow-y-auto dark:divide-gray-700 sm:flex">
                  <div class="flex-none p-6 text-center">
                    <img v-if="activeOption.icon" :src="activeOption.icon" alt="" class="mx-auto h-16 w-16" />
                    <div v-else class="mx-auto h-16 w-16"></div>
                    <h2 class="mt-3 flex items-center justify-center truncate font-semibold text-gray-900 dark:text-gray-400">
                      {{ activeOption.match }} <span class="ml-2"><StatusLabel :status="activeOption.status" :result="activeOption.result"></StatusLabel></span>
                    </h2>
                  </div>
                  <div class="flex flex-auto flex-col justify-between p-6">
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm text-gray-700 dark:text-gray-400">
                      <dt class="col-end-1 font-semibold text-gray-900 dark:text-gray-300">Date</dt>
                      <dd>{{ activeOption.date }}</dd>
                      <dt class="col-end-1 font-semibold text-gray-900 dark:text-gray-300">Selection</dt>
                      <dd>{{ activeOption.selection }}</dd>
                      <dt class="col-end-1 font-semibold text-gray-900 dark:text-gray-300">Odds</dt>
                      <dd>{{ activeOption.odds }}</dd>
                      <dt class="col-end-1 font-semibold text-gray-900 dark:text-gray-300">Stake</dt>
                      <dd>{{ activeOption.stake }}</dd>
                    </dl>
                    <div class="mt-6 flex gap-3">
                      <Link :href="activeOption.url" class="w-full rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-center text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Go to bet</Link>
                    </div>
                  </div>
                </div>
              </ComboboxOptions>

              <div v-if="filters.query !== '' && filteredItems.length === 0 && !loading" class="py-14 px-6 text-center text-sm sm:px-14">
                <UsersIcon class="mx-auto h-6 w-6 text-gray-400 dark:text-gray-400" aria-hidden="true" />
                <p class="mt-4 font-semibold text-gray-900 dark:text-gray-400">No bets found</p>
                <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn’t find anything with that term. Please try again.</p>
              </div>
            </Combobox>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { Switch } from '@headlessui/vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { ChevronRightIcon, ChevronLeftIcon, UsersIcon } from '@heroicons/vue/20/solid'
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia'
import { Link, useForm } from '@inertiajs/inertia-vue3'
import StatusLabel from '@/Components/StatusLabel.vue'

const emit = defineEmits()
const loading = ref(false)
const filterData = ref({})
const filteredItems = ref([])
const pagination = ref({})

const filters = reactive({
  query: '',
  allBets: false,
  page: 1,
  type: 'event',
})

watch(
  () => [filters.allBets, filters.page, filters.type],
  (value) => {
    console.log('123')
    search(filters.query)
  },
  { deep: true },
)

const props = defineProps({
  open: Boolean,
})

function onSelect(item) {
  Inertia.visit(item.url)
}

function timeout(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms))
}
const search = async (q) => {
  console.log('search!!!')
  loading.value = true

  if (q.length < 3) {
    loading.value = false
    filteredItems.value = []
    return
  }

  await timeout(1000)

  filters.query = q

  if (q) {
    const data = await axios.post(route('event.global-search'), filters)
    filteredItems.value = Object.values(data.data.items)
    filterData.value = data.data
    delete filterData.value['items']
    const perPage = 8
    const from = perPage * filters.page - perPage + 1
    const to = from + (perPage + from > filterData.value.totalResults ? filterData.value.totalResults - from : perPage - 1)
    pagination.value = {
      from,
      to,
      total: filterData.value.totalResults,
      lastPage: Math.ceil(filterData.value.totalResults / perPage),
    }
  } else {
    filteredItems.value = []
  }
  loading.value = false
}

const close = () => {
  emit('close')
}
</script>

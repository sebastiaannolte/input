<template>
  <Layout :title="title" :errors="errors">
    <div class="mb-2 grid grid-cols-1 gap-2 sm:mb-4 sm:grid-cols-2 sm:gap-4">
      <div class="w-full rounded-md bg-white p-4 px-4 text-center leading-4 shadow dark:bg-slate-800 sm:overflow-hidden">
        <div>
          <div class="mb-2 flex flex-col">
            <span class="dark:text-slate-200">ROI</span>
            <span class="text-xl font-bold">{{ generalStats.roi }}%</span>
          </div>
        </div>
        <div>
          <div class="flex flex-row">
            <div class="flex flex-1 flex-col justify-between">
              <span class="mb-2 dark:text-slate-200">Bets</span>
              <span class="font-bold">{{ generalStats.bets }}</span>
            </div>
            <div class="flex flex-1 flex-col justify-between">
              <span class="mb-2 dark:text-slate-200">Total stake</span>
              <span class="font-bold">{{ generalStats.totalStake }}</span>
            </div>
            <div class="flex flex-1 flex-col justify-between">
              <span class="mb-2 dark:text-slate-200">Win rate</span>
              <span class="font-bold">{{ generalStats.winRate }}%</span>
            </div>
          </div>
        </div>
      </div>
      <div class="w-full rounded-md bg-white p-4 px-4 text-center leading-4 shadow dark:bg-slate-800 sm:overflow-hidden">
        <div>
          <div class="mb-4 flex flex-col">
            <span class="dark:text-slate-200">Profit</span>
            <span class="text-xl font-bold">{{ generalStats.profit }} units</span>
          </div>
        </div>
        <div>
          <div class="flex flex-row">
            <div class="flex flex-1 flex-col justify-between">
              <span class="mb-2 dark:text-slate-200">Avg stake</span>
              <span class="font-bold">{{ generalStats.avgStake }}</span>
            </div>
            <div class="flex flex-1 flex-col justify-between">
              <span class="mb-2 dark:text-slate-200">Avg odds</span>
              <span class="font-bold">{{ generalStats.avgOdds }}</span>
            </div>
            <div class="flex flex-1 flex-col justify-between">
              <span class="mb-2 dark:text-slate-200">Avg odds/stake</span>
              <span class="font-bold">{{ generalStats.avgOddsStake }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <active-filters :prop-filters="filters" :filter-route="filterRoute" />

    <div class="flex flex-col items-center">
      <div class="mb-2 w-full sm:hidden">
        <label for="tabs" class="sr-only">Select a tab</label>
        <select id="tabs" name="tabs" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base capitalize focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-200/5 dark:bg-slate-800 sm:text-sm" @change="onDropdownTabChange">
          <option v-for="tab in generatedTabs" :key="tab.option" class="capitalize" :selected="tab.current">
            {{ tab.option }}
          </option>
        </select>
      </div>
      <div class="mb-4 w-full rounded-md bg-white text-center leading-4 shadow dark:bg-slate-800 sm:overflow-hidden">
        <div class="hidden sm:block">
          <div class="border-b border-gray-200 border-slate-200/5">
            <nav class="-mb-px flex justify-center space-x-8" aria-label="Tabs">
              <span v-for="tab in generatedTabs" :key="tab.option" class="cursor-pointer capitalize" :class="[tab.current ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-slate-200', 'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium']" :aria-current="tab.current ? 'page' : undefined" @click="openTab(tab.option, tab)">
                {{ tab.option }}
              </span>
            </nav>
          </div>
        </div>
        <div
          class="grid gap-4 p-4"
          :class="{
            'grid-cols-1': !currentTable.head,
            'grid-cols-1 sm:grid-cols-2': currentTable.head,
          }"
        >
          <div class="h-full w-full flex-col">
            <Line :chart-data="currentGraph.data" :chart-options="chartOptions" :height="400" />
          </div>
          <div v-if="currentTable.head" class="w-full overflow-x-auto">
            <div class="inline-block min-w-full py-2 align-middle">
              <div class="overflow-hidden rounded-md shadow">
                <table class="min-w-full divide-y divide-gray-200 border dark:divide-slate-200/5 dark:border-slate-200/5">
                  <thead class="bg-gray-50 dark:bg-slate-800">
                    <th
                      v-for="(values, keys) in currentTable.head"
                      :key="keys"
                      scope="col"
                      class="t-header px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-slate-200"
                      :class="{
                        'arrow-down': sort.sortType == values && sort.sortOrder == 'DESC',
                        'arrow-up': sort.sortType == values && sort.sortOrder == 'ASC',
                      }"
                      @click="sortHeader(values)"
                    >
                      {{ values }}
                    </th>
                  </thead>
                  <tbody class="dark:bg-slate-900">
                    <tr v-for="(values, key) in currentTable.body" :key="key" :class="key % 2 === 1 ? 'bg-white dark:bg-slate-800' : 'bg-gray-50  dark:bg-slate-800/80'">
                      <td v-for="(values, key) in values" :key="key" class="whitespace-nowrap px-6 py-2 text-left text-sm font-medium text-gray-900 first:font-bold dark:text-slate-400">{{ values.value }}{{ values.type }}</td>
                    </tr>
                  </tbody>
                </table>
                <div v-if="currentTable && currentTable.body && currentTable.body.length == 0" class="col-span-1 whitespace-nowrap bg-white px-6 py-4 text-center text-sm font-medium text-gray-900 dark:bg-slate-800 dark:text-slate-400">No results</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Authenticated.vue'
import pickBy from 'lodash/pickBy'
import ActiveFilters from '@/PageComponents/ActiveFilters.vue'
import { Inertia } from '@inertiajs/inertia'
import { Line } from 'vue-chartjs'
import emitter from '@/Plugins/mitt'
import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'

const props = defineProps({
  generalStats: Object,
  tabs: Array,
  filters: {
    type: Array,
    default: [],
  },
  type: Number,
  sort: Object,
  gamesStatus: Boolean,
  stats: Object,
  errors: Object,
})

const title = ref(null)
const generatedTabs = ref([])
const chartOptions = ref({
  maintainAspectRatio: false,
  plugins: {
    legend: {
      labels: {
        color: '#e2e8f0',
      },
    },
  },
  scales: {
    y: {
      ticks: {
        color: '#e2e8f0',
      },
    },
    x: {
      ticks: {
        color: '#e2e8f0',
      },
    },
  },
  interaction: { mode: 'index', intersect: 'true' },
  animation: { duration: '0' },
})
const currentTab = ref(null)
const filterRoute = ref(null)
const currentGraph = computed(() => props.stats.graph)
const currentTable = computed(() => props.stats.table)

const createTabs = () => {
  for (const key in props.tabs) {
    var tab = props.tabs[key]
    var current = false
    var localTab = {
      option: tab,
      current: current,
    }
    if (tab == props.type) {
      localTab.current = true
      currentTab.value = localTab
    }
    generatedTabs.value.push(localTab)
  }
}

createTabs()
const openTab = (value) => {
  changeTab(value)
}

const onDropdownTabChange = (event) => {
  changeTab(event.target.value)
}

const changeTab = (value) => {
  props.sort.sortType = 'bets'
  currentTab.value = generatedTabs.value.find((tab) => tab.option == value)
  Inertia.get(
    route('stats.index', usePage().props.value.userInfo.user.username),
    pickBy({
      type: currentTab.value.option,
      filters: null, // reset filters if tab changes
    }),
    {
      preserveScroll: true,
      only: ['type', 'filters', 'stats'],
    },
  )
}

const getStats = () => {
  Inertia.get(
    route('stats.index', usePage().props.value.userInfo.user.username),
    pickBy({
      type: currentTab.value.option,
      sort: props.sort,
      filters: props.filters,
    }),
    {
      only: ['type', 'sort', 'filters', 'stats'],
    },
  )
}

const sortHeader = (tableHeader) => {
  if (currentTable.value.body.length < 2) {
    return
  }

  if (props.sort.sortType != tableHeader) {
    props.sort.sortOrder = 'DESC'
  } else {
    props.sort.sortOrder = props.sort.sortOrder == 'DESC' ? 'ASC' : 'DESC'
  }
  props.sort.sortType = tableHeader

  getStats()
}

const setPageTitle = () => {
  title.value = 'Your stats'
  if (!usePage().props.value.userInfo.myPage) {
    title.value = usePage().props.value.userInfo.user.name + "'s stats"
  }
}

setPageTitle()

filterRoute.value = route('stats.index', {
  username: usePage().props.value.userInfo.user.username,
  _query: pickBy({ type: props.type, sort: props.sort }),
})

emitter.on('filter:submit', () => {
  // this.getStats();
})
</script>

<template>

  <Head :title="title" />
  <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="w-full rounded-md bg-white p-4 px-4 text-center leading-4 shadow sm:overflow-hidden">
      <div>
        <div class="mb-4 flex flex-col">
          <span>ROI</span>
          <span class="text-xl font-bold">{{ generalStats.roi }}%</span>
        </div>
      </div>
      <div>
        <div class="flex flex-row">
          <div class="flex flex-1 flex-col justify-between">
            <span class="mb-2">Bets</span>
            <span class="font-bold">{{ generalStats.bets }}</span>
          </div>
          <div class="flex flex-1 flex-col justify-between">
            <span class="mb-2">Total stake</span>
            <span class="font-bold">{{ generalStats.totalStake }}</span>
          </div>
          <div class="flex flex-1 flex-col justify-between">
            <span class="mb-2">Win rate</span>
            <span class="font-bold">{{ generalStats.winRate }}%</span>
          </div>
        </div>
      </div>
    </div>
    <div class="w-full rounded-md bg-white p-4 px-4 text-center leading-4 shadow sm:overflow-hidden">
      <div>
        <div class="mb-4 flex flex-col">
          <span>Profit</span>
          <span class="text-xl font-bold">{{ generalStats.profit }} units</span>
        </div>
      </div>
      <div>
        <div class="flex flex-row">
          <div class="flex flex-1 flex-col justify-between">
            <span class="mb-2">Avg stake</span>
            <span class="font-bold">{{ generalStats.avgStake }}</span>
          </div>
          <div class="flex flex-1 flex-col justify-between">
            <span class="mb-2">Avg odds</span>
            <span class="font-bold">{{ generalStats.avgOdds }}</span>
          </div>
          <div class="flex flex-1 flex-col justify-between">
            <span class="mb-2">Avg odds/stake</span>
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
      <select id="tabs" name="tabs" @change="onDropdownTabChange" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base capitalize focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
        <option v-for="tab in generatedTabs" :key="tab.option" class="capitalize" :selected="tab.current">
          {{ tab.option }}
        </option>
      </select>
    </div>
    <div class="mb-4 w-full rounded-md bg-white text-center leading-4 shadow sm:overflow-hidden">
      <!-- md:w-2/3 -->
      <div class="hidden sm:block">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex justify-center space-x-8" aria-label="Tabs">
            <span v-for="tab in generatedTabs" :key="tab.option" class="cursor-pointer capitalize" :class="[
              tab.current
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
            ]" :aria-current="tab.current ? 'page' : undefined" @click="openTab(tab.option, tab)">
              {{ tab.option }}
            </span>
          </nav>
        </div>
      </div>
      <div class="relative" :style="loading ? 'height: 500px' : ''">
        <loading z-index="10" v-model:active="loading" :is-full-page="false" />
      </div>
      <div class="grid gap-4 p-4" :class="{
        'grid-cols-1': !currentTable.head,
        'grid-cols-1 sm:grid-cols-2': currentTable.head,
      }">
        <div class="h-full w-full flex-col">


          <Line :chart-data="currentGraph.data"
            :chart-options='{ "maintainAspectRatio": false, "interaction": { "mode": "index", "intersect": "true" }, "animation": { "duration": "0" } }'
            :height="400" />
        </div>
        <div class="w-full overflow-x-auto" v-if="currentTable.head">
          <div class="inline-block min-w-full py-2 align-middle">
            <div class="overflow-hidden rounded-md border-b border-gray-200 shadow">
              <table class="min-w-full divide-y divide-gray-200" v-if="!loading">
                <thead class="bg-gray-50">
                  <th scope="col" class="t-header px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500" :key="keys" v-for="(values, keys) in currentTable.head" :class="{
                      'arrow-down':
                        sort.sortType == values && sort.sortOrder == 'DESC',
                      'arrow-up':
                        sort.sortType == values && sort.sortOrder == 'ASC',
                    }" @click="sortHeader(values)">
                    {{ values }}
                  </th>
                </thead>
                <tbody>
                  <tr v-for="(values, key) in currentTable.body" :key="key"
                    :class="key % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                    <td class="whitespace-nowrap px-6 py-2 text-left text-sm font-medium text-gray-900 first:font-bold" v-for="(values, key) in values" :key="key">
                      {{ values.value }}{{ values.type }}
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="
  currentTable &&
  currentTable.body &&
  currentTable.body.length == 0
              " class="col-span-1 whitespace-nowrap bg-white px-6 py-4 text-center text-sm font-medium text-gray-900">
                No results
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated.vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import pickBy from "lodash/pickBy";
import ShowFilterButton from "@/Components/ShowFilterButton.vue";
import ActiveFilters from "@/PageComponents/ActiveFilters.vue";
import { Inertia } from "@inertiajs/inertia";
import { Line } from 'vue-chartjs'

export default {
  layout: Layout,
  components: {
    Loading,
    ShowFilterButton,
    ActiveFilters,
    Line
  },
  props: {
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
  },

  data() {
    return {
      currentGraph: null,
      currentTable: {
        head: null,
        body: null,
      },
      generatedTabs: [],
      loading: false,
      localFilters: {},
      currentTab: null,
      filterRoute: null,
    };
  },

  created() {
    this.createTabs();
    this.filterRoute = this.route("stats.index", {
      username: this.$page.props.userInfo.user.username,
      _query: pickBy({ type: this.type, sort: this.sort }),
    });

    this.emitter.on("filter:submit", () => {
      // this.getStats();
    });

    this.currentGraph = this.stats.graph;
    this.currentTable = this.stats.table;
    this.setPageTitle();
  },

  methods: {
    getStats() {
      Inertia.get(
        this.route("stats.index", this.$page.props.userInfo.user.username),
        pickBy({
          type: this.currentTab.option,
          sort: this.sort,
          filters: this.filters,
        }),
        {
          only: ["type", "sort", "filters", "stats"],
        }
      );
    },

    setPageTitle() {
      this.title = "Your stats";
      if (!this.$page.props.userInfo.myPage) {
        this.title = this.$page.props.userInfo.user.name + "'s stats";
      }
    },
    openTab(value) {
      this.changeTab(value);
    },

    onDropdownTabChange(event) {
      this.changeTab(event.target.value);
    },

    changeTab(value) {
      this.sort.sortType = "bets";
      this.currentTab = this.generatedTabs.find((tab) => tab.option == value);
      Inertia.get(
        this.route("stats.index", this.$page.props.userInfo.user.username),
        pickBy({
          type: this.currentTab.option,
          filters: null, // reset filters if tab changes
        }),
        {
          preserveScroll: true,
          only: ["type", "filters", "stats"],
        }
      );
    },

    createTabs() {
      for (const key in this.tabs) {
        var tab = this.tabs[key];
        var current = false;
        var localTab = {
          option: tab,
          current: current,
        };
        if (tab == this.type) {
          localTab.current = true;
          this.currentTab = localTab;
        }
        this.generatedTabs.push(localTab);
      }
    },

    sortHeader(tableHeader) {
      if (this.currentTable.body.length < 2) {
        return;
      }

      if (this.sort.sortType != tableHeader) {
        this.sort.sortOrder = "DESC";
      } else {
        this.sort.sortOrder = this.sort.sortOrder == "DESC" ? "ASC" : "DESC";
      }
      this.sort.sortType = tableHeader;

      this.getStats();
    },

    changeStatus(status) {
      Inertia.get(
        this.route("stats.index", this.$page.props.userInfo.user.username),
        pickBy({
          type: this.currentTab.option,
          sort: this.sort,
          filters: this.filters,
        }),
        {
          preserveScroll: true,
        },
        {
          only: ["type", "sort", "filters"],
        }
      );
    },
  },
};
</script>

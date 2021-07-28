<template>
  <Head :title="title" />
  <div class="grid grid-cols-2 gap-4">
    <div
      class="
        px-4
        mb-5
        leading-4
        text-center
        bg-white
        p-4
        shadow
        rounded-md
        sm:overflow-hidden
        w-full
      "
    >
      <div>
        <div class="flex flex-col mb-5">
          <span>ROI</span>
          <span class="text-xl font-bold">{{ stats.roi }}%</span>
        </div>
      </div>
      <div>
        <div class="flex flex-row">
          <div class="flex-1 flex flex-col justify-between">
            <span class="mb-2">Bets</span>
            <span class="font-bold">{{ stats.bets }}</span>
          </div>
          <div class="flex-1 flex flex-col justify-between">
            <span class="mb-2">Total stake</span>
            <span class="font-bold">{{ stats.totalStake }}</span>
          </div>
          <div class="flex-1 flex flex-col justify-between">
            <span class="mb-2">Win rate</span>
            <span class="font-bold">{{ stats.winRate }}%</span>
          </div>
        </div>
      </div>
    </div>
    <div
      class="
        px-4
        mb-5
        leading-4
        text-center
        bg-white
        p-4
        shadow
        rounded-md
        sm:overflow-hidden
        w-full
      "
    >
      <div>
        <div class="flex flex-col mb-5">
          <span>Profit</span>
          <span class="text-xl font-bold">{{ stats.profit }} units</span>
        </div>
      </div>
      <div>
        <div class="flex flex-row">
          <div class="flex-1 flex flex-col justify-between">
            <span class="mb-2">Avg stake</span>
            <span class="font-bold">{{ stats.avgStake }}</span>
          </div>
          <div class="flex-1 flex flex-col justify-between">
            <span class="mb-2">Avg odds</span>
            <span class="font-bold">{{ stats.avgOdds }}</span>
          </div>
          <div class="flex-1 flex flex-col justify-between">
            <span class="mb-2">Avg result</span>
            <span class="font-bold">{{ stats.avgResult }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div
    class="
      px-4
      w-full
      font-light
      leading-4
      text-gray-800
      cursor-default
      flex
      justify-end
    "
  >
    <p
      align="right"
      class="p-0 mx-0 mt-0 mb-5 text-base text-gray-800 box-border"
    >
      <button
        type="button"
        @click.prevent="showFilter"
        id="filterswitch"
        class="
          m-0
          text-red-500
          no-underline
          box-border
          hover:text-red-500
          focus:text-red-500
        "
      >
        <template v-if="!filterStatus">Show filter</template>
        <template v-else>Hide filter</template>
      </button>
      >
    </p>
  </div>
  <Filters
    :prop-filters="filters"
    @filterSubmit="handleFilter"
    v-show="filterStatus"
  />
  <div class="flex flex-col items-center">
    <div
      class="
        mb-5
        leading-4
        text-center
        bg-white
        shadow
        rounded-md
        sm:overflow-hidden
        w-full
        md:w-2/3
      "
    >
      <div class="sm:hidden mb-5">
        <label for="tabs" class="sr-only">Select a tab</label>
        <select
          id="tabs"
          name="tabs"
          @change="onDropdownTabChange"
          class="
            block
            w-full
            pl-3
            pr-10
            py-2
            text-base
            border-gray-300
            focus:outline-none
            focus:ring-indigo-500
            focus:border-indigo-500
            sm:text-sm
            rounded-md
          "
        >
          <option
            v-for="tab in generatedTabs"
            :key="tab.option"
            class="capitalize"
            :selected="tab.current"
          >
            {{ tab.option }}
          </option>
        </select>
      </div>
      <div class="hidden sm:block mb-5">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8 justify-center" aria-label="Tabs">
            <span
              v-for="tab in generatedTabs"
              :key="tab.option"
              class="cursor-pointer capitalize"
              :class="[
                tab.current
                  ? 'border-indigo-500 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              ]"
              :aria-current="tab.current ? 'page' : undefined"
              @click="openTab(tab.option, tab)"
            >
              {{ tab.option }}
            </span>
          </nav>
        </div>
      </div>
      <div class="relative" style="height: 500px" v-if="loading">
        <loading v-model:active="loading" :is-full-page="false" />
      </div>
      <vue3-chart-js
        v-if="!loading"
        :type="selectedTab.type"
        :data="selectedTab.data"
        :options="selectedTab.options"
        class="mb-5 p-2 md:p-4"
      />
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div
            class="shadow overflow-hidden border-b border-gray-200 rounded-md"
          >
            <table class="min-w-full divide-y divide-gray-200" v-if="!loading">
              <thead class="bg-gray-50">
                <th
                  scope="col"
                  class="
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  "
                  :key="keys"
                  v-for="(values, keys) in currentTable.head"
                >
                  {{ values }}
                </th>
              </thead>
              <tbody>
                <tr
                  v-for="(values, key) in currentTable.body"
                  :key="key"
                  :class="key % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td
                    class="
                      first:font-bold
                      px-6
                      py-2
                      whitespace-nowrap
                      text-sm
                      font-medium
                      text-gray-900 text-left
                    "
                    v-for="(values, key) in values"
                    :key="key"
                  >
                    {{ values }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import Layout from "@/Layouts/Authenticated";
import Vue3ChartJs from "@j-t-mcc/vue3-chartjs";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import Filters from "@/Components/Filters";
import pickBy from "lodash/pickBy";

export default {
  layout: Layout,
  components: {
    Vue3ChartJs,
    Loading,
    Filters,
  },
  props: {
    stats: Object,
    tabs: Array,
    filters: Array,
    showFilter: Boolean,
  },

  data() {
    return {
      selectedTab: null,
      currentTable: null,
      generatedTabs: [],
      loading: false,
      localFilters: {},
      filterStatus: false,
    };
  },

  created() {
    this.createTabs();
    this.getStats("tipster");
    this.setPageTitle();

    if (this.showFilter == true) {
      this.filterStatus = true;
    }
  },

  methods: {
    getStats(key) {
      this.loading = true;
      this.$http
        .post(this.route("stats.stats"), {
          key: key,
          filters: this.localFilters,
        })
        .then((response) => {
          if (response.data) {
            this.selectedTab = response.data[key].graph;
            this.currentTable = response.data[key].table;
            this.loading = false;
          }
        });
    },

    handleFilter(filters) {
      var localFilters = {};
      this.localFilters = filters;
      var currentTab = this.generatedTabs.find((tab) => tab.current == true);
      this.getStats(currentTab.option);

      for (const key in this.localFilters) {
        var filter = this.localFilters[key];

        if (filter.value) {
          localFilters[key] = filter;
        }
      }

      if (Object.keys(localFilters).length == 0) {
        this.filterStatus = false;
      }

      this.$inertia.get(
        this.route("stats.index", this.$page.props.auth.user.username),
        pickBy({ filters: localFilters, showFilter: this.filterStatus }),
        {
          preserveState: true,
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
    showFilter() {
      this.filterStatus = !this.filterStatus;
    },

    onDropdownTabChange(event) {
      this.changeTab(event.target.value);
    },

    changeTab(value) {
      this.getStats(value);
      this.generatedTabs.forEach((tab) => {
        if (tab.option == value) {
          return (tab.current = true);
        }
        tab.current = false;
      });
    },

    createTabs() {
      for (const key in this.tabs) {
        var tab = this.tabs[key];
        this.generatedTabs.push({ option: tab, current: false });
      }

      this.generatedTabs[0].current = true;
    },
  },
};
</script>

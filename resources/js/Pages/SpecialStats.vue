<template>
  <Head :title="title" />
  <table-filter-header title="Special stats" />
  <active-filters :prop-filters="filters" :filter-route="filterRoute"  />
  <div class="flex flex-col items-center">
    <div class="mb-2 w-full sm:hidden">
      <label for="tabs" class="sr-only">Select a tab</label>
      <select
        id="tabs"
        name="tabs"
        @change="onDropdownTabChange"
        class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base capitalize focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
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
    <div
      class="w-full rounded-md bg-white text-center leading-4 shadow sm:overflow-hidden"
    >
      <div class="hidden sm:block">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex justify-center space-x-8" aria-label="Tabs">
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
      <div class="relative" :style="loading ? 'height: 500px' : ''">
        <loading z-index="10" v-model:active="loading" :is-full-page="false" />
      </div>
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <div
            class="overflow-hidden rounded-md border-b border-gray-200 shadow"
          >
            <table class="min-w-full divide-y divide-gray-200" v-if="!loading">
              <thead class="bg-gray-50">
                <th
                  scope="col"
                  class="t-header px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                  :key="keys"
                  v-for="(values, keys) in currentTable.head"
                  :class="{
                    'arrow-down':
                      sort.sortType == values &&
                      sort.sortOrder == 'DESC',
                    'arrow-up':
                      sort.sortType == values &&
                      sort.sortOrder == 'ASC',
                  }"
                  @click="sortHeader(values)"
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
                    class="whitespace-nowrap px-6 py-2 text-left text-sm font-medium text-gray-900 first:font-bold"
                    v-for="(values, key) in values"
                    :key="key"
                  >
                    <button
                      @click="goTo(currentTab.route, values.specialId)"
                      v-if="values.specialId"
                    >
                      {{ values.value }}
                    </button>
                    <span v-else> {{ values.value }}{{ values.type }} </span>
                  </td>
                </tr>
              </tbody>
            </table>
            <div
              v-if="
                currentTable &&
                currentTable.body &&
                currentTable.body.length == 0
              "
              class="col-span-1 whitespace-nowrap bg-white px-6 py-4 text-center text-sm font-medium text-gray-900"
            >
              No results
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <pagination
    v-if="currentTable && currentTable.body && currentTable.body.length > 0"
    :data="currentTable.body"
    :per-page-prop="pagination.perPage"
    :total-results-prop="pagination.totalResults"
    :custom="true"
    :type-id="currentTab ? currentTab.option : ''"
  />
</template>

<script>
import Layout from "@/Layouts/Authenticated.vue";
import ActiveFilters from "@/PageComponents/ActiveFilters.vue";
import pickBy from "lodash/pickBy";
import TableFilterHeader from "@/PageComponents/TableFilterHeader.vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import Pagination from "@/PageComponents/Pagination.vue";
import { Inertia } from "@inertiajs/inertia";

export default {
  layout: Layout,
  components: {
    TableFilterHeader,
    Loading,
    Pagination,
    ActiveFilters,
  },
  props: {
    filters: Array,
    tabs: Array,
    type: String,
    sort: Object,
    stats: Object,
  },

  data() {
    return {
      currentTable: {
        head: null,
        body: null,
      },
      generatedTabs: [],
      localFilters: {},
      loading: false,
      pagination: {
        totalResults: null,
        perPage: null,
      },
      currentTab: null,
    };
  },

  created() {
    this.filterRoute = this.route("special", {
      username: this.$page.props.userInfo.user.username,
      _query: pickBy({ type: this.type, sort: this.sort }),
    });

    this.createTabs();
    this.emitter.on("filter:submit", () => {
      // this.getStats();
    });

    this.currentTable = this.stats.table;
    this.pagination.totalResults = this.stats.totalResults;
    this.pagination.perPage = this.stats.perPage;
    this.setPageTitle();
  },

  methods: {
    getStats() {
      Inertia.get(
        this.route("special", this.$page.props.userInfo.user.username),
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

    goTo(route, id) {
      this.$inertia.get(
        this.route(route, [this.$page.props.userInfo.user.username, id]),
        pickBy({
          filters: this.filters,
        }),
        {
          preserveScroll: true,
          preserveState: true,
        }
      );
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
        this.route("special", this.$page.props.userInfo.user.username),
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
          option: tab.name,
          current: current,
          route: tab.route,
        };
        if (tab.name == this.type) {
          localTab.current = true;
          this.currentTab = localTab;
        }
        this.generatedTabs.push(localTab);
      }
    },

    setPageTitle() {
      this.title = "Your special stats";
      if (!this.$page.props.userInfo.myPage) {
        this.title = this.$page.props.userInfo.user.name + "'s special stats";
      }
    },

    sortHeader(tableHeader) {
      if (this.currentTable.body.length < 2) {
        return;
      }

      if (this.sort.sortType != tableHeader) {
        this.sort.sortOrder = "DESC";
      } else {
        this.sort.sortOrder =
          this.sort.sortOrder == "DESC" ? "ASC" : "DESC";
      }
      this.sort.sortType = tableHeader;

      this.getStats();
    },
  },
};
</script>

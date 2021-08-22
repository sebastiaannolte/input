<template>
  <Head :title="title" />
  <table-filter-header title="Special stats" />
  <active-filters
    :prop-filters="filters"
    @filterSubmit="handleFilter"
  />
  <filters-slide-over
    :prop-filters="filters"
    @filterSubmit="handleFilter"
  />
  <div class="flex flex-col items-center">
    <div class="sm:hidden w-full mb-2">
      <label for="tabs" class="sr-only">Select a tab</label>
      <select
        id="tabs"
        name="tabs"
        @change="onDropdownTabChange"
        class="
          capitalize
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
    <div
      class="
        leading-4
        text-center
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
                    t-header
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
                  :class="{
                    'arrow-down':
                      sort.sortType == values && sort.sortOrder == 'DESC',
                    'arrow-up':
                      sort.sortType == values && sort.sortOrder == 'ASC',
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
                    <button
                      @click="goTo(currentRoute, values.specialId)"
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
              v-if="currentTable && currentTable.body.length == 0"
              class="
                bg-white
                col-span-1
                px-6
                py-4
                whitespace-nowrap
                text-sm
                font-medium
                text-gray-900 text-center
              "
            >
              No results
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <pagination
    v-if="currentTable && currentTable.body.length > 0"
    :data="currentTable.body"
    :per-page-prop="pagination.perPage"
    :total-results-prop="pagination.totalResults"
    custom="true"
    :type-id="this.currentTab ? currentTab.option : ''"
  />
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import ActiveFilters from "@/Components/ActiveFilters";
import FiltersSlideOver from "@/Components/FiltersSlideOver";
import pickBy from "lodash/pickBy";
import TableFilterHeader from "@/Components/TableFilterHeader";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import Pagination from "@/Components/Pagination";

export default {
  layout: Layout,
  components: {
    TableFilterHeader,
    Loading,
    Pagination,
    FiltersSlideOver,
    ActiveFilters,
  },
  props: {
    filters: Array,
    tabs: Array,
    type: String,
  },

  data() {
    return {
      currentTable: {
        head: null,
        body: null,
      },
      sort: {
        sortType: "bets",
        sortOrder: "DESC",
      },
      selectedTab: null,
      currentTable: null,
      generatedTabs: [],
      localFilters: {},
      loading: false,
      pagination: {
        totalResults: null,
        perPage: null,
      },
      currentRoute: null,
      currentTab: null,
    };
  },

  created() {
    this.createTabs();
    if (this.type) {
      this.getStats(this.type, false);
    } else {
      this.getStats("referee", false);
    }

    this.setPageTitle();
  },

  methods: {
    getStats(key, pageReset = false) {
      var pageNumber = location.search.split("page=")[1];
      if (pageReset) {
        pageNumber = 1;
        history.replaceState &&
          history.replaceState(
            null,
            "",
            location.pathname +
              location.search.replace(/[\?&]page=[^&]+/, "").replace(/^&/, "?")
          );
      }
      this.loading = true;

      this.$http
        .post(this.route("api.special"), {
          key: key,
          filters: this.filters,
          sort: this.sort,
          page: pageNumber,
        })
        .then((response) => {
          if (response.data) {
            this.currentTable = response.data.table;
            this.pagination.totalResults = response.data.totalResults;
            this.pagination.perPage = response.data.perPage;
            this.loading = false;
          }
        });
    },

    goTo(route, id) {
      this.$inertia.get(
        this.route(route, [this.$page.props.auth.user.username, id]),
        pickBy({
          filters: this.filters,
        }),
        {
          preserveScroll: true,
          preserveState: true,
        }
      );
    },

    handleFilter(filters) {
      var localFilters = {};
      this.localFilters = filters.filters;

      var currentTab = this.generatedTabs.find((tab) => tab.current == true);

      for (const key in this.localFilters) {
        var filter = this.localFilters[key];

        if (filter.value) {
          localFilters[key] = filter;
        }
      }
      this.$inertia.get(
        this.route("special", this.$page.props.auth.user.username),
        pickBy({
          filters: localFilters,
          type: currentTab.option,
          sort: this.sort,
        }),
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            this.getStats(currentTab.option, false);
          },
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
      this.getStats(value, true);
      this.generatedTabs.forEach((tab) => {
        if (tab.option == value) {
          this.currentRoute = tab.route;
          this.currentTab = tab;
          return (tab.current = true);
        }
        tab.current = false;
      });
    },

    createTabs() {
      for (const key in this.tabs) {
        var tab = this.tabs[key];
        var current = false;
        if (tab == this.type) {
          this.currentRoute = tab.route;
          this.currentTab = tab
          current = true;
        }
        this.generatedTabs.push({ option: tab.name, current: current, route:tab.route });
      }

      if (!this.type) {
        this.currentTab = this.generatedTabs[0];
        this.currentRoute = this.generatedTabs[0].route;
        this.generatedTabs[0].current = true;
      }else{
        this.generatedTabs.find((tab) => tab.option == this.type).current = true;
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
        this.sort.sortOrder = this.sort.sortOrder == "DESC" ? "ASC" : "DESC";
      }
      this.sort.sortType = tableHeader;

      var currentTab = this.generatedTabs.find((tab) => tab.current == true);
      this.getStats(currentTab.option);
    },
  },
};
</script>

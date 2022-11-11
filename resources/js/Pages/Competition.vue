<template>
  <Head :title="title" />
  <table-filter-header :title="competition.name">
    <template v-slot:button>
      <inertia-link
        :href="
          this.route('competition.bets', [
            $page.props.auth.user.username,
            competition.id,
          ])
        "
        type="button"
        class="ml-3 inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
      >
        <span class="flex items-center">All {{ competition.name }} bets</span>
      </inertia-link>
    </template>
  </table-filter-header>
  <active-filters :prop-filters="filters" :filter-route="filterRoute" />
  <div class="flex flex-col items-center">
    <div
      class="mb-4 w-full rounded-md bg-white text-center leading-4 shadow sm:overflow-hidden"
    >
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <div
            class="overflow-hidden rounded-md border-b border-gray-200 shadow"
          >
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <th
                  scope="col"
                  class="t-header px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
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
                    class="whitespace-nowrap px-6 py-2 text-left text-sm font-medium text-gray-900 first:font-bold"
                    v-for="(values, key) in values"
                    :key="key"
                  >
                    <inertia-link
                      :href="
                        this.route('team', [
                          this.$page.props.userInfo.user.username,
                          values.specialId[0],
                          values.specialId[1],
                        ])
                      "
                      v-if="values.specialId"
                      >{{ values.value }}</inertia-link
                    >
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
import pickBy from "lodash/pickBy";
import TableFilterHeader from "@/PageComponents/TableFilterHeader.vue";
import { Inertia } from "@inertiajs/inertia";
import Pagination from "@/PageComponents/Pagination.vue";

export default {
  layout: Layout,
  components: {
    TableFilterHeader,
    Pagination,
  },
  props: {
    filters: Array,
    competition: Number,
    sort: Object,
  },

  data() {
    return {
      currentTable: {
        head: null,
        body: null,
      },
      localFilters: {},
      pagination: {
        totalResults: null,
        perPage: null,
      },
    };
  },

  created() {
    this.filterRoute = this.route("competition", {
      username: this.$page.props.userInfo.user.username,
      id: this.competition.id,
      _query: pickBy({ sort: this.sort }),
    });

    this.emitter.on("filter:submit", () => {
      this.getStats();
    });

    this.getStats();
    this.setPageTitle();
  },

  methods: {
    getStats() {
      var pageNumber = location.search.split("page=")[1];
      this.loading = true;
      this.$http
        .post(
          this.route("competition.get", this.$page.props.userInfo.user.username),
          pickBy({
            competition: this.competition.id,
            filters: this.filters,
            sort: this.sort,
            page: pageNumber,
          })
        )
        .then((response) => {
          if (response.data) {
            this.currentTable = response.data.table;
            this.pagination.totalResults = response.data.totalResults;
            this.pagination.perPage = response.data.perPage;
          }
        });
    },

    setPageTitle() {
      this.title = "Your competition stats";
      if (!this.$page.props.userInfo.myPage) {
        this.title =
          this.$page.props.userInfo.user.name + "'s competition stats";
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

      Inertia.get(
        this.route("competition", [
          this.$page.props.userInfo.user.username,
          this.competition.id,
        ]),
        pickBy({
          sort: this.sort,
          filters: this.filters,
        }),
        {
          only: ["sort", "filters"],
        }
      );
    },
  },
};
</script>

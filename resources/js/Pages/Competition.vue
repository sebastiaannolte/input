<template>
  <Head :title="title" />
  <table-filter-header :title="competition.name">
    <template v-slot:button>
     <inertia-link
     :href="this.route('competition.bets', [$page.props.auth.user.username, competition.id])"
    type="button"
    class="
      ml-3
      inline-flex
      items-center
      px-4
      py-2
      border border-transparent
      rounded-md
      shadow-sm
      text-sm
      font-medium
      text-white
      bg-indigo-600
      hover:bg-indigo-700
      focus:outline-none
      focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
    "
  >
    <span class="flex items-center">All {{competition.name}} bets</span>
  </inertia-link>
  </template>
  </table-filter-header>

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
      "
    >
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div
            class="shadow overflow-hidden border-b border-gray-200 rounded-md"
          >
            <table class="min-w-full divide-y divide-gray-200">
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
                    'arrow-down': sortType == values && isReverse,
                    'arrow-up': sortType == values && !isReverse,
                  }"
                  @click="sortHeader(values)"
                >
                  {{ values }}
                </th>
              </thead>
              <tbody>
                <tr
                  v-for="(values, key) in sortTable"
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
                    <inertia-link
                      :href="
                        this.route('team', [
                          this.$page.props.auth.user.username,
                          values.specialId[0],
                          values.specialId[1]
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
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import pickBy from "lodash/pickBy";
import TableFilterHeader from "@/Components/TableFilterHeader";

export default {
  layout: Layout,
  components: {
    TableFilterHeader,
  },
  props: {
    filters: Array,
    competition: Number,
  },

  data() {
    return {
      currentTable: {
        head: null,
      },
      localFilters: {},
      sortType: "",
      isReverse: false,
      sortIcon: null,
    };
  },

  created() {
    this.handleFilter(this.filters);
    this.setPageTitle();
  },

  methods: {
    handleFilter(filters) {
      if (filters) {
        var localFilters = {};
        this.localFilters = filters.filters;

        for (const key in this.localFilters) {
          var filter = this.localFilters[key];

          if (filter.value) {
            localFilters[key] = filter;
          }
        }
        var filterStatus = filters.filterStatus;
      }

      this.$http
        .post(
          this.route("competition.get"),
          pickBy({
            competition: this.competition.id,
            filters: localFilters,
          })
        )
        .then((response) => {
          if (response.data) {
            this.currentTable = response.data;
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
      if (this.sortType != tableHeader) {
        this.isReverse = false;
      }
      this.sortType = tableHeader;
      this.isReverse = !this.isReverse;
    },
  },
  computed: {
    sortTable: function () {
      var self = this;
      if (!this.currentTable.body) {
        return;
      }

      if (!this.sortType) {
        return this.currentTable.body;
      }

      this.currentTable.body = Object.values(this.currentTable.body).sort(
        function (a, b) {
          if (a[self.sortType].value < b[self.sortType].value) {
            if (self.isReverse) return 1;
            else return -1;
          } else {
            if (self.isReverse) return -1;
            else return 1;
          }
        }
      );

      return this.currentTable.body;
    },
  },
};
</script>

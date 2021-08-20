<template>
  <div class="flex items-center mb-5">
    <div class="flex-grow">
      <span
        v-for="(filter, key) in activeFilters"
        :key="key"
        class="
          inline-flex
          flex-grow
          rounded-full
          items-center
          py-0.5
          pl-2.5
          pr-1
          text-sm
          font-medium
          bg-indigo-100
          text-indigo-700
          mr-1
          mb-1
        "
      >
        <span class="capitalize font-bold">{{ key }}</span
        >: {{ filter }}
        <button
          @click="removeFilter(key)"
          type="button"
          class="
            flex-shrink-0
            ml-0.5
            h-4
            w-4
            rounded-full
            inline-flex
            items-center
            justify-center
            text-indigo-400
            hover:bg-indigo-200
            hover:text-indigo-500
            focus:outline-none
            focus:bg-indigo-500
            focus:text-white
          "
        >
          <span class="sr-only">Remove large option</span>
          <svg
            class="h-2 w-2"
            stroke="currentColor"
            fill="none"
            viewBox="0 0 8 8"
          >
            <path
              stroke-linecap="round"
              stroke-width="1.5"
              d="M1 1l6 6m0-6L1 7"
            />
          </svg>
        </button>
      </span>
    </div>
  </div>
  <div class="space-y-6 lg:px-0 lg:col-span-9 mb-5" v-show="filterStatus">
    <section aria-labelledby="payment-details-heading">
      <form @submit.prevent="filter">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <div>
              <h2
                id="payment-details-heading"
                class="text-xl leading-6 font-medium text-gray-900 font-bold"
              >
                Filters
              </h2>
              <p class="mt-1 text-sm text-gray-500">
                Update your billing information. Please note that updating your
                location could affect your tax rates.
              </p>
            </div>
            <div class="mt-6 grid grid-cols-6 gap-4">
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.from.value"
                  :error="errors"
                  type="date"
                  label="From"
                />
              </div>

              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.minOdds.value"
                  :error="errors"
                  label="Min odds"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input-with-add-on
                  v-model="filters.minStake.value"
                  :error="errors"
                  add-on="units"
                  label="Min stake"
                  type="text"
                  id="stake"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.sport.value"
                  :error="errors"
                  label="Sport"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.bookie.value"
                  :error="errors"
                  label="Bookie"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <label
                  class="
                    block
                    text-sm
                    font-medium
                    text-gray-700
                    dark:text-gray-400
                  "
                  >Status:</label
                >
                <select
                  class="
                    pl-3
                    pr-10
                    py-2
                    block
                    pl-3
                    py-2
                    text-base
                    border-gray-300
                    focus:outline-none
                    focus:ring-indigo-500
                    focus:border-indigo-500
                    sm:text-sm
                    rounded-md
                    w-full
                  "
                  v-model="filters.status.value"
                >
                  <option
                    class="capitalize"
                    v-for="(status, key) in this.statuses"
                    :key="key"
                    :value="key"
                  >
                    {{ key }}
                  </option>
                </select>
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.to.value"
                  :error="errors"
                  type="date"
                  label="To"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.maxOdds.value"
                  :error="errors"
                  label="Max odds"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input-with-add-on
                  v-model="filters.maxStake.value"
                  :error="errors"
                  add-on="units"
                  label="Max stake"
                  type="text"
                  id="stake"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.tipster.value"
                  :error="errors"
                  label="Tipster"
                  type="text"
                  id="tipster"
                />
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <a
              @click.prevent="resetFilters"
              class="
                mr-2
                bg-white
                border border-gray-300
                rounded-md
                shadow-sm
                py-2
                px-4
                inline-flex
                justify-center
                text-sm
                font-medium
                text-black
                hover:bg-gray-100
                focus:outline-none
                focus:ring-2 focus:ring-offset-2 focus:ring-gray-300
                cursor-pointer
              "
            >
              Reset
            </a>
            <button
              @click.prevent="filter"
              class="
                mr-2
                bg-red-500
                border border-transparent
                rounded-md
                shadow-sm
                py-2
                px-4
                inline-flex
                justify-center
                text-sm
                font-medium
                text-white
                hover:bg-red-600
                focus:outline-none
                focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
              "
            >
              Filter
            </button>
          </div>
        </div>
      </form>
    </section>
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import TextInput from "@/Components/TextInput.vue";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import Pagination from "@/Components/Pagination";
import ShowFilterButton from "@/Components/ShowFilterButton";

export default {
  layout: Layout,
  components: { TextInput, TextInputWithAddOn, Pagination, ShowFilterButton },

  props: {
    propFilters: Object,
    showFilter: Boolean,
    filterButton: Boolean,
  },
  data() {
    return {
      selectedStatus: false,
      activeKey: -1,
      statuses: {
        new: "",
        won: "border-r-8 border-green-500",
        lost: "border-r-8 border-red-500",
        void: "border-r-8 border-gray-200",
      },
      filterStatus: false,
      filteredBets: null,
      pageNumber: 1,
      perPage: 25,
      testFilters: {
        from: {
          value: null,
          type: "min",
          col: "date",
          specialType: "date",
        },
        to: {
          value: null,
          type: "max",
          col: "date",
          specialType: "date",
        },
        minOdds: {
          value: null,
          type: "min",
          col: "odds",
        },
        maxOdds: {
          value: null,
          type: "max",
          col: "odds",
        },
        minStake: {
          value: null,
          type: "min",
          col: "stake",
        },
        maxStake: {
          value: null,
          type: "max",
          col: "stake",
        },
        sport: {
          value: null,
          type: "match",
          col: "sport",
        },
        bookie: {
          value: null,
          type: "match",
          col: "bookie",
        },
        tipster: {
          value: null,
          type: "match",
          col: "tipster",
        },
        status: {
          value: null,
          type: "match",
          col: "status",
        },
      },
      filters: {},
    };
  },

  created() {
    this.filters = {
      ...this.testFilters,
      ...this.propFilters,
    };

    if (this.showFilter == true) {
      this.filterStatus = true;
    }

    // this.emitter.on("filter:show", (status) => {
    //   this.filterStatus = status;
    // });
  },

  methods: {
    filter() {
      if (Object.keys(this.activeFilters).length == 0) {
        this.filterStatus = false;
      }
      this.$emit("filterSubmit", {
        filters: this.filters,
        filterStatus: this.filterStatus,
      });
    },
    showFilter() {
      this.filterStatus = !this.filterStatus;
    },

    removeFilter(key) {
      this.filters[key].value = null;

      this.filter();
    },

    resetFilters() {
      for (var key in this.filters) {
        this.filters[key].value = null;
      }
      this.filter();
    },
  },

  computed: {
    activeFilters() {
      var filtersWithValue = {};
      for (const key in this.filters) {
        var filter = this.filters[key];
        if (filter.value) {
          filtersWithValue[key] = filter.value;
        }
      }
      return filtersWithValue;
    },
  },
};
</script>

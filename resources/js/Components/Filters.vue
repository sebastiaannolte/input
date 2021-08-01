<template>
  <div class="space-y-6 lg:px-0 lg:col-span-9 mb-5">
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
              <div class="col-span-6 sm:col-span-2">
                <text-input
                  v-model="filters.event.value"
                  :error="errors"
                  type="text"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="Event"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.from.value"
                  :error="errors"
                  type="date"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="From"
                />
              </div>

              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.minOdds.value"
                  :error="errors"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
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
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.sport.value"
                  :error="errors"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="Sport"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.bookie.value"
                  :error="errors"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
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
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                  label="To"
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.maxOdds.value"
                  :error="errors"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
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
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
                />
              </div>
              <div class="col-span-6 sm:col-span-1">
                <text-input
                  v-model="filters.tipster.value"
                  :error="errors"
                  label="Tipster"
                  type="text"
                  id="tipster"
                  class="
                    mt-1
                    block
                    w-full
                    border border-gray-300
                    rounded-md
                    shadow-sm
                    py-2
                    px-3
                    focus:outline-none
                    focus:ring-gray-900
                    focus:border-gray-900
                    sm:text-sm
                  "
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

export default {
  layout: Layout,
  components: { TextInput, TextInputWithAddOn, Pagination },

  props: {
    propFilters: Object,
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
        event: {
          value: null,
          type: "like",
          col: "event",
        },
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
  },

  methods: {
    filter() {
      this.$emit("filterSubmit", this.filters);
    },

    resetFilters() {
      for (var key in this.filters) {
        this.filters[key].value = null;
      }
      this.filter();
    },
  },

  computed: {},
};
</script>

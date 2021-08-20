<template>
    <div class="">
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

</template>

<script>
export default {
  props: {
    propFilters: Object,
  },
  data() {
    return {
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

  created() {},

  methods: {
    filter() {
      if (Object.keys(this.activeFilters).length == 0) {
        this.filterStatus = false;
      }
      this.$emit("filterSubmit", {
        filters: this.filters,
      });
    },

    removeFilter(key) {
      this.filters[key].value = null;
      this.filter();
    },
  },

  computed: {
    myFilters() {
      this.filters = {
        ...this.testFilters,
        ...this.propFilters,
      };
    },
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

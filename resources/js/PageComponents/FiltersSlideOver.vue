<!--
  This example requires Tailwind CSS v2.0+ 
  
  This example requires some changes to your config:
  
  ```
  // tailwind.config.js
  module.exports = {
    // ...
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
  ```
-->
<template>
  <TransitionRoot as="template" :show="open">
    <Dialog
      as="div"
      auto-reopen="true"
      class="fixed inset-0 overflow-hidden z-20"
      @close="open = false"
    >
      <div class="absolute inset-0 overflow-hidden">
        <DialogOverlay class="absolute inset-0" />

        <div class="fixed inset-y-0 pl-16 max-w-full right-0 flex">
          <TransitionChild
            as="template"
            enter="transform transition ease-in-out duration-500 sm:duration-700"
            enter-from="translate-x-full"
            enter-to="translate-x-0"
            leave="transform transition ease-in-out duration-500 sm:duration-700"
            leave-from="translate-x-0"
            leave-to="translate-x-full"
          >
            <div class="w-screen max-w-md">
              <form
                class="
                  h-full
                  divide-y divide-gray-200
                  flex flex-col
                  bg-white
                  shadow-xl
                "
              >
                <div class="flex-1 h-0 overflow-y-auto">
                  <div class="py-6 px-4 bg-indigo-700 sm:px-6">
                    <div class="flex items-center justify-between">
                      <DialogTitle class="text-lg font-medium text-white">
                        Filters
                      </DialogTitle>
                      <div class="ml-3 h-7 flex items-center">
                        <button
                          type="button"
                          class="
                            bg-indigo-700
                            rounded-md
                            text-indigo-200
                            hover:text-white
                            focus:outline-none
                            focus:ring-2 focus:ring-white
                          "
                          @click="open = false"
                        >
                          <span class="sr-only">Close panel</span>
                          <XIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                      </div>
                    </div>
                    <div class="mt-1">
                      <p class="text-sm text-indigo-300">
                        Get started by filling in the information below to
                        create your new project.
                      </p>
                    </div>
                  </div>
                  <div class="space-y-6 lg:px-0 lg:col-span-9">
                    <section aria-labelledby="payment-details-heading">
                      <form @submit.prevent="filter">
                        <div class="ssm:overflow-hidden">
                          <div class="py-6 px-4 sm:p-6">
                            <div class="mt-6 grid grid-cols-6 gap-4">
                              <div class="col-span-6 sm:col-span-3">
                                <text-input
                                  v-model="filters.from.value"
                                  :error="errors"
                                  type="date"
                                  label="From"
                                />
                              </div>
                              <div class="col-span-6 sm:col-span-3">
                                <text-input
                                  v-model="filters.to.value"
                                  :error="errors"
                                  type="date"
                                  label="To"
                                />
                              </div>
                              <div class="col-span-6 sm:col-span-3">
                                <text-input
                                  v-model="filters.minOdds.value"
                                  :error="errors"
                                  label="Min odds"
                                />
                              </div>
                              <div class="col-span-6 sm:col-span-3">
                                <text-input
                                  v-model="filters.maxOdds.value"
                                  :error="errors"
                                  label="Max odds"
                                />
                              </div>
                              <div class="col-span-6 sm:col-span-3">
                                <text-input-with-add-on
                                  v-model="filters.minStake.value"
                                  :error="errors"
                                  add-on="units"
                                  label="Min stake"
                                  type="text"
                                  id="stake"
                                />
                              </div>
                              <div class="col-span-6 sm:col-span-3">
                                <text-input-with-add-on
                                  v-model="filters.maxStake.value"
                                  :error="errors"
                                  add-on="units"
                                  label="Max stake"
                                  type="text"
                                  id="stake"
                                />
                              </div>
                              <div class="col-span-6 sm:col-span-3">
                                <text-input
                                  v-model="filters.sport.value"
                                  :error="errors"
                                  label="Sport"
                                />
                              </div>
                              <div class="col-span-6 sm:col-span-3">
                                <text-input
                                  v-model="filters.bookie.value"
                                  :error="errors"
                                  label="Bookie"
                                />
                              </div>
                              <div class="col-span-6 sm:col-span-3">
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

                              <div class="col-span-6 sm:col-span-3">
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
                        </div>
                      </form>
                    </section>
                  </div>
                </div>
                <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                  <button
                    type="button"
                    class="
                      bg-white
                      py-2
                      px-4
                      border border-gray-300
                      rounded-md
                      shadow-sm
                      text-sm
                      font-medium
                      text-gray-700
                      hover:bg-gray-50
                      focus:outline-none
                      focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                    "
                    @click.prevent="resetFilters"
                  >
                    Reset
                  </button>
                  <button
                    @click.prevent="filter(false)"
                    type="submit"
                    class="
                      ml-4
                      inline-flex
                      justify-center
                      py-2
                      px-4
                      border border-transparent
                      shadow-sm
                      text-sm
                      font-medium
                      rounded-md
                      text-white
                      bg-indigo-600
                      hover:bg-indigo-700
                      focus:outline-none
                      focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                    "
                  >
                    Filter
                  </button>
                </div>
              </form>
            </div>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { ref } from "vue";
import {
  Dialog,
  DialogOverlay,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { XIcon } from "@heroicons/vue/outline";
import {
  LinkIcon,
  PlusIcon,
  QuestionMarkCircleIcon,
} from "@heroicons/vue/solid";
import TextInput from "@/Components/TextInput.vue";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import Pagination from "@/PageComponents/Pagination";
import ShowFilterButton from "@/Components/ShowFilterButton";
import pickBy from "lodash/pickBy";
import { Inertia } from "@inertiajs/inertia";

export default {
  components: {
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    LinkIcon,
    PlusIcon,
    QuestionMarkCircleIcon,
    XIcon,
    TextInput,
    TextInputWithAddOn,
    Pagination,
    ShowFilterButton,
  },
  setup() {
    const open = ref(false);

    return {
      open,
    };
  },
  props: {
    propFilters: Object,
    filterButton: Boolean,
    filterRoute: String,
  },
  data() {
    return {
      statuses: {
        new: "",
        won: "border-r-8 border-green-500",
        lost: "border-r-8 border-red-500",
        void: "border-r-8 border-gray-200",
      },
      filterStatus: false,
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

    this.emitter.on("filter:show", (status) => {
      this.open = true;
    });
    this.emitter.on("filter:remove", (key) => {
      this.removeFilter(key);
    });
  },

  methods: {
    filter() {
      var localFilters = {};
      this.localFilters = this.filters;

      for (const key in this.localFilters) {
        var filter = this.localFilters[key];

        if (filter.value) {
          localFilters[key] = filter;
        }
      }

      Inertia.get(
        this.filterRoute,
        pickBy({
          filters: localFilters,
        }),
        {
          only: ["filters", "stats", "bets"],
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            this.emitter.emit("filter:submit");
          },
        }
      );
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
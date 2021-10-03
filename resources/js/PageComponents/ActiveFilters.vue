<template>
  <Disclosure
    as="section"
    aria-labelledby="filter-heading"
    class="
      relative
      border-t border-b border-gray-200
      grid
      items-center
      bg-white
      mb-4
      shadow
      sm:rounded-md
      sm:overflow-hidden
    "
  >
    <h2 id="filter-heading" class="sr-only">Filters</h2>
    <div class="relative col-start-1 row-start-1 py-4">
      <div
        class="
          max-w-7xl
          mx-auto
          flex
          space-x-6
          divide-x divide-gray-200
          text-sm
          px-4
          sm:px-6
          lg:px-8
        "
      >
        <div>
          <DisclosureButton
            class="group text-gray-700 font-medium flex items-center"
          >
            <FilterIcon
              class="
                flex-none
                w-5
                h-5
                mr-2
                text-gray-400
                group-hover:text-gray-500
              "
              aria-hidden="true"
            />
            {{ Object.values(activeFilters).length }} Filters
          </DisclosureButton>
        </div>
        <div class="pl-6">
          <button
            type="button"
            @click.prevent="resetFilters"
            class="text-gray-500"
          >
            Clear all
          </button>
        </div>
      </div>
    </div>
    <DisclosurePanel class="border-t border-gray-200">
      <form @submit.prevent="filter" @keyup.enter="filter">
        <div class="ssm:overflow-hidden">
          <div class="py-6 px-4 sm:p-6">
            <div class="mt-6 grid grid-cols-8 gap-4">
              <div class="col-span-6 sm:col-span-2">
                <text-input
                  v-model="filters.from.value"
                  :error="errors"
                  type="date"
                  label="From"
                />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input
                  v-model="filters.to.value"
                  :error="errors"
                  type="date"
                  label="To"
                />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input
                  v-model="filters.minOdds.value"
                  :error="errors"
                  label="Min odds"
                />
              </div>
              <div class="col-span-6 sm:col-span-2">
                <text-input
                  v-model="filters.maxOdds.value"
                  :error="errors"
                  label="Max odds"
                />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input-with-add-on
                  v-model="filters.minStake.value"
                  :error="errors"
                  add-on="units"
                  label="Min stake"
                  type="text"
                  id="stake"
                />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input-with-add-on
                  v-model="filters.maxStake.value"
                  :error="errors"
                  add-on="units"
                  label="Max stake"
                  type="text"
                  id="stake"
                />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input
                  v-model="filters.sport.value"
                  :error="errors"
                  label="Sport"
                />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input
                  v-model="filters.bookie.value"
                  :error="errors"
                  label="Bookie"
                />
              </div>
              <div class="col-span-4 sm:col-span-2">
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

              <div class="col-span-4 sm:col-span-2">
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
    </DisclosurePanel>
    <div class="col-start-1 row-start-1 py-4">
      <div class="flex justify-end max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <Menu as="div" class="relative inline-block">
          <div class="flex">
            <MenuButton
              @click.prevent="filter()"
              class="
                group
                inline-flex
                justify-center
                text-sm
                font-medium
                text-gray-700
                hover:text-gray-900
              "
            >
              Filter
            </MenuButton>
          </div>

          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <MenuItems
              class="
                origin-top-right
                absolute
                right-0
                mt-2
                w-40
                rounded-md
                shadow-2xl
                bg-white
                ring-1 ring-black ring-opacity-5
                focus:outline-none
              "
            >
              <div class="py-1">
                <MenuItem
                  v-for="option in sortOptions"
                  :key="option.name"
                  v-slot="{ active }"
                >
                  <a
                    :href="option.href"
                    :class="[
                      option.current
                        ? 'font-medium text-gray-900'
                        : 'text-gray-500',
                      active ? 'bg-gray-100' : '',
                      'block px-4 py-2 text-sm',
                    ]"
                  >
                    {{ option.name }}
                  </a>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
      </div>
    </div>
  </Disclosure>
</template>

<script>
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from "@headlessui/vue";
import { ChevronDownIcon, FilterIcon } from "@heroicons/vue/solid";
import TextInput from "@/Components/TextInput.vue";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import { Inertia } from "@inertiajs/inertia";
import pickBy from "lodash/pickBy";

export default {
  props: {
    propFilters: Object,
    filterRoute: String,
    errors: Object,
  },

  components: {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    ChevronDownIcon,
    FilterIcon,
    TextInputWithAddOn,
    TextInput,
  },

  data() {
    return {
      statuses: {
        new: "",
        won: "border-r-8 border-green-500",
        lost: "border-r-8 border-red-500",
        void: "border-r-8 border-gray-200",
      },
      localFilters: {
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
    this.setFilters();
  },

  methods: {
    setFilters() {
      this.filters = {
        ...this.localFilters,
        ...this.propFilters,
      };
    },
    resetFilters() {
      for (var key in this.filters) {
        this.filters[key].value = null;
      }

      this.filter();
    },

    filter() {
      var localFilters = {};
      for (const key in this.filters) {
        var filter = this.filters[key];

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

  watch: {
    propFilters: function (val) {
      // this.setFilters();
    },
  },
};
</script>

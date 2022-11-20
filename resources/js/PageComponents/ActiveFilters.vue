<template>
  <Disclosure as="section" aria-labelledby="filter-heading" class="relative z-0 mb-4 grid items-center rounded-md bg-white shadow dark:bg-slate-800 sm:overflow-hidden">
    <h2 id="filter-heading" class="sr-only">Filters</h2>
    <div class="relative col-start-1 row-start-1 py-4">
      <div class="mx-auto flex max-w-7xl space-x-6 divide-x divide-slate-200/20 px-4 text-sm sm:px-6 lg:px-8">
        <div>
          <DisclosureButton class="group flex items-center font-medium text-gray-700 dark:text-slate-400">
            <FilterIcon class="mr-2 h-5 w-5 flex-none text-gray-400 group-hover:text-gray-500 dark:text-slate-400" aria-hidden="true" />
            {{ Object.values(activeFilters).length }} Filters
          </DisclosureButton>
        </div>
        <div class="pl-6">
          <button type="button" class="text-gray-500 dark:text-slate-400" @click.prevent="resetFilters">Clear all</button>
        </div>
      </div>
    </div>
    <DisclosurePanel class="border-t border-gray-200 dark:border-slate-200/20">
      <form @submit.prevent="filter" @keyup.enter="filter">
        <div class="ssm:overflow-hidden">
          <div class="py-6 px-4 sm:p-6">
            <div class="mt-6 grid grid-cols-8 gap-4">
              <div class="col-span-4 sm:col-span-2">
                <text-input v-model="filters.from.value" :error="errors" type="date" label="From" />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input v-model="filters.to.value" :error="errors" type="date" label="To" />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input v-model="filters.minOdds.value" :error="errors" label="Min odds" />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input v-model="filters.maxOdds.value" :error="errors" label="Max odds" />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input-with-add-on id="stake" v-model="filters.minStake.value" :error="errors" add-on="units" label="Min stake" type="text" />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input-with-add-on id="stake" v-model="filters.maxStake.value" :error="errors" add-on="units" label="Max stake" type="text" />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <SelectInput label="Sport" v-model="filters.sport.value">
                  <option v-for="(sport, key) in $page.props.sports" :key="key" class="capitalize dark:bg-slate-700 dark:text-slate-400" :value="sport.name">
                    {{ sport.name }}
                  </option>
                </SelectInput>
              </div>
              <div class="col-span-4 sm:col-span-2">
                <text-input v-model="filters.bookie.value" :error="errors" label="Bookie" />
              </div>
              <div class="col-span-4 sm:col-span-2">
                <SelectInput label="Status" v-model="filters.status.value">
                  <option v-for="(status, key) in statuses" :key="key" class="capitalize dark:bg-slate-700 dark:text-slate-400" :value="key">
                    {{ key }}
                  </option>
                </SelectInput>
              </div>

              <div class="col-span-4 sm:col-span-2">
                <text-input id="tipster" v-model="filters.tipster.value" :error="errors" label="Tipster" type="text" />
              </div>
            </div>
          </div>
        </div>
      </form>
    </DisclosurePanel>
    <div class="col-start-1 row-start-1 py-4">
      <div class="mx-auto flex max-w-7xl justify-end px-4 sm:px-6 lg:px-8">
        <Menu as="div" class="relative inline-block">
          <div class="flex">
            <MenuButton class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-slate-400" @click.prevent="filter()"> Filter </MenuButton>
          </div>

          <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems class="absolute right-0 mt-2 w-40 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none">
              <div class="py-1">
                <MenuItem v-for="option in sortOptions" :key="option.name" v-slot="{ active }">
                  <a :href="option.href" :class="[option.current ? 'font-medium text-gray-900' : 'text-gray-500', active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm']">
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
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon, FilterIcon } from '@heroicons/vue/solid'
import TextInput from '@/Components/TextInput.vue'
import TextInputWithAddOn from '@/Components/TextInputWithAddOn.vue'
import { Inertia } from '@inertiajs/inertia'
import pickBy from 'lodash/pickBy'
import emitter from '@/Plugins/mitt'
import SelectInput from '@/Components/SelectInput.vue'

export default {
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
    SelectInput,
  },
  props: {
    propFilters: Object,
    filterRoute: String,
    errors: Object,
  },

  data() {
    return {
      statuses: {
        new: '',
        won: 'border-r-8 border-green-500',
        lost: 'border-r-8 border-red-500',
        void: 'border-r-8 border-gray-200',
      },
      localFilters: {
        from: {
          value: null,
          type: 'min',
          col: 'date',
          specialType: 'date',
        },
        to: {
          value: null,
          type: 'max',
          col: 'date',
          specialType: 'date',
        },
        minOdds: {
          value: null,
          type: 'min',
          col: 'odds',
        },
        maxOdds: {
          value: null,
          type: 'max',
          col: 'odds',
        },
        minStake: {
          value: null,
          type: 'min',
          col: 'stake',
        },
        maxStake: {
          value: null,
          type: 'max',
          col: 'stake',
        },
        sport: {
          value: null,
          type: 'match',
          col: 'sport',
        },
        bookie: {
          value: null,
          type: 'match',
          col: 'bookie',
        },
        tipster: {
          value: null,
          type: 'match',
          col: 'tipster',
        },
        status: {
          value: null,
          type: 'match',
          col: 'status',
        },
      },
      filters: {},
    }
  },

  computed: {
    activeFilters() {
      var filtersWithValue = {}
      for (const key in this.filters) {
        var filter = this.filters[key]
        if (filter.value) {
          filtersWithValue[key] = filter.value
        }
      }
      return filtersWithValue
    },
  },

  watch: {
    propFilters: function (val) {
      // this.setFilters();
    },
  },

  created() {
    this.setFilters()
  },

  methods: {
    setFilters() {
      this.filters = {
        ...this.localFilters,
        ...this.propFilters,
      }
    },
    resetFilters() {
      for (var key in this.filters) {
        this.filters[key].value = null
      }

      this.filter()
    },

    filter() {
      var localFilters = {}
      for (const key in this.filters) {
        var filter = this.filters[key]

        if (filter.value) {
          localFilters[key] = filter
        }
      }

      Inertia.get(
        this.filterRoute,
        pickBy({
          filters: localFilters,
        }),
        {
          only: ['filters', 'stats', 'bets', 'generalStats', 'table'],
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            emitter.emit('filter:submit')
          },
        },
      )
    },
  },
}
</script>

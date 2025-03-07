<template>
  <div>
    <div class="flex">
      <Multiselect ref="multiselect" v-model="event" mode="single" placeholder="Search a match" no-options-text="Start typing to find a match" :filterResults="false" :min-chars="0" :caret="false" :resolve-on-load="false" :delay="300" :searchable="true" :create-tag="true" :object="true" :clear-on-select="false" :clear-on-search="false" :options="fetchMatchesOptions" class="rounded-r-0" @select="onSelect">
        <template #option="{ option }"> <img class="mr-2 h-4" :src="option.icon" /> {{ option.label }} </template>
      </Multiselect>
      <div @click="showFilters = !showFilters" :class="{ 'inner-shadow bg-gray-200 text-white': showFilters }" class="inline-flex cursor-pointer items-center border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm">
        <FunnelIcon class="h-4 w-4" aria-hidden="true" />
      </div>
      <button type="button" class="inline-flex cursor-pointer items-center border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm" @click="searchMatches">Search</button>
    </div>
    <div v-if="errors['games.' + index + '.event']" class="form-error text-gray-400">
      <small>{{ errors['games.' + index + '.event'] }}</small>
    </div>
    <div v-if="showFilters">
      <label class="block text-sm font-medium text-gray-700 dark:text-slate-400" for="team">Club:</label>
      <TeamSearch v-model="filters.club" id="team" />
      <button
        type="button"
        :class="{
          'inner-shadow bg-gray-200 text-white': filters.searchType == 'simple',
        }"
        class="inline-flex items-center border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm"
        @click="filters.searchType = 'simple'"
      >
        <LockClosedIcon class="h-4 w-4" aria-hidden="true" />
      </button>
      <button type="button" :class="{ 'inner-shadow bg-gray-200 text-white': filters.searchType == 'full' }" class="inline-flex items-center rounded-r border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:bg-gray-300 focus:outline-none" @click="filters.searchType = 'full'">
        <LockOpenIcon class="h-4 w-4" aria-hidden="true" />
      </button>
    </div>
  </div>
</template>

<script>
import TextInput from '@/Components/TextInput.vue'
import Multiselect from '@vueform/multiselect'
import { LockClosedIcon, LockOpenIcon, FunnelIcon } from '@heroicons/vue/20/solid'
import emitter from '@/Plugins/mitt'
import TeamSearch from './TeamSearch.vue'
import axios from 'axios'

export default {
  components: { TextInput, Multiselect, LockClosedIcon, LockOpenIcon, FunnelIcon, TeamSearch },
  props: {
    modelValue: Object,
    errors: Object,
    bet: Object,
    index: String,
    sport: Object,
  },

  setup() {
    const fetchMatches = async (filters, query, sport) => {
      filters = { ...filters, query, sport }
      const response = await axios.post('/api/search', filters)
      return Object.values(response.data).map((item) => {
        return { value: item.id, label: item.match, icon: item.icon }
      })
    }

    return {
      fetchMatches,
    }
  },
  data() {
    return {
      betData: {},
      event: null,
      showFilters: false,
      filters: {
        club: null,
        searchType: 'simple',
      },
    }
  },

  created() {
    if (this.bet.event) {
      this.betData = this.bet
    }

    emitter.on('event:clear', () => {
      this.betData.event = null
      this.betData.match = null
    })
  },

  mounted() {
    if (this.bet.event) {
      this.event = {
        value: this.bet.fixture_id ? this.bet.fixture_id : 0,
        label: this.bet.event,
      }

      this.setBet()
    }
  },

  watch: {
    'filters.club': {
      handler: function (val) {
        this.searchMatches()
        this.$refs.multiselect.open()
        this.$refs.multiselect.$el.querySelector('input').focus()
      },
      deep: true,
    },
  },

  methods: {
    async searchMatches() {
      this.$refs.multiselect.refreshOptions()
    },

    fetchMatchesOptions(query) {
      return this.fetchMatches(this.filters, query, this.sport)
    },

    onSelect(option) {
      this.findGame(option.value)
    },

    findGame(option) {
      if (option) {
        this.$http.get(this.route('event.match', option)).then((response) => {
          if (response.data) {
            this.betData.match = response.data
            this.setBet()
            return
          }
        })
      }
      this.betData.match = null
      this.setBet()
    },

    setBet() {
      var newData = {
        event: this.event,
        match: this.betData.match,
      }
      this.$emit('update:modelValue', newData)
    },
  },
}
</script>

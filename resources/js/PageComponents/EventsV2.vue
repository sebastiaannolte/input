<template>
  <div>
    <div class="flex">
      <Multiselect
        ref="multiselect"
        v-model="event"
        mode="single"
        placeholder="Search a match"
        no-options-text="Start typing to find a match"
        :filterResults="false"
        :min-chars="3"
        :caret="false"
        :resolve-on-load="false"
        :delay="300"
        :searchable="true"
        :create-tag="true"
        :object="true"
        :clear-on-select="false"
        :clear-on-search="false"
        :options="
          async function (query) {
            return await fetchMatches(query, searchType, sport)
          }
        "
        class="rounded-r-0"
        @select="onSelect"
      >
        <template #option="{ option }"> <img class="mr-2 h-4" :src="option.icon" /> {{ option.label }} </template>
      </Multiselect>
      <button
        type="button"
        :class="{
          'inner-shadow bg-gray-200 text-white': searchType == 'simple',
        }"
        class="inline-flex items-center border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm"
        @click="setSearchType('simple')"
      >
        <LockClosedIcon class="h-4 w-4" aria-hidden="true" />
      </button>
      <button type="button" :class="{ 'inner-shadow bg-gray-200 text-white': searchType == 'full' }" class="inline-flex items-center rounded-r border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:bg-gray-300 focus:outline-none" @click="setSearchType('full')">
        <LockOpenIcon class="h-4 w-4" aria-hidden="true" />
      </button>
    </div>
    <div v-if="errors['games.' + index + '.event']" class="form-error text-gray-400">
      <small>{{ errors['games.' + index + '.event'] }}</small>
    </div>
  </div>
</template>

<script>
import TextInput from '@/Components/TextInput.vue'
import Multiselect from '@vueform/multiselect'
import { LockClosedIcon, LockOpenIcon } from '@heroicons/vue/20/solid'
import emitter from '@/Plugins/mitt'

export default {
  components: { TextInput, Multiselect, LockClosedIcon, LockOpenIcon },
  props: {
    modelValue: Object,
    errors: Object,
    bet: Object,
    index: String,
    sport: Object,
  },

  setup() {
    const fetchMatches = async (query, searchType, sport) => {
      const response = await fetch('/api/search/' + query + '/' + searchType + '/' + sport, {})
      const data = await response.json()
      return Object.values(data).map((item) => {
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
      searchType: 'simple',
      event: null,
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

  methods: {
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

    setSearchType(type) {
      this.searchType = type
      // this.$refs.multiselect.open();
    },
  },
}
</script>

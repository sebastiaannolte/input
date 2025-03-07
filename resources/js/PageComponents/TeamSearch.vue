<template>
  <div>
    <div class="flex">
      <Multiselect
        ref="multiselect"
        v-model="event"
        mode="single"
        placeholder="Search a team"
        no-options-text="Start typing to find a match"
        :filterResults="false"
        :min-chars="3"
        :caret="false"
        :resolve-on-load="false"
        :delay="300"
        :searchable="true"
        :create-tag="false"
        :object="true"
        :clear-on-select="false"
        :clear-on-search="false"
        :options="
          async function (query) {
            return await fetchTeams(query, filters)
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
          'inner-shadow bg-gray-200 text-white': filters.location == 'home',
        }"
        class="inline-flex items-center border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm"
        @click="changeLocation('home')"
      >
        Home
      </button>
      <button type="button" :class="{ 'inner-shadow bg-gray-200 text-white': filters.location == 'away' }" class="inline-flex items-center rounded-r border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:bg-gray-300 focus:outline-none" @click="changeLocation('away')">Away</button>
    </div>
  </div>
</template>

<script>
import TextInput from '@/Components/TextInput.vue'
import Multiselect from '@vueform/multiselect'
import { LockClosedIcon, LockOpenIcon, FunnelIcon } from '@heroicons/vue/20/solid'
import axios from 'axios'

export default {
  components: { TextInput, Multiselect, LockClosedIcon, LockOpenIcon, FunnelIcon },
  props: {
    modelValue: Object,
  },

  setup() {
    const fetchTeams = async (query, filters) => {
      filters.query = query
      const response = await axios.post('/api/teams', filters)
      return Object.values(response.data).map((item) => {
        return { value: item.id, label: item.name, icon: item.logo }
      })
    }

    return {
      fetchTeams,
    }
  },
  data() {
    return {
      event: null,
      currentOption: null,
      filters: {
        location: 'home',
      },
    }
  },

  methods: {
    onSelect(option) {
      option.location = this.filters.location
      this.currentOption = option
      this.$emit('update:modelValue', option)
    },
    changeLocation(location) {
      this.filters.location = location
      if (this.currentOption) {
        this.onSelect(this.currentOption)
      }

    },
  },
}
</script>

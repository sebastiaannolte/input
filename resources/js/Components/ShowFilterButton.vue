<template>
  <button
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
    @click.prevent="showFilter"
  >
    <span class="flex items-center"><FilterIcon
      class="block h-4 w-4 mr-1.5"
      aria-hidden="true"
    />Filter</span>
  </button>
</template>

<script>
import Layout from '@/Layouts/Authenticated.vue'
import { FilterIcon } from '@heroicons/vue/outline'

export default {
  components: { FilterIcon },
  layout: Layout,

  props: {
    status: Boolean,
  },
  data() {
    return {
      filterStatus: false,
    }
  },

  created() {
    this.filterStatus = this.status
  },

  methods: {
    showFilter() {
      this.filterStatus = !this.filterStatus
      this.emitter.emit('filter:show', this.filterStatus)
    },
  },
}
</script>

<template>
  <div v-if="from" class="flex items-center justify-between p-4">
    <div class="flex flex-1 justify-between sm:hidden">
      <Card v-if="currentPage == 1" class="bg-gray-100 px-4 py-2 hover:bg-gray-50">Previous</Card>
      <Card v-else class="bg-gray-100 px-4 py-2 hover:bg-gray-50">
        <inertia-link preserve-scroll :href="url(currentPage - 1)"> Previous </inertia-link>
      </Card>
      <Card v-if="currentPage == lastPage" class="bg-gray-100 px-4 py-2 hover:bg-gray-50">Next</Card>
      <Card v-else class="bg-gray-100 px-4 py-2 hover:bg-gray-50">
        <inertia-link preserve-scroll :href="url(currentPage + 1)"> Next </inertia-link>
      </Card>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700 dark:text-slate-400">
          Showing
          {{ ' ' }}
          <span class="font-medium">{{ from }}</span>
          {{ ' ' }}
          to
          {{ ' ' }}
          <span class="font-medium">{{ to }}</span>
          {{ ' ' }}
          of
          {{ ' ' }}
          <span class="font-medium">{{ totalResults }}</span>
          {{ ' ' }}
          results
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex gap-1.5 -space-x-pxfont-bold shadow-sm" aria-label="Pagination">
          <Card v-if="currentPage == 1" class="px-2 py-2 cursor-not-allowed">
            <span class="sr-only">Previous</span>
            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
          </Card>
          <Card v-else class="px-2 py-2 hover:bg-gray-50 cursor-pointer">
            <inertia-link :href="url(currentPage - 1)">
              <span class="sr-only">Previous</span>
              <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
            </inertia-link>
          </Card>
          <Card v-for="index in pages" :key="index" class="px-4 py-2 cursor-pointer" :class="[currentPage == index ? 'border-indigo-500 bg-indigo-50 text-indigo-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50']">
            <inertia-link preserve-scroll :href="url(index)" aria-current="page">
              {{ index }}
            </inertia-link>
          </Card>
          <Card v-if="currentPage == lastPage" class="px-2 py-2 cursor-not-allowed">
            <span class="sr-only">Next</span>
            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
          </Card>
          <Card v-else class="px-2 py-2 hover:bg-gray-50 cursor-pointer">
            <inertia-link preserve-scroll :href="url(currentPage + 1)">
              <span class="sr-only">Next</span>
              <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
            </inertia-link>
          </Card>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import Card from '@/PageComponents/Card.vue'

export default {
  components: {
    ChevronLeftIcon,
    ChevronRightIcon,
    Card,
  },
  props: {
    data: Object,
    custom: {
      type: Boolean,
      default: false,
    },
    perPageProp: {
      type: Number,
      default: 0,
    },
    totalResultsProp: {
      type: Number,
      default: 0,
    },
    typeId: {
      type: [String, Number],
      default: 0,
    },
  },

  data() {
    return {
      perPage: null,
      totalResults: null,
      from: null,
      to: null,
      active: false,
      currentPage: null,
      lastPage: null,
      pages: null,
    }
  },

  watch: {
    data: {
      immediate: true,
      handler(val, oldVal) {
        this.setPageInfo()
      },
    },
  },

  created() {
    this.setPageInfo()
  },

  methods: {
    url(number) {
      if (typeof number != 'number') {
        return
      }
      var url = new URL(window.location.href)
      var search_params = url.searchParams

      if (this.typeId) {
        search_params.set('type', this.typeId)
      }
      search_params.set('page', number)
      url.search = search_params.toString()
      var new_url = url.toString()
      return new_url
    },

    setPageInfo() {
      if (this.custom) {
        if (!this.data) {
          return
        }
        this.perPage = this.perPageProp
        this.totalResults = this.totalResultsProp
        var url = new URL(window.location.href)
        var search_params = url.searchParams
        this.currentPage = parseInt(search_params.get('page')) ? parseInt(search_params.get('page')) : 1
        this.from = this.perPage * this.currentPage - this.perPage + 1
        this.to = this.from + (this.perPage + this.from > this.totalResults ? this.totalResults - this.from : this.perPage - 1)
        ;(this.lastPage = Math.ceil(this.totalResults / this.perPage)), this.setPages(this.currentPage, this.lastPage)
        return
      }
      this.perPage = this.data.per_page
      this.totalResults = this.data.total
      this.from = this.data.from
      this.to = this.data.to
      this.currentPage = this.data.current_page
      ;(this.lastPage = Math.ceil(this.totalResults / this.perPage)), this.setPages(this.currentPage, this.lastPage)
    },

    setPages(currentPage, pageCount) {
      let delta = 1,
        left = currentPage - delta,
        right = currentPage + delta + 1,
        result = []

      result = Array.from({ length: pageCount }, (v, k) => k + 1).filter((i) => i && i >= left && i < right)

      if (result.length > 1) {
        // Add first page and dots
        if (result[0] > 1) {
          if (result[0] > 2) {
            // Add dots
            result.unshift('...')
          }
          // Add first page
          result.unshift(1)
        }

        // Add dots and last page
        if (result[result.length - 1] < pageCount) {
          if (result[result.length - 1] !== pageCount - 1) {
            // Add dots
            result.push('...')
          }
          // Add last page
          result.push(pageCount)
        }
      }

      this.pages = result
    },
  },
}
</script>

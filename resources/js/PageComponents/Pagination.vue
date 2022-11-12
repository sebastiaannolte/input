<template>
  <div v-if="from" class="py-3 flex items-center justify-between border-t border-gray-200">
    <div class="flex-1 flex justify-between sm:hidden">
      <div
        v-if="currentPage == 1" class="
          relative
          inline-flex
          items-center
          px-4
          py-2
          border border-gray-300
          text-sm
          font-medium
          rounded-md
          text-gray-700
          bg-gray-100
          hover:bg-gray-50
        "
      >
        Previous
      </div>
      <inertia-link
        v-else preserve-scroll :href="url(currentPage - 1)" class="
          relative
          inline-flex
          items-center
          px-4
          py-2
          border border-gray-300
          text-sm
          font-medium
          rounded-md
          text-gray-700
          bg-white
          hover:bg-gray-50
        "
      >
        Previous
      </inertia-link>

      <div
        v-if="currentPage == lastPage" class="
          relative
          inline-flex
          items-center
          px-4
          py-2
          border border-gray-300
          text-sm
          font-medium
          rounded-md
          text-gray-700
          bg-gray-100
          hover:bg-gray-50
        "
      >
        Next
      </div>
      <inertia-link
        v-else preserve-scroll :href="url(currentPage + 1)" class="
          ml-3
          relative
          inline-flex
          items-center
          px-4
          py-2
          border border-gray-300
          text-sm
          font-medium
          rounded-md
          text-gray-700
          bg-white
          hover:bg-gray-50
        "
      >
        Next
      </inertia-link>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          {{ " " }}
          <span class="font-medium">{{ from }}</span>
          {{ " " }}
          to
          {{ " " }}
          <span class="font-medium">{{ to }}</span>
          {{ " " }}
          of
          {{ " " }}
          <span class="font-medium">{{ totalResults }}</span>
          {{ " " }}
          results
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <div
            v-if="currentPage == 1" class="
              relative
              inline-flex
              items-center
              px-2
              py-2
              rounded-l-md
              border border-gray-300
              bg-white
              text-sm
              font-medium
              text-gray-400
              bg-gray-100
            "
          >
            <span class="sr-only">Previous</span>
            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
          </div>
          <inertia-link
            v-else :href="url(currentPage - 1)" class="
              relative
              inline-flex
              items-center
              px-2
              py-2
              rounded-l-md
              border border-gray-300
              bg-white
              text-sm
              font-medium
              text-gray-500
              hover:bg-gray-50
            "
          >
            <span class="sr-only">Previous</span>
            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
          </inertia-link>
          <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
          <inertia-link
            v-for="index in pages" :key="index" preserve-scroll :href="url(index)" aria-current="page"
            class="
            relative inline-flex items-center border  px-4 py-2 text-sm font-medium
            " :class="[currentPage == index ?
              'z-10 border-indigo-500 bg-indigo-50 text-indigo-600 focus:z-20' :
              'border-gray-300 bg-white text-gray-500 hover:bg-gray-50 focus:z-20'
          ]"
          >
            {{ index }}
          </inertia-link>
          <div
            v-if="currentPage == lastPage" class="
              relative
              inline-flex
              items-center
              px-2
              py-2
              rounded-r-md
              border border-gray-300
              bg-white
              text-sm
              font-medium
              text-gray-400
              bg-gray-100
            "
          >
            <span class="sr-only">Next</span>
            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
          </div>
          <inertia-link
            v-else preserve-scroll :href="url(currentPage + 1)" class="
              relative
              inline-flex
              items-center
              px-2
              py-2
              rounded-r-md
              border border-gray-300
              bg-white
              text-sm
              font-medium
              text-gray-500
              hover:bg-gray-50
            "
          >
            <span class="sr-only">Next</span>
            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
          </inertia-link>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/solid'

export default {
  components: {
    ChevronLeftIcon,
    ChevronRightIcon,
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
        this.currentPage = parseInt(search_params.get('page'))
          ? parseInt(search_params.get('page'))
          : 1
        this.from = this.perPage * this.currentPage - this.perPage + 1
        this.to =
          this.from +
          (this.perPage + this.from > this.totalResults
            ? this.totalResults - this.from
            : this.perPage - 1);

        (this.lastPage = Math.ceil(this.totalResults / this.perPage)),
        this.setPages(this.currentPage, this.lastPage)
        return
      }
      this.perPage = this.data.per_page
      this.totalResults = this.data.total
      this.from = this.data.from
      this.to = this.data.to
      this.currentPage = this.data.current_page;
      (this.lastPage = Math.ceil(this.totalResults / this.perPage)),
      this.setPages(this.currentPage, this.lastPage)
    },

    setPages(currentPage, pageCount) {
      let delta = 1,
        left = currentPage - delta,
        right = currentPage + delta + 1,
        result = []

      result = Array.from({ length: pageCount }, (v, k) => k + 1).filter(
        (i) => i && i >= left && i < right,
      )

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
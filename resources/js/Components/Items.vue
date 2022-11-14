<template>
  <table-filter-header :title="title" />
  <active-filters :prop-filters="filters" :filter-route="filterRoute" />
  <div class="flex flex-col items-center">
    <div
      class="
        leading-4
        text-center
        bg-white
        shadow
        rounded-md
        sm:overflow-hidden
        w-full
      "
    >
      <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
          <div class="shadow overflow-hidden border-b border-gray-200 rounded-md">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <th
                  v-for="(values, keys) in currentTable.head" :key="keys" scope="col" class="
                    t-header
                    px-6
                    py-3
                    text-left text-xs
                    font-medium
                    text-gray-500
                    uppercase
                    tracking-wider
                  " :class="{
                    'arrow-down':
                      sort.sortType == values &&
                      sort.sortOrder == 'DESC',
                    'arrow-up':
                      sort.sortType == values &&
                      sort.sortOrder == 'ASC',
                  }" @click="sortHeader(values)"
                >
                  {{ values }}
                </th>
              </thead>
              <tbody>
                <tr
                  v-for="(values, key) in currentTable.body" :key="key"
                  :class="key % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                >
                  <td
                    v-for="(values, key) in values" :key="key" class="
                      first:font-bold
                      px-6
                      py-2
                      whitespace-nowrap
                      text-sm
                      font-medium
                      text-gray-900 text-left
                    "
                  >
                    <button v-if="values.specialId" @click="goTo(values.route, values.specialId)">
                      {{ values.value }}
                    </button>
                    <span v-else> {{ values.value }}{{ values.type }} </span>
                  </td>
                </tr>
              </tbody>
            </table>
            <div
              v-if="
                currentTable &&
                  currentTable.body &&
                  currentTable.body.length == 0
              " class="
                bg-white
                col-span-1
                px-6
                py-4
                whitespace-nowrap
                text-sm
                font-medium
                text-gray-900 text-center
              "
            >
              No results
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <Pagination
    v-if="currentTable && currentTable.body && currentTable.body.length > 0" :data="currentTable.body"
    :per-page-prop="pagination.perPage" :total-results-prop="pagination.totalResults" :custom="true"
    :type-id="currentTab ? currentTab.option : ''"
  />
</template>

<script setup>
import ActiveFilters from '@/PageComponents/ActiveFilters.vue'
import pickBy from 'lodash/pickBy'
import TableFilterHeader from '@/PageComponents/TableFilterHeader.vue'
import Pagination from '@/PageComponents/Pagination.vue'
import { Inertia } from '@inertiajs/inertia'
import emitter from '@/Plugins/mitt'
import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'

const props = defineProps({
  table: Object,
  sort: Object,
  filters: Array,
  pagination: Object,
  errors: Object,
  title: String,
  route: Object,
})

const filterRoute = ref(null)
const currentTable = computed(() => props.table)

const mainUrl = [usePage().props.value.userInfo.user.username].concat(props.route.id)
const params = mainUrl.concat(pickBy({ sort: props.sort }))
filterRoute.value = route(props.route.name,
  params,
)


emitter.on('filter:submit', () => {
  // this.getStats();
})

const getStats = () => {
  Inertia.get(
    route('competition', mainUrl),
    pickBy({
      sort: props.sort,
      filters: props.filters,
    }),
    {
      only: ['type', 'sort', 'filters', 'table'],
    },
  )
}

const sortHeader = (tableHeader) => {
  if (currentTable.value.body.length < 2) {
    return
  }

  if (props.sort.sortType != tableHeader) {
    props.sort.sortOrder = 'DESC'
  } else {
    props.sort.sortOrder =
      props.sort.sortOrder == 'DESC' ? 'ASC' : 'DESC'
  }
  props.sort.sortType = tableHeader

  getStats()
}

const goTo = (router, id) => {
  Inertia.get(
    route(router, [usePage().props.value.userInfo.user.username].concat(id)),
    pickBy({
      filters: props.filters,
    }),
    {
      preserveScroll: true,
      preserveState: true,
    },
  )
}
</script>

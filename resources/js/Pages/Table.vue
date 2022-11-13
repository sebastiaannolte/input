<template>
  <Layout :title="pageTitle" :errors="errors">
    <table-filter-header :title="type.name" />
    <span>
      <bets
        :bets="bets.bets"
        :filters="filters"
        :filter-route="
          route(filterRoute, [
            $page.props.userInfo.user.username,
            type.id,
          ])
        "
        :is-stats="true"
      />
    </span>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Authenticated.vue'
import Bets from '@/PageComponents/Bets.vue'
import TableFilterHeader from '@/PageComponents/TableFilterHeader.vue'
import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
  bets: Object,
  type: Object,
  filters: Array,
  filterRoute: String,
  title: String,
  errors: Object,
})

const pageTitle = ref(null)
const setPageTitle = () => {
  pageTitle.value = 'Your ' + props.title + ' stats'
  if (!usePage().props.value.userInfo.myPage) {
    pageTitle.value =
         usePage().props.value.userInfo.user.name + '\'s ' + props.title + ' stats'
  }
}
setPageTitle()
</script>

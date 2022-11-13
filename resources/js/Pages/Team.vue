<template>
  <Layout :title="teamName" :errors="errors">
    <Head :title="title" />
    <table-filter-header :title="teamName" />
    <bets
      :bets="bets.bets"
      :filters="filters"
      :filter-route="
        route('team', [$page.props.auth.user.username, team.id, league.id])
      "
      :is-stats="true"
    />
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Authenticated.vue'
import Bets from '@/PageComponents/Bets.vue'
import TableFilterHeader from '@/PageComponents/TableFilterHeader.vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  bets: Object,
  team: Object,
  league: Object,
  filters: Array,
  errors: Object,
})

const title = ref(null)

const setPageTitle = () => {
  title.value = 'Your team stats'
  if (!usePage().props.value.userInfo.myPage) {
    title.value = usePage().props.value.userInfo.user.name + '\'s team stats'
  }
}
    
setPageTitle()
const teamName = computed(() => {
  var teamName = props.team.name
  if (props.league) {
    teamName += ' - ' + props.league.name
  }
  return teamName
})
</script>

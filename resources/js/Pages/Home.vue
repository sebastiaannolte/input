<template>
  <Layout :errors="errors" :title="title">
    <stats :stats="stats" :upcomming-bets="upcommingBets" />
    <Card :hover="false" class="mb-4 bg-white p-4">
      <BetsTimeline :items="timeline" />
    </Card>
    <bets :bets="bets" :filters="filters" :filter-button="true" :filter-route="route('userhome', $page.props.userInfo.user.username)" />
  </Layout>
</template>

<script setup>
import BetsTimeline from '@/Components/BetsTimeline.vue'
import Layout from '@/Layouts/Authenticated.vue'
import Bets from '@/PageComponents/Bets.vue'
import Card from '@/PageComponents/Card.vue'
import Stats from '@/PageComponents/Stats.vue'
import emitter from '@/Plugins/mitt'
import { usePage } from '@inertiajs/inertia-vue3'
import { onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
  errors: Object,
  bets: Object,
  stats: Object,
  filters: Array,
  upcommingBets: Object,
  import: Object,
  timeline: Object,
})

const title = ref(null)
onMounted(() => {
  if (props.import) {
    emitter.emit('event:import', props.import)
  }
})

const setPageTitle = () => {
  title.value = 'Your bets'
  if (!usePage().props.value.userInfo.myPage) {
    title.value = 'Bets by ' + usePage().props.value.userInfo.user.name
  }
}

setPageTitle()
</script>

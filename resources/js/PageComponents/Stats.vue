<template>
  <div class="mb-2 grid grid-cols-1 gap-2 sm:mb-4 sm:grid-cols-2 sm:gap-4">
    <Card class="w-full bg-white text-center p-4">
      <span class="font-bold text-indigo-500">Next bets:</span>
      <div>
        <div v-for="(bet, key) in upcommingBets" :key="key" class="first:text-lg first:font-bold">
          <div class="cursor-pointer overflow-hidden truncate hover:text-gray-700 dark:text-slate-400" @click="scrollToElement(bet.id)">{{ moment(bet.date).format('MMM DD HH:mm') }} - {{ bet.event }}</div>
        </div>
        <div v-if="upcommingBets.length == 0">No upcomming bets</div>
      </div>
    </Card>
    <Card class="text-center bg-white p-4 grid grid-cols-2 sm:grid-cols-4 gap-2">

        <Card :hover="true" class="flex flex-1 flex-col bg-gray-200 p-2 sm:p-4 gap-2">
          <span>Bets</span>
          <span class="font-bold text-xl">{{ stats.totalBets }}</span>
        </Card>
        <Card :hover="true" class="flex flex-1 flex-col bg-gray-200 p-2 sm:p-4 gap-2">
          <span>ROI</span>
          <span class="font-bold text-xl">{{ stats.roi }}%</span>
        </Card>
        <Card :hover="true" class="flex flex-1 flex-col bg-gray-200 p-2 sm:p-4 gap-2">
          <span>Profit</span>
          <span class="font-bold text-xl">{{ stats.units }} units</span>
        </Card>
        <Card :hover="true" class="flex flex-1 flex-col bg-gray-200 p-2 sm:p-4 gap-2">
          <span>Won bets</span>
          <span class="font-bold text-xl">{{ stats.wonbets }} ({{ stats.winprecentage }}%)</span>
        </Card>

    </Card>
  </div>
</template>

<script setup>
import moment from 'moment'
import emitter from '@/Plugins/mitt'
import Card from './Card.vue';

defineProps({
  stats: Object,
  upcommingBets: Object,
})

const scrollToElement = (id) => {
  emitter.emit('event:scroll', id)
}
</script>

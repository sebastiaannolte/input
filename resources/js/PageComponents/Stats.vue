<template>
  <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="w-full rounded-md bg-white p-4 px-4 text-center leading-5 shadow dark:bg-slate-800 sm:overflow-hidden">
      <span class="font-bold text-indigo-500">Next bets:</span>
      <div>
        <div v-for="(bet, key) in upcommingBets" :key="key" class="first:text-lg first:font-bold">
          <div class="cursor-pointer overflow-hidden truncate hover:text-gray-700 dark:text-slate-400" @click="scrollToElement(bet.id)">{{ moment(bet.date).format('MMM DD HH:mm') }} - {{ bet.event }}</div>
        </div>
        <div v-if="upcommingBets.length == 0">No upcomming bets</div>
      </div>
    </div>
    <div class="w-full rounded-md bg-white p-4 px-4 text-center leading-4 shadow dark:bg-slate-800 dark:text-slate-400 sm:overflow-hidden">
      <div class="flex h-full flex-row justify-center sm:items-center">
        <div class="flex flex-1 flex-col">
          <span>Bets</span>
          <span class="pt-3 font-bold">{{ stats.totalBets }}</span>
        </div>
        <div class="flex flex-1 flex-col">
          <span>ROI</span>
          <span class="pt-3 font-bold">{{ stats.roi }}%</span>
        </div>
        <div class="flex flex-1 flex-col">
          <span>Profit</span>
          <span class="pt-3 font-bold">{{ stats.units }} units</span>
        </div>
        <div class="flex flex-1 flex-col">
          <span>Won bets</span>
          <span class="pt-3 font-bold">{{ stats.wonbets }} ({{ stats.winprecentage }}%)</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Layout from '@/Layouts/Authenticated.vue'
import moment from 'moment'
import emitter from '@/Plugins/mitt'

defineProps({
  stats: Object,
  upcommingBets: Object,
})

const scrollToElement = (id) => {
  emitter.emit('event:scroll', id)
}
</script>

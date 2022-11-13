<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div
      class="
        px-4
        w-full
        leading-5
        text-center
        bg-white
        p-4
        shadow
        rounded-md
        sm:overflow-hidden
      "
    >
      <span class="text-indigo-500 font-bold">Next bets:</span>
      <div>
        <div
          v-for="(bet, key) in upcommingBets"
          :key="key"
          class="first:text-lg first:font-bold"
        >
          <div
            class="cursor-pointer hover:text-gray-700 truncate overflow-hidden"
            @click="scrollToElement(bet.id)"
          >
            {{ moment(bet.date).format("MMM DD HH:mm") }} - {{ bet.event }}
          </div>
        </div>
        <div v-if="upcommingBets.length == 0">No upcomming bets</div>
      </div>
    </div>
    <div
      class="
        px-4
        w-full
        leading-4
        text-center
        bg-white
        p-4
        shadow
        rounded-md
        sm:overflow-hidden
      "
    >
      <div class="flex justify-center sm:items-center flex-row h-full">
        <div class="flex flex-col flex-1">
          <span>Bets</span>
          <span class="pt-3 font-bold">{{ stats.totalBets }}</span>
        </div>
        <div class="flex flex-col flex-1">
          <span>ROI</span>
          <span class="pt-3 font-bold">{{ stats.roi }}%</span>
        </div>
        <div class="flex flex-col flex-1">
          <span>Profit</span>
          <span class="pt-3 font-bold">{{ stats.units }} units</span>
        </div>
        <div class="flex flex-col flex-1">
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
  console.log('123')
  emitter.emit('event:scroll', id)
}
</script>

<template>
  <span v-if="title">{{ title }}</span>
  <div class="flex w-full gap-8 overflow-x-auto">
    <button @click="reset" class="text-blue-600 hover:underline">Reset</button>
    <div v-for="(item, idx) in timeline" :key="item.key" class="flex cursor-pointer flex-col items-center" @click="selectTimeline(idx)">
      <div>
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-800" :class="{ 'ring-4 ring-blue-400': selectedIndex === idx }">
          <span class="text-xs font-bold text-white">{{ item.count }}</span>
        </div>
      </div>
      <div class="max-w-[200px] text-center">
        <time class="text-sm">{{ item.key }}</time>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
})
console.log('Timeline:', groupTimeline(props.items),props.items)
// Group bets by date and time
function groupTimeline(rawItems) {
  const grouped = {}
  rawItems.forEach((bet) => {
    bet.key = bet.event
    bet.items = []
    bet.count = 1
    const date = new Date(bet.date).toISOString().split('T')[0]
    const time = new Date(bet.date).toISOString().split('T')[1].slice(0, 5) // HH:mm
    bet.title = date + ' ' + time
    if (!grouped[date]) grouped[date] = { key: date, count: 0, items: {} }
    if (!grouped[date].items[time]) grouped[date].items[time] = { key: time, count: 0, items: [] }
    grouped[date].items[time].items.push(bet)
    grouped[date].items[time].count = grouped[date].items[time].items.length
    grouped[date].items[time].title = date
    grouped[date].count++
  })

  const result = Object.values(grouped).sort((a, b) => a.key.localeCompare(b.key))

  result.forEach((dateGroup) => {
    dateGroup.items = Object.values(dateGroup.items)
    dateGroup.items.sort((a, b) => a.key.localeCompare(b.key))
  })

  return result
}

const getTitle = (items) => {
  const item = items[0].title
  if (item) {
    return item
  }
  return 'Bets Timeline'
}

const origionalTimeline = ref(groupTimeline(props.items))
const timeline = ref(groupTimeline(props.items))
const title = ref('Bets Timeline')
function selectTimeline(idx) {
  if (timeline.value[idx].items && timeline.value[idx].items.length > 0) {
    timeline.value = timeline.value[idx].items
    title.value = getTitle(timeline.value)
  }
}

const reset = () => {
  timeline.value = origionalTimeline.value
  title.value = getTitle(timeline.value)
}
</script>

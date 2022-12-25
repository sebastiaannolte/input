<template>
  <span>
    <span v-if="result" class="flex items-center justify-between">
      <span class="inline-flex rounded-full px-2 text-xs leading-5" :class="[statusColor(status, 'label')]">
        {{ result }}
      </span>
    </span>
    <span v-else class="inline-flex rounded-full bg-yellow-100 px-2 text-xs leading-5 text-yellow-800">
      {{ status }}
    </span>
  </span>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  status: String,
  result: String,
})

const statuses = ref({
  new: {
    border: 'dark:border-slate-200/20',
    label: 'bg-yellow-100 text-yellow-800',
  },
  won: {
    border: 'border-green-500',
    label: 'bg-green-100 text-green-800',
  },
  lost: {
    border: 'border-red-500',
    label: 'bg-red-100 text-red-800',
  },
  void: {
    border: 'border-gray-300 dark:border-slate-200/20',
    label: 'bg-gray-100 text-gray-800',
  },
  halfwon: {
    border: 'border-green-500',
    label: 'bg-green-100 text-green-800',
  },
  halflost: {
    border: 'border-red-500',
    label: 'bg-red-100 text-red-800',
  },
})

const statusColor = (status, type) => {
  return statuses.value[status][type]
}
</script>

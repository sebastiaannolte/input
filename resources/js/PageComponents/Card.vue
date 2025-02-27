<template>
  <span class="block transition-all border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] rounded-lg"
    :class="{ 'hover:translate-y-[1px] hover:translate-x-[1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]': hover }">
    <slot></slot>
  </span>
</template>

<script setup>
const props = defineProps({
  hover: {
    type: Boolean,
    default: false
  }
})
</script>
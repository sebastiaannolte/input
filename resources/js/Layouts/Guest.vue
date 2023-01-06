<template>
  <div class="pt-20 pb-5 sm:mx-auto sm:w-full sm:max-w-md">
    <logo class="mx-auto h-16 w-auto fill-current text-blue-600" />
  </div>
  <div class="mx-auto max-w-md px-4 pt-4 pb-12 lg:pb-16">
    <slot />
  </div>
</template>

<script>
export default {
  methods: {},
}
</script>

<script setup>
import Logo from '@/Components/Logo.vue'
</script>

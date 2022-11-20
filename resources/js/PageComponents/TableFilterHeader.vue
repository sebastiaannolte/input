<template>
  <div class="mb-4 flex items-center justify-between">
    <div class="min-w-0 flex-1">
      <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
        {{ title }}
      </h2>
    </div>
  </div>
</template>

<script>
import Layout from '@/Layouts/Authenticated.vue'

export default {
  components: {},
  layout: Layout,

  props: {
    title: String,
  },
  data() {
    return {}
  },

  created() {},

  methods: {},
}
</script>

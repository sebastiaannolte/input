<template>
  <button
    :disabled="loading"
    class="
      flex
      items-center
      bg-red-500
      border border-transparent
      rounded-md
      shadow-sm
      py-2
      px-4
      inline-flex
      justify-center
      text-sm
      font-medium
      text-white
      hover:bg-red-600
      focus:outline-none
      focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
    "
    v-bind="$attrs"
  >
    <div v-if="loading" class="btn-spinner mr-2" />
    <slot />
  </button>
</template>

<script>
export default {
  props: {
    loading: Boolean,
  },
}
</script>
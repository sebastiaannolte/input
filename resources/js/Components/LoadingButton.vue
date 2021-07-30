<template>
  <button
    :disabled="loading"
    class="
      flex
      items-center
      bg-indigo-600
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
      hover:bg-indigo-700
      focus:outline-none
      focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
    "
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
};
</script>
<template>
  <button
    :type="type"
    class="
      flex
      items-center
      text-center
      justify-center
      px-4
      py-2
      border border-transparent
      rounded-md
      shadow-sm
      text-sm
      font-medium
      text-white
      bg-indigo-600
      hover:bg-indigo-700
      focus:outline-none
      focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
    "
  >
    <slot />
  </button>
</template>

<script>
export default {
  props: {
    type: {
      type: String,
      default: 'submit',
    },
  },
}
</script>

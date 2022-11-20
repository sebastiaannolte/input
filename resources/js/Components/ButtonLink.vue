<template>
  <inertia-link :href="href" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-black shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 dark:border-slate-200/20 dark:bg-slate-400/10 dark:text-slate-400 dark:hover:bg-slate-400/20">
    <slot />
  </inertia-link>
</template>

<script>
export default {
  props: {
    href: {
      type: String,
    },
  },
}
</script>

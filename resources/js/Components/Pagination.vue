<template>
  <div class="mt-3 -mb-1 flex flex-wrap">
    <template v-for="(link, key) in links" :key="key">
      <div
        v-if="link.url === null"
        v-html="link.label"
        class="
          mr-1
          mb-1
          px-4
          py-1
          text-sm
          border
          rounded
          text-gray-400
          bg-gray-100
        "
        :class="{ 'ml-auto': link.label === 'Next' }"
      ></div>
      <div
        v-else-if="!hasData"
        v-html="link.label"
        class="mr-1 mb-1 px-4 py-1 text-sm border rounded text-back bg-gray-200"
        :class="{ 'ml-auto': link.label === 'Next' }"
      ></div>
      <inertia-link
        v-else
        :key="key"
        class="
          mr-1
          mb-1
          px-4
          py-1
          text-sm
          border
          rounded
          hover:bg-white
          bg-white
          focus:border-indigo-500
          focus:text-indigo-500
        "
        :class="{
          'bg-gray-300': link.active,
          'ml-auto': link.label === 'Next',
        }"
        :href="link.url"
        preserve-scroll
        >{{ link.label }}</inertia-link
      >
    </template>
  </div>
</template>

<script>
export default {
  props: {
    links: Array,
    hasData: Boolean,
  },
};
</script>
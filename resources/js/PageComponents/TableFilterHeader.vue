<template>
  <div class="flex items-center justify-between mb-4">
    <div class="flex-1 min-w-0">
      <h2
        class="
          text-2xl
          font-bold
          leading-7
          text-gray-900
          sm:text-3xl
          sm:truncate
        "
      >
        {{ title }}
      </h2>
    </div>
    <!-- <div class="mt-4 flex md:mt-0 md:ml-4">
       <slot name="button"></slot>
      <show-filter-button />
    </div> -->
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import ShowFilterButton from "@/Components/ShowFilterButton";

export default {
  layout: Layout,

  components: {
    ShowFilterButton,
  },

  props: {
    title: String,
  },
  data() {
    return {};
  },

  created() {},

  methods: {},
};
</script>

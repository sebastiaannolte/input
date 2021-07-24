<template>
  <div class="mt-3 -mb-1 flex flex-wrap">
    <div
      @click="toPage(modelValue - 1)"
      class="mr-1 mb-1 px-4 py-1 text-sm border rounded bg-white"
      :class="{
        'opacity-50 cursor-default': modelValue == 1,
        'cursor-pointer': modelValue != 1,
      }"
    >
      «
    </div>

    <template v-for="index in pages" :key="index">
      <div
        v-if="index"
        class="
          mr-1
          mb-1
          px-4
          py-1
          text-sm
          border
          rounded
          bg-white
          cursor-pointer
        "
        :class="{
          'bg-indigo-500 text-white pointer-events-none': index == modelValue,
        }"
        @click="toPage(index)"
      >
        {{ index }}
      </div>
    </template>
    <div
      @click="toPage(modelValue + 1)"
      class="mr-1 mb-1 px-4 py-1 text-sm border rounded bg-white"
      :class="{
        'opacity-50 cursor-default': modelValue == pages,
        'cursor-pointer': modelValue != pages,
      }"
    >
      »
    </div>
  </div>
</template>

<script>
export default {
  props: {
    itemCount: Number,
    perPage: Number,
    modelValue: String,
  },

  emits: ["update:modelValue"],

  data() {
    return {
      currentPage: 1,
      pages: Math.ceil(this.itemCount / this.perPage),
    };
  },

  methods: {
    toPage(pageNumber) {
      if (pageNumber > this.pages || pageNumber < 1) {
        return;
      }
      this.currentPage = pageNumber;
      this.$emit("update:modelValue", pageNumber);
    },
  },
};
</script>
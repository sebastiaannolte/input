  
<template>
  <div>
    <label
      v-if="label"
      class="block text-sm font-medium text-gray-700 dark:text-gray-400"
      :for="id"
      >{{ label }}:</label
    >
    <div class="flex rounded-md shadow-sm">
      <input
        :id="id"
        ref="input"
        autocomplete="off"
        v-bind="{ ...$attrs, class: null }"
        name="company-website"
        class="
          flex-1
          min-w-0
          block
          w-full
          px-3
          py-2
          rounded-none rounded-l-md
          focus:ring-indigo-500
          focus:border-indigo-500
          sm:text-sm
          border-gray-300
        "
        :class="{ error: error }"
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
      />
      <span
        class="
          inline-flex
          items-center
          px-3
          rounded-r-md
          border border-l-0 border-gray-300
          bg-gray-50
          text-gray-500
          sm:text-sm
        "
      >
        {{ addOn }}
      </span>
    </div>
    <div v-if="error" class="form-error text-gray-400">
      <small>{{ error }}</small>
    </div>
  </div>
</template>


<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `select-input-${Math.random() * 1000}`;
      },
    },
    type: {
      type: String,
      default: "text",
    },
    addOn: String,
    modelValue: String,
    label: String,
    error: String,
  },
  methods: {
    focus() {
      this.$refs.input.focus();
    },
    select() {
      this.$refs.input.select();
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end);
    },
  },
};
</script>
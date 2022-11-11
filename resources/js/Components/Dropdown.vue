  
<template>
  <div>
    <label
      v-if="label"
      class="block text-sm font-medium capitalize text-gray-700 dark:text-gray-400"
      :for="id"
      >{{ label }}:</label
    >

    <select
      :id="id"
      ref="input"
      v-bind="{ ...$attrs, class: null }"
      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-400 sm:text-sm"
      :class="{ error: error }"
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    >
      <option v-for="(option, key) in options" :value="key" :key="key">
        {{ option }}
      </option>
    </select>
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
    modelValue: String,
    label: String,
    error: String,
    options: Object,
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
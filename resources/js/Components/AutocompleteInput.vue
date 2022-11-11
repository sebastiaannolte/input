  
<template>
  <label
    v-if="label"
    class="block text-sm font-medium capitalize text-gray-700 dark:text-gray-400"
    :for="id"
    >{{ label }}:</label
  >
  <div class="relative" style="height: 38px">
    <input
      @input="check"
      @keydown.enter.prevent="onEnter"
      @keydown.tab="onTab"
      @blur="outOfFocus"
      v-model="autocomplete"
      ref="input"
      autocomplete="off"
      v-bind="{ ...$attrs, class: null }"
      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-400 sm:text-sm"
      :class="{ error: error }"
      :type="type"
    />
    <input
      type="text"
      class="autocomplete block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-400 sm:text-sm"
      disabled
      v-model="placeholderValue"
    />
  </div>
  <div v-if="error" class="form-error text-gray-400">
    <small>{{ error }}</small>
  </div>
</template>


<script>
import { computed } from "vue";
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
    options: Array,
  },

  setup(props, { emit }) {
    const autocomplete = computed({
      get: () => props.modelValue,
      set: (value) => emit("update:modelValue", value),
    });

    return {
      autocomplete,
    };
  },

  data() {
    return {
      placeholderValue: null,
      localOptions: {},
      foundOption: null,
    };
  },

  created() {
    this.localOptions = this.options;
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
    check(e) {
      this.foundOption = this.localOptions.find((option) => {
        return option.startsWith(e.target.value);
      }, e.target.value);
      if (e.target.value) {
        this.placeholderValue = this.foundOption;
      } else {
        this.placeholderValue = null;
      }

      // this.$emit("update:modelValue", e.target.value);
    },

    onEnter() {
      if (this.foundOption) {
        this.autocomplete = this.foundOption;
      }
    },

    onTab() {
      if (this.foundOption) {
        this.autocomplete = this.foundOption;
      }
    },

    outOfFocus() {
      this.placeholderValue = null;
    },
  },
};
</script>

<style scoped>
input {
  position: absolute;
  z-index: 7;
  background-color: transparent;
}

.autocomplete {
  color: silver;
  z-index: 1;
}
</style>
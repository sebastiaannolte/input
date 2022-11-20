<template>
  <label v-if="label" class="block text-sm font-medium capitalize text-gray-700 dark:text-slate-400" :for="id">{{ label }}:</label>
  <div class="relative" style="height: 38px">
    <input ref="input" v-model="autocomplete" autocomplete="off" v-bind="{ ...$attrs, class: null }" class="block w-full appearance-none rounded-md border border-transparent bg-white py-2 px-3 leading-5 text-slate-900 shadow ring-1 ring-slate-900/5 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:text-slate-400 dark:ring-slate-200/20 dark:focus:ring-sky-500 sm:text-sm" :class="{ error: error }" :type="type" @input="check" @keydown.enter.prevent="onEnter" @keydown.tab="onTab" @blur="outOfFocus" />
    <input v-model="placeholderValue" type="text" class="autocomplete block w-full appearance-none rounded-md border border-transparent bg-white py-2 px-3 leading-5 text-slate-900 shadow ring-1 ring-slate-900/5 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-slate-700/20 dark:text-slate-400 dark:ring-slate-200/20 dark:focus:ring-sky-500 sm:text-sm" disabled />
  </div>
  <div v-if="error" class="form-error text-gray-400">
    <small>{{ error }}</small>
  </div>
</template>

<script>
import { computed } from 'vue'
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `autocomplete-input-${Math.random() * 1000}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    modelValue: String,
    label: String,
    error: String,
    options: Array,
  },

  setup(props, { emit }) {
    const autocomplete = computed({
      get: () => props.modelValue,
      set: (value) => emit('update:modelValue', value),
    })

    return {
      autocomplete,
    }
  },

  data() {
    return {
      placeholderValue: null,
      localOptions: {},
      foundOption: null,
    }
  },

  created() {
    this.localOptions = this.options
  },

  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
    check(e) {
      this.foundOption = this.localOptions.find((option) => {
        return option.startsWith(e.target.value)
      }, e.target.value)
      if (e.target.value) {
        this.placeholderValue = this.foundOption
      } else {
        this.placeholderValue = null
      }
    },

    onEnter() {
      if (this.foundOption) {
        this.autocomplete = this.foundOption
      }
    },

    onTab() {
      if (this.foundOption) {
        this.autocomplete = this.foundOption
      }
    },

    outOfFocus() {
      this.placeholderValue = null
    },
  },
}
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

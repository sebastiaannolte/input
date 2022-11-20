<template>
  <label class="block text-sm font-medium text-gray-700 dark:text-slate-400">{{ label }}</label>
  <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" class="block w-full rounded-md border border-transparent py-2 pl-3 pr-10 text-base capitalize ring-1 ring-slate-900/5 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-slate-700/20 dark:ring-slate-200/20 dark:focus:ring-sky-500 sm:text-sm">
    <slot />
  </select>
  <div v-if="error" class="form-error text-gray-400">
    <small>{{ error }}</small>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `select-input-${Math.random() * 1000}`
      },
    },
    type: {
      type: [String, Number],
      default: 'text',
    },
    modelValue: String,
    label: String,
    error: String,
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
  },
}
</script>

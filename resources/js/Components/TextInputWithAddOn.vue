<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-slate-400" :for="id">{{ label }}:</label>
    <div class="flex rounded-md shadow-sm">
      <input :id="id" ref="input" autocomplete="off" v-bind="{ ...$attrs, class: null }" class="block w-full appearance-none rounded-l-md border border-gray-300 bg-white py-2 px-3 leading-5 text-slate-900 shadow ring-slate-900/5 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 dark:border-slate-200/20 dark:bg-slate-700/20 dark:text-slate-400 dark:ring-slate-200/20 dark:focus:ring-sky-500 sm:text-sm" :class="{ error: error }" :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" />
      <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 dark:border-slate-200/20 dark:bg-slate-700/20 dark:text-slate-400 sm:text-sm">
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
        return `select-input-${Math.random() * 1000}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    addOn: String,
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

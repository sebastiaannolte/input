  
<template>
  <div>
    <label
      v-if="label"
      class="
        block
        text-sm
        font-medium
        text-gray-700
        capitalize
      "
      :for="id"
    >{{ label }}:</label>

    <select
      :id="id"
      ref="input"
      v-bind="{ ...$attrs, class: null }"
      class="
        shadow-sm
        focus:ring-indigo-500
        focus:border-indigo-500
        block
        w-full
        sm:text-sm
        border-gray-300
        rounded-md
      "
      :class="{ error: error }"
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    >
      <option v-for="(option, key) in options" :key="key" :value="key">
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
        return `select-input-${Math.random() * 1000}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    modelValue: String,
    label: String,
    error: String,
    options: Object,
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
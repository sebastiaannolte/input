<template>
  <div class="flex justify-center">
    <div class="mb-4 w-full rounded-md bg-white p-10 leading-4 shadow dark:bg-slate-800 sm:overflow-hidden">
      <div class="mb-4 text-sm text-gray-600 dark:text-slate-400">Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</div>

      <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
      </div>

      <breeze-validation-errors class="mb-4" />

      <form @submit.prevent="submit">
        <div>
          <text-input id="email" v-model="form.email" :error="errors" label="Email" type="email" required autofocus autocomplete="username" />
        </div>

        <div class="mt-4 flex items-center justify-end">
          <Button class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Email Password Reset Link </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import BreezeButton from '@/Components/Button.vue'
import Layout from '@/Layouts/Guest.vue'
import BreezeInput from '@/Components/Input.vue'
import BreezeLabel from '@/Components/Label.vue'
import BreezeValidationErrors from '@/Components/ValidationErrors.vue'
import Button from '@/Components/Button.vue'
import TextInput from '@/Components/TextInput.vue'

export default {
  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Button,
    TextInput,
  },
  layout: Layout,

  props: {
    status: String,
  },

  data() {
    return {
      form: this.$inertia.form({
        email: '',
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.route('password.email'))
    },
  },
}
</script>

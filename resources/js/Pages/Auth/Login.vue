<template>
  <Head title="Login" />
  <div class="">
    <div class="mb-4 rounded-md bg-white p-10 leading-4 shadow dark:bg-slate-800 sm:overflow-hidden">
      <breeze-validation-errors class="mb-4" />
      <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
      </div>
      <h2 class="mb-6 text-lg leading-9 text-gray-900 dark:text-slate-200">Sign in to your account</h2>
      <form @submit.prevent="submit">
        <div>
          <text-input id="username" v-model="form.email" :error="errors" label="Email" type="email" required autofocus autocomplete="username" />
        </div>
        <div class="mt-4">
          <text-input id="password" v-model="form.password" type="password" :error="errors" label="Password" required autocomplete="current-password" />
          <inertia-link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-slate-400"> Forgot your password? </inertia-link>
        </div>

        <div class="mt-4 block">
          <label class="flex items-center">
            <breeze-checkbox v-model:checked="form.remember" name="remember" class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-blue-600 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
            <span class="ml-2 text-sm text-gray-600 dark:text-slate-400">Remember me</span>
          </label>
        </div>

        <div class="mt-4 flex items-center justify-end">
          <Button class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Log in </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import BreezeButton from '@/Components/Button.vue'
import Layout from '@/Layouts/Guest.vue'
import BreezeInput from '@/Components/Input.vue'
import BreezeCheckbox from '@/Components/Checkbox.vue'
import BreezeLabel from '@/Components/Label.vue'
import BreezeValidationErrors from '@/Components/ValidationErrors.vue'
import Button from '@/Components/Button.vue'
import TextInput from '@/Components/TextInput.vue'

export default {
  components: {
    BreezeButton,
    BreezeInput,
    BreezeCheckbox,
    BreezeLabel,
    BreezeValidationErrors,
    Button,
    TextInput,
  },
  layout: Layout,

  props: {
    canResetPassword: Boolean,
    status: String,
  },

  data() {
    return {
      form: this.$inertia.form({
        email: '',
        password: '',
        remember: false,
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.route('login'), {
        onFinish: () => this.form.reset('password'),
      })
    },
  },
}
</script>

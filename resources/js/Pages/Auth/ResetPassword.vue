<template>
  <div class="flex justify-center">
    <div
      class="
        mb-4
        leading-4
        bg-white
        p-10
        shadow
        rounded-md
        sm:overflow-hidden
        w-full
      "
    >
      <breeze-validation-errors class="mb-4" />

      <form @submit.prevent="submit">
        <div>
          <breeze-label for="email" value="Email" />
          <breeze-input
            id="email"
            v-model="form.email"
            type="email"
            class="mt-1 block w-full"
            required
            autofocus
            autocomplete="username"
          />
        </div>

        <div class="mt-4">
          <breeze-label for="password" value="Password" />
          <breeze-input
            id="password"
            v-model="form.password"
            type="password"
            class="mt-1 block w-full"
            required
            autocomplete="new-password"
          />
        </div>

        <div class="mt-4">
          <breeze-label for="password_confirmation" value="Confirm Password" />
          <breeze-input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            class="mt-1 block w-full"
            required
            autocomplete="new-password"
          />
        </div>

        <div class="flex items-center justify-end mt-4">
          <Button
            class="w-full"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Reset Password
          </Button>
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

export default {

  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Layout,
    Button,
  },
  layout: Layout,

  props: {
    email: String,
    token: String,
  },

  data() {
    return {
      form: this.$inertia.form({
        token: this.token,
        email: this.email,
        password: '',
        password_confirmation: '',
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.route('password.update'), {
        onFinish: () => this.form.reset('password', 'password_confirmation'),
      })
    },
  },
}
</script>

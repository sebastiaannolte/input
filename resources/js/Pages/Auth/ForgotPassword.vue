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
      <div class="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Just let us know your email address
        and we will email you a password reset link that will allow you to
        choose a new one.
      </div>

      <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
      </div>

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

        <div class="flex items-center justify-end mt-4">
          <Button
            class="w-full"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Email Password Reset Link
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
    Button,
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

<template>
  <div class="flex justify-center">
    <div
      class="
        px-4
        mb-4
        leading-4
        bg-white
        p-4
        shadow
        rounded-md
        sm:overflow-hidden
        w-full
        sm:w-2/3
      "
    >
      <breeze-validation-errors class="mb-4" />

      <form @submit.prevent="submit">
        <div>
          <breeze-label for="name" value="Name" />
          <breeze-input
            id="name"
            v-model="form.name"
            type="text"
            class="mt-1 block w-full"
            required
            autofocus
            autocomplete="name"
          />
        </div>
        <div>
          <breeze-label for="username" value="Username" />
          <breeze-input
            id="username"
            v-model="form.username"
            type="text"
            class="mt-1 block w-full"
            required
            autofocus
            autocomplete="username"
          />
        </div>

        <div class="mt-4">
          <breeze-label for="email" value="Email" />
          <breeze-input
            id="email"
            v-model="form.email"
            type="email"
            class="mt-1 block w-full"
            required
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
          <inertia-link
            :href="route('login')"
            class="underline text-sm text-gray-600 hover:text-gray-900"
          >
            Already registered?
          </inertia-link>

          <breeze-button
            class="ml-4"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Register
          </breeze-button>
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

export default {

  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
  },
  layout: Layout,

  data() {
    return {
      form: this.$inertia.form({
        name: '',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: false,
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.route('register'), {
        onFinish: () => this.form.reset('password', 'password_confirmation'),
      })
    },
  },
}
</script>

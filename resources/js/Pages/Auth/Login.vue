<template>
  <Head title="Login" />
  <div class="">
    <div
      class="mb-4 leading-4 bg-white p-10 shadow rounded-md sm:overflow-hidden"
    >
      <breeze-validation-errors class="mb-4" />
      <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
      </div>
      <h2 class="mb-6 text-lg leading-9 text-gray-900">
        Sign in to your account
      </h2>
      <form @submit.prevent="submit">
        <div>
          <breeze-label for="email" value="Email" />
          <breeze-input
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autofocus
            autocomplete="username"
          />
        </div>
        <div class="mt-4">
          <div class="flex justify-between">
            <breeze-label for="password" value="Password" />
            <inertia-link
              v-if="canResetPassword"
              :href="route('password.request')"
              class="text-sm text-gray-600 underline hover:text-gray-900"
            >
              Forgot your password?
            </inertia-link>
          </div>
          <breeze-input
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="current-password"
          />
        </div>

        <div class="mt-4 block">
          <label class="flex items-center">
            <breeze-checkbox name="remember" v-model:checked="form.remember" />
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
          </label>
        </div>

        <div class="mt-4 flex items-center justify-end">
          <Button
            class="w-full"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Log in
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import BreezeButton from "@/Components/Button.vue";
import Layout from "@/Layouts/Guest.vue";
import BreezeInput from "@/Components/Input.vue";
import BreezeCheckbox from "@/Components/Checkbox.vue";
import BreezeLabel from "@/Components/Label.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import Button from "@/Components/Button.vue";

export default {
  layout: Layout,

  components: {
    BreezeButton,
    BreezeInput,
    BreezeCheckbox,
    BreezeLabel,
    BreezeValidationErrors,
    Button,
  },

  props: {
    canResetPassword: Boolean,
    status: String,
  },

  data() {
    return {
      form: this.$inertia.form({
        email: "",
        password: "",
        remember: false,
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("login"), {
        onFinish: () => this.form.reset("password"),
      });
    },
  },
};
</script>

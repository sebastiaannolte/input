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
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autofocus
            autocomplete="username"
          />
        </div>

        <div class="mt-4">
          <breeze-label for="password" value="Password" />
          <breeze-input
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="new-password"
          />
        </div>

        <div class="mt-4">
          <breeze-label for="password_confirmation" value="Confirm Password" />
          <breeze-input
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password_confirmation"
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
import BreezeButton from "@/Components/Button";
import Layout from "@/Layouts/Guest";
import BreezeInput from "@/Components/Input";
import BreezeLabel from "@/Components/Label";
import BreezeValidationErrors from "@/Components/ValidationErrors";
import Button from "@/Components/Button.vue";

export default {
  layout: Layout,

  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Layout,
    Button,
  },

  props: {
    email: String,
    token: String,
  },

  data() {
    return {
      form: this.$inertia.form({
        token: this.token,
        email: this.email,
        password: "",
        password_confirmation: "",
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("password.update"), {
        onFinish: () => this.form.reset("password", "password_confirmation"),
      });
    },
  },
};
</script>

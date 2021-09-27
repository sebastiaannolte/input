<template>
  <Head title="Profile" />
  <div class="shadow sm:rounded-md sm:overflow-hidden">
    <div class="bg-white py-6 px-4 sm:p-6">
      <div>
        <h2
          id="payment-details-heading"
          class="text-xl leading-6 font-medium text-gray-900 font-bold"
        >
          Profile
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          The default setting are used when adding new bets
        </p>
      </div>
      <div class="mt-6 grid grid-cols-4 gap-4">
        <div class="col-span-4 sm:col-span-2">
          <text-input
            v-model="userData.username"
            :error="errors.username"
            label="Username"
          />
        </div>

        <div class="col-span-4 sm:col-span-2">
          <text-input
            v-model="userData.name"
            :error="errors.name"
            label="Name"
          />
        </div>
        <div class="col-span-4 sm:col-span-2">
          <text-input
            v-model="userData.email"
            :error="errors.email"
            label="Email"
          />
        </div>
      </div>
    </div>
    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
      <loading-button @click.prevent="save" :loading="userData.processing">
        Save
      </loading-button>
    </div>
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";
import LoadingButton from "@/Components/LoadingButton";

export default {
  components: { Button, TextInput, LoadingButton },
  layout: Layout,

  props: {
    errors: Object,
  },
  data() {
    return {};
  },

  created() {
    this.userData = this.$inertia.form(this.$page.props.auth.user);
  },

  methods: {
    save() {
      this.userData.put(
        this.route("profile.update", this.$page.props.auth.user.username),
        this.userData,
        {
          preserveScroll: true, // bets are not added to frontend
        }
      );
    },
  },
};
</script>

<template>
  <Head title="Settings" />
  <div class="shadow sm:rounded-md sm:overflow-hidden">
    <div class="bg-white py-6 px-4 sm:p-6">
      <div>
        <h2
          id="payment-details-heading"
          class="text-xl leading-6 font-medium text-gray-900 font-bold"
        >
         Default settings
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Update your billing information. Please note that updating your
          location could affect your tax rates.
        </p>
      </div>
      <div class="mt-6 grid grid-cols-4 gap-6">
        <div v-for="(value, key) in settings" :key="key">
          <div class="col-span-4 sm:col-span-2">
            <text-input
              v-model="newSettings[key]"
              class="
                mt-1
                block
                w-full
                border border-gray-300
                rounded-md
                shadow-sm
                py-2
                px-3
                focus:outline-none
                focus:ring-gray-900
                focus:border-gray-900
                sm:text-sm
              "
              :label="key"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
      <button
        @click.prevent="saveSettings"
        class="
          bg-gray-800
          border border-transparent
          rounded-md
          shadow-sm
          py-2
          px-4
          inline-flex
          justify-center
          text-sm
          font-medium
          text-white
          hover:bg-gray-900
          focus:outline-none
          focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
        "
      >
        Save
      </button>
    </div>
  </div>
</template>



<script>
import Layout from "@/Layouts/Authenticated";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";

export default {
  components: { Button, TextInput },
  layout: Layout,

  props: {},
  data() {
    return {
      settings: {},
      newSettings: {},
    };
  },

  created() {
    this.settings = this.$page.props.auth.settings;
    this.newSettings = this.settings;
  },

  methods: {
    saveSettings() {
      this.$inertia.post(
        this.route("userSettings.store", this.$page.props.auth.user.username),
        this.newSettings,
        {
          preserveScroll: true, // bets are not added to frontend
        }
      );
    },
  },

  computed: {},
};
</script>

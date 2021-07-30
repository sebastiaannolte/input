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
         The default setting are used when adding new bets
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
      <loading-button
        @click.prevent="saveSettings"
        :loading="newSettings.processing"
      >
        Save
      </loading-button>
    </div>
  </div>
</template>



<script>
import Layout from "@/Layouts/Authenticated";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";
import LoadingButton from '@/Components/LoadingButton';

export default {
  components: { Button, TextInput, LoadingButton },
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
    this.newSettings = this.$inertia.form(this.settings);
  },

  methods: {
    saveSettings() {
      this.loading = true;
      this.newSettings.post(
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

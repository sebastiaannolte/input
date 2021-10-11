<template>
  <Head title="Settings" />
  <form action="#" method="POST">
    <div class="shadow sm:rounded-md sm:overflow-hidden">
      <div class="bg-white py-6 px-4 sm:p-6 h-96">
        <div>
          <h2 class="text-xl leading-6 font-medium text-gray-900 font-bold">
            Settings
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            The settings are used when adding new bets
          </p>
        </div>
        <div class="mt-6 grid grid-cols-4 gap-4">
          <div
            class="col-span-4 sm:col-span-2"
            v-for="(value, key) in stringSettings"
            :key="key"
          >
            <text-input
              v-model="newSettings[key].value"
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
          <div class="col-span-4 sm:col-span-2">
            <div class="mt-1 flex rounded-md shadow-sm">
              <div
                class="relative flex items-stretch flex-grow focus-within:z-10"
              >
                <div
                  class="
                    absolute
                    inset-y-0
                    left-0
                    pl-3
                    flex
                    items-center
                    pointer-events-none
                  "
                ></div>
                <input
                  type="date"
                  v-model="date"
                  id="email"
                  class="
                    focus:ring-indigo-500
                    focus:border-indigo-500
                    block
                    w-full
                    rounded-none rounded-l-md
                    pl-3
                    sm:text-sm
                    border-gray-300
                  "
                />
              </div>
              <loading-button :loading="loadingGames" class="rounded-l-none" @click.prevent="getGames">
                <span>Get games</span>
              </loading-button>
            </div>
            <span>{{ gamesResponse }}</span>
          </div>
          <div class="col-span-4 sm:col-span-2">
            <Multiselect
              v-model="newSettings.bookmakers.value"
              mode="tags"
              :searchable="true"
              :createTag="true"
              addTagOn="'enter'|'space'|'tab'|';'|','"
              :options="bookmakerNames"
            />
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
  </form>
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";
import LoadingButton from "@/Components/LoadingButton";
import moment from "moment";
import Multiselect from "@vueform/multiselect";

export default {
  components: { Button, TextInput, LoadingButton, Multiselect },
  layout: Layout,

  props: {
    bookmakers: Object,
  },
  data() {
    return {
      settings: {},
      newSettings: {},
      date: null,
      gamesResponse: null,
      loadingGames: false,
      speiclaTabsFiltered: null,
    };
  },

  created() {
    this.moment = moment;
    this.date = moment().format("YYYY-MM-DD");
    this.settings = this.$page.props.auth.settings;
    this.newSettings = this.$inertia.form(this.settings);
  },

  methods: {
    saveSettings() {
      this.newSettings.post(
        this.route("userSettings.store", this.$page.props.auth.user.username),
        {
          preserveScroll: true, // bets are not added to frontend
        }
      );
    },

    getGames() {
      this.loadingGames = true;
      this.$http.get(this.route("games.get", this.date)).then((response) => {
        this.loadingGames = false;
        if (response.data) {
          this.gamesResponse = response.data.message;
        }
      });
    },
  },

  computed: {
    bookmakerNames() {
      return this.bookmakers
        .map((el) => el.name)
        .sort((a, b) => a.localeCompare(b));
    },
    stringSettings() {
      return Object.fromEntries(
        Object.entries(this.settings).filter(
          ([key, value]) => value.type == "string"
        )
      );
    },
  },
};
</script>
<style src="@vueform/multiselect/themes/default.css"></style>

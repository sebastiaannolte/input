<template>
  <Head title="Settings" />
  <form action="#" method="POST">
    <div class="">
      <div class="py-6 px-4 sm:p-6 h-96">
        <settings-tabs />
        <div>
          <h2 class="text-xl font-medium font-bold leading-6 text-gray-900">
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
              class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm"
              :label="key"
            />
          </div>
          <div class="col-span-4 sm:col-span-2">
            <label
              class="block text-sm font-medium capitalize text-gray-700 dark:text-gray-400"
              >Games:</label
            >
            <div class="mt-1 flex rounded-md shadow-sm">
              <div
                class="relative flex flex-grow items-stretch focus-within:z-10"
              >
                <div
                  class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                ></div>
                <input
                  type="date"
                  v-model="date"
                  id="email"
                  class="block w-full rounded-none rounded-l-md border-gray-300 pl-3 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
              <button
                v-for="sport in sports"
                :key="sport.name"
                type="button"
                @click="setSportType(sport.name)"
                :class="{
                  'bg-gray-200 text-white inner-shadow':
                    activeSport == sport.name,
                }"
                class="inline-flex h-10 w-10 items-center rounded border border-gray-300 bg-white px-1.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:bg-gray-300 focus:outline-none"
              >
                <sport-icon class="h-6 w-6" :name="sport.name" />
              </button>
              <loading-button
                :loading="loadingGames"
                class="rounded-l-none"
                @click.prevent="getGames"
              >
                <span>Get games</span>
              </loading-button>
            </div>
            <span>{{ gamesResponse }}</span>
          </div>
          <div class="col-span-4 sm:col-span-2">
            <label
              class="block text-sm font-medium capitalize text-gray-700 dark:text-gray-400"
              >Bookies:</label
            >
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
      <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
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
import Layout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import Settings from "@/Pages/Settings.vue";
import moment from "moment";
import Multiselect from "@vueform/multiselect";
import SportIcon from "@/Components/SportIcon.vue";

export default {
  components: {
    Button,
    TextInput,
    LoadingButton,
    Multiselect,
    Settings,
    SportIcon,
  },
  layout: [Layout, Settings],

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
      activeSport: "football",
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

    setSportType(type) {
      this.activeSport = type;
    },

    getGames() {
      this.loadingGames = true;
      this.$http
        .get(this.route("games.get", [this.date, this.activeSport]))
        .then((response) => {
          this.loadingGames = false;
          if (response.data) {
            this.gamesResponse = response.data.message;
          }
        });
    },
  },

  computed: {
    sports() {
      return this.$page.props.sports;
    },
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

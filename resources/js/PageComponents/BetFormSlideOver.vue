<template>
  <TransitionRoot as="template" :show="open">
    <Dialog
      as="div"
      class="fixed inset-0 z-20 overflow-hidden"
      @close="open = false"
    >
      <div class="absolute inset-0 overflow-hidden">
        <DialogOverlay class="absolute inset-0" />

        <div class="fixed inset-y-0 right-0 flex max-w-full pl-16">
          <TransitionChild
            as="template"
            enter="transform transition ease-in-out duration-500 sm:duration-700"
            enter-from="translate-x-full"
            enter-to="translate-x-0"
            leave="transform transition ease-in-out duration-500 sm:duration-700"
            leave-from="translate-x-0"
            leave-to="translate-x-full"
          >
            <div class="w-screen max-w-md">
              <form
                class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl"
              >
                <div class="h-0 flex-1 overflow-y-auto">
                  <div class="bg-indigo-700 py-6 px-4 sm:px-6">
                    <div class="flex items-center justify-between">
                      <DialogTitle class="text-lg font-medium text-white">
                        {{ title }}
                      </DialogTitle>
                      <div class="ml-3 flex h-7 items-center">
                        <button
                          type="button"
                          class="rounded-md bg-indigo-700 text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                          @click="open = false"
                        >
                          <span class="sr-only">Close panel</span>
                          <XIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                      </div>
                    </div>
                    <div class="mt-1">
                      <p class="text-sm text-indigo-300">
                        Get started by filling in the information below to
                        create your new project.
                      </p>
                    </div>
                  </div>
                  <div class="space-y-6 lg:col-span-9">
                    <section aria-labelledby="payment-details-heading">
                      <form action="#" method="POST">
                        <div class="">
                          <div class="py-6 px-4 sm:p-6">
                            <div class="grid grid-cols-4 gap-4">
                              <span class="col-span-4">
                                <button
                                  v-for="sport in sports"
                                  :key="sport.name"
                                  type="button"
                                  @click="setSportType(sport.name)"
                                  :class="{
                                    'bg-gray-200 text-white inner-shadow':
                                      activeSport == sport.name,
                                  }"
                                  class="mr-2 inline-flex h-10 w-10 items-center rounded border border-gray-300 bg-white px-1.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:bg-gray-300 focus:outline-none"
                                >
                                  <sport-icon
                                    class="h-6 w-6"
                                    :name="sport.name"
                                  />
                                </button>
                              </span>
                              <div
                                class="col-span-4 sm:col-span-4"
                                v-if="renderComponent"
                              >
                                <Event
                                  v-for="(game, key) in addedGames"
                                  :key="key"
                                  v-model="games[key]"
                                  :errors="errors"
                                  :name="index"
                                  :game="game"
                                  :index="key"
                                  :isEdit="isEdit"
                                  :sport="activeSport"
                                >
                                </Event>
                                <div class="flex justify-end">
                                  <button
                                    class="inline-flex items-center rounded border border-transparent bg-indigo-600 px-1.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    @click.prevent="addGame"
                                  >
                                    <PlusIcon
                                      class="h-4 w-4"
                                      aria-hidden="true"
                                    />
                                  </button>
                                </div>
                              </div>
                              <div class="col-span-4 sm:col-span-2">
                                <text-input-with-add-on
                                  v-model="betData.stake"
                                  :error="errors.stake"
                                  add-on="units"
                                  label="Stake"
                                  type="text"
                                  id="stake"
                                  class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm"
                                />
                              </div>

                              <div class="col-span-4 sm:col-span-2">
                                <text-input
                                  v-model="betData.odds"
                                  :error="errors.odds"
                                  label="Odds"
                                  type="text"
                                  id="odds"
                                  class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm"
                                />
                              </div>
                              <div class="col-span-4 sm:col-span-2">
                                <autocomplete-input
                                  :options="
                                    $page.props.auth.settings.bookmakers.value
                                  "
                                  v-model="betData.bookie"
                                  :error="errors.bookie"
                                  label="Bookie"
                                  type="text"
                                  id="bookie"
                                  class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm"
                                />
                              </div>
                              <div class="col-span-4 sm:col-span-2">
                                <dropdown
                                  v-model="betData.type"
                                  :error="errors.type"
                                  label="Type"
                                  :options="{
                                    prematch: 'Prematch',
                                    inplay: 'Inplay',
                                  }"
                                />
                              </div>
                              <div class="col-span-4 sm:col-span-2">
                                <text-input
                                  v-model="betData.tipster"
                                  :error="errors.tipster"
                                  label="Tipster"
                                  type="text"
                                  id="tipster"
                                  class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-gray-900 sm:text-sm"
                                />
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </section>
                  </div>
                </div>
                <div class="flex flex-shrink-0 justify-end px-4 py-4">
                  <div class="mr-5 flex items-center" v-if="!bet">
                    <div class="text-sm">
                      <label
                        for="comments"
                        class="mr-2 font-medium text-gray-700"
                        >Clear inputs</label
                      >
                    </div>
                    <div class="flex h-5 items-center">
                      <input
                        v-model="betData.clearInputs"
                        id="comments"
                        aria-describedby="comments-description"
                        name="comments"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      />
                    </div>
                  </div>
                  <button
                    type="button"
                    class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    @click="open = false"
                  >
                    Cancel
                  </button>

                  <loading-button
                    class="ml-5"
                    @click.prevent="save"
                    :loading="betData.processing"
                  >
                    Save
                  </loading-button>
                </div>
              </form>
            </div>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import Button from "@/Components/Button.vue";
import Events from "@/PageComponents/Events.vue";
import Event from "@/PageComponents/Event.vue";
import TextInput from "@/Components/TextInput.vue";
import AutocompleteInput from "@/Components/AutocompleteInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import moment from "moment";
import Multiselect from "@vueform/multiselect";
import {
  Dialog,
  DialogOverlay,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { XIcon, PlusIcon } from "@heroicons/vue/outline";
import { ref } from "vue";
import SportIcon from "@/Components/SportIcon.vue";

export default {
  components: {
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    XIcon,
    PlusIcon,
    Button,
    Events,
    Event,
    TextInput,
    TextInputWithAddOn,
    Dropdown,
    LoadingButton,
    AutocompleteInput,
    Multiselect,
    SportIcon,
  },
  setup() {
    const open = ref(false);

    return {
      open,
    };
  },

  props: {
    errors: Object,
  },
  data() {
    return {
      bet: null,
      betData: {},
      betTypes: this.$page.props.betTypes,
      title: null,
      addedGames: { 0: 0 },
      currentGameId: 0,
      games: {},
      renderComponent: true,
      isEdit: false,
      activeSport: "football",
    };
  },

  created() {
    this.emitter.on("betForm:show", () => {
      this.open = true;
      this.isEdit = this.bet && Object.keys(this.bet).length > 0 && this.bet.id;
      if (!this.bet) {
        // Reset games if slide over is opened
        this.games = {};
        this.addedGames = { 0: 0 };
      }
      this.setBetData();
    });

    this.emitter.on("game:delete", (index) => {
      delete this.addedGames[index];
      delete this.games[index];
    });

    this.moment = moment;
    this.emitter.on("event:search", (event) => {
      this.betData.event = event.event;
    });

    this.emitter.on("event:edit", (event) => {
      this.bet = event;
      this.activeSport = this.bet.sport;
      this.currentGameId = this.bet.bet_fixture.length - 1;
      this.addedGames = Object.assign({}, this.bet.bet_fixture);
      this.emitter.emit("betForm:show");
    });

    this.emitter.on("event:import", (event) => {
      this.bet = event;
      this.currentGameId = this.bet.games.length - 1;
      this.addedGames = Object.assign({}, this.bet.games);
      this.emitter.emit("betForm:show");
    });

    this.emitter.on("event:clear", (event) => {
      this.betData = this.$inertia.form({
        bookie: null,
        tipster: null,
        odds: null,
        stake: null,
        sport: null,
        type: null,
        clearInputs: true,
      });
      this.setUserSettings();
    });
  },

  methods: {
    setUserSettings() {
      var userSettings = this.$page.props.auth.settings;
      for (const key in userSettings) {
        var setting = userSettings[key];
        this.betData[key] = setting.value;
      }
    },
    setSportType(type) {
      this.activeSport = type;
      this.betData.sport = type;
    },

    addGame() {
      this.currentGameId++;
      this.addedGames[this.currentGameId] = 0;
    },
    save() {
      var route = "";
      if (this.bet && this.bet.id) {
        route = this.route("bet.update");
        this.betData.games = this.games;

        this.betData.put(route, {
          preserveScroll: true,
          onSuccess: () => {
            this.setBetData();
            this.open = false;
            this.emitter.emit("event:clear");
          },
        });
      } else {
        var importId = this.betData.importId;
        route = this.route("bet.store");
        this.betData.games = this.games;
        this.betData.post(route, {
          preserveScroll: true,
          onSuccess: () => {
            if (this.betData.clearInputs) {
              this.setBetData();
              this.open = false;
              this.emitter.emit("event:clear");
            }

            if (importId > 0) {
              this.$http
                .put(
                  this.route(
                    "import.update"
                  ),
                  {
                    id: importId
                  }
                )
                .then((response) => {
                   this.$inertia.visit(this.route('import.index'));
                });
            }
          },
        });
      }
    },

    setBetData() {
      if (!this.bet) {
        this.title = "New bet";
        this.betData = this.$inertia.form({
          bookie: null,
          tipster: null,
          sport: this.activeSport,
          odds: null,
          stake: null,
          type: null,
          games: null,
          clearInputs: true,
        });
        this.setUserSettings();
      } else if (this.bet.id) {
        this.bet.games = {};
        this.betData = this.$inertia.form(this.bet);
        this.title = "Edit " + this.bet.event;
      } else {
        this.bet.games = {};
        this.betData = this.$inertia.form(
          Object.assign(
            {
              bookie: null,
              tipster: null,
              sport: this.activeSport,
              odds: null,
              stake: null,
              type: null,
              games: null,
              clearInputs: true,
            },
            this.bet
          )
        );
        this.title = "New bet";
      }
    },
  },

  computed: {
    sports() {
      return this.$page.props.sports;
    },
  },

  watch: {
    open: function (newOpen, oldOpen) {
      if (newOpen == false) {
        this.bet = null;
      }
    },
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<template>
  <TransitionRoot as="template" :show="open">
    <Dialog
      as="div"
      class="fixed inset-0 overflow-hidden z-20"
      @close="open = false"
    >
      <div class="absolute inset-0 overflow-hidden">
        <DialogOverlay class="absolute inset-0" />

        <div class="fixed inset-y-0 pl-16 max-w-full right-0 flex">
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
                class="
                  h-full
                  divide-y divide-gray-200
                  flex flex-col
                  bg-white
                  shadow-xl
                "
              >
                <div class="flex-1 h-0 overflow-y-auto">
                  <div class="py-6 px-4 bg-indigo-700 sm:px-6">
                    <div class="flex items-center justify-between">
                      <DialogTitle class="text-lg font-medium text-white">
                        {{ title }}
                      </DialogTitle>
                      <div class="ml-3 h-7 flex items-center">
                        <button
                          type="button"
                          class="
                            bg-indigo-700
                            rounded-md
                            text-indigo-200
                            hover:text-white
                            focus:outline-none focus:ring-2 focus:ring-white
                          "
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
                                >
                                </Event>
                                <div class="flex justify-end">
                                  <button
                                    class="
                                      inline-flex
                                      items-center
                                      px-1.5
                                      py-1.5
                                      border border-transparent
                                      text-xs
                                      font-medium
                                      rounded
                                      shadow-sm
                                      text-white
                                      bg-indigo-600
                                      hover:bg-indigo-700
                                      focus:outline-none
                                      focus:ring-2
                                      focus:ring-offset-2
                                      focus:ring-indigo-500
                                    "
                                    @click.prevent="addGame"
                                  >
                                   <PlusIcon class="h-4 w-4" aria-hidden="true" />
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
                                />
                              </div>

                              <div class="col-span-4 sm:col-span-2">
                                <text-input
                                  v-model="betData.odds"
                                  :error="errors.odds"
                                  label="Odds"
                                  type="text"
                                  id="odds"
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
                                  v-model="betData.sport"
                                  :error="errors.sport"
                                  label="Sport"
                                  type="text"
                                  id="time"
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
                                />
                              </div>

                              <div class="col-span-4 sm:col-span-2">
                                <text-input
                                  v-model="betData.tipster"
                                  :error="errors.tipster"
                                  label="Tipster"
                                  type="text"
                                  id="tipster"
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
                                />
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </section>
                  </div>
                </div>
                <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                  <div class="flex items-center mr-5" v-if="!bet">
                    <div class="text-sm">
                      <label
                        for="comments"
                        class="font-medium text-gray-700 mr-2"
                        >Clear inputs</label
                      >
                    </div>
                    <div class="flex items-center h-5">
                      <input
                        v-model="betData.clearInputs"
                        id="comments"
                        aria-describedby="comments-description"
                        name="comments"
                        type="checkbox"
                        class="
                          focus:ring-indigo-500
                          h-4
                          w-4
                          text-indigo-600
                          border-gray-300
                          rounded
                        "
                      />
                    </div>
                  </div>
                  <button
                    type="button"
                    class="
                      bg-white
                      py-2
                      px-4
                      border border-gray-300
                      rounded-md
                      shadow-sm
                      text-sm
                      font-medium
                      text-gray-700
                      hover:bg-gray-50
                      focus:outline-none
                      focus:ring-2
                      focus:ring-offset-2
                      focus:ring-indigo-500
                    "
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
import Dropdown from "@/Components/Dropdown";
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
    };
  },

  created() {
    this.emitter.on("betForm:show", () => {
      this.open = true;
      this.isEdit = this.bet && Object.keys(this.bet).length > 0;
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

      // this.betData.games = this.games;

      if (this.bet) {
        // Looks not needed
        // this.renderComponent = false;
        // this.$nextTick(() => {
        //   // Add the component back in
        //   this.renderComponent = true;
        // });
      }
    });

    this.moment = moment;
    this.emitter.on("event:search", (event) => {
      this.betData.event = event.event;
    });

    this.emitter.on("event:edit", (event) => {
      this.bet = event;
      this.currentGameId = this.bet.bet_fixture.length - 1;
      this.addedGames = Object.assign({}, this.bet.bet_fixture);
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

    addGame() {
      this.currentGameId++;
      this.addedGames[this.currentGameId] = 0;
    },
    save() {
      var route = "";
      if (this.bet) {
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
          sport: null,
          odds: null,
          stake: null,
          type: null,
          games: null,
          clearInputs: true,
        });
        this.setUserSettings();
      } else {
        this.bet.games = {};
        this.betData = this.$inertia.form(this.bet);
        this.title = "Edit " + this.bet.event;
      }
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
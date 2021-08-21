<template>
  <TransitionRoot as="template" :show="open">
    <Dialog
      as="div"
      class="fixed inset-0 overflow-hidden"
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
                            focus:outline-none
                            focus:ring-2 focus:ring-white
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
                            <div class="grid grid-cols-4 gap-6">
                              <div class="col-span-4 sm:col-span-4">
                                <Events :bet="betData" :errors="errors" />
                              </div>

                              <div class="col-span-4 sm:col-span-4">
                                <text-input
                                  v-model="betData.selection"
                                  :error="errors.selection"
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
                                  label="Selection"
                                />
                              </div>

                              <div class="col-span-4 sm:col-span-4">
                                <label
                                  class="
                                    block
                                    text-sm
                                    font-medium
                                    text-gray-700
                                    dark:text-gray-400
                                    capitalize
                                  "
                                  >Category:</label
                                >
                                <Multiselect
                                  v-model="betData.category"
                                  :searchable="true"
                                  :options="formattedBetTypes"
                                  mode="tags"
                                />
                              </div>

                              <div class="col-span-4 sm:col-span-2">
                                <autocomplete-input
                                  :options="
                                    JSON.parse(
                                      $page.props.auth.settings.bookmakers.value
                                    )
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
                              <div class="col-span-4 sm:col-span-4">
                                <text-input
                                  v-model="betData.date"
                                  :error="errors.date"
                                  label="Date"
                                  type="datetime-local"
                                  id="date"
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
                      focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                    "
                    @click="open = false"
                  >
                    Cancel
                  </button>
                  <loading-button
                    class="ml-5"
                    @click.prevent="save"
                    :loading="processing"
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
// import Layout from "@/Layouts/Authenticated";
import Button from "@/Components/Button.vue";
import Events from "@/Components/Events.vue";
import TextInput from "@/Components/TextInput.vue";
import AutocompleteInput from "@/Components/AutocompleteInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import Dropdown from "@/Components/Dropdown";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
// import Stats from "@/Components/Stats.vue";
import moment from "moment";
import Multiselect from "@vueform/multiselect";
import {
  Dialog,
  DialogOverlay,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { XIcon } from "@heroicons/vue/outline";
import {
  LinkIcon,
  PlusIcon,
  QuestionMarkCircleIcon,
} from "@heroicons/vue/solid";
import { ref, watch, getCurrentInstance } from "vue";

export default {
  components: {
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    LinkIcon,
    PlusIcon,
    QuestionMarkCircleIcon,
    XIcon,
    Button,
    Events,
    TextInput,
    // Stats,
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
    processing: Boolean,
  },
  data() {
    return {
      bet: null,
      betData: {},
      betTypes: this.$page.props.betTypes,
      title: null,
    };
  },

  created() {
    this.emitter.on("betForm:show", () => {
      this.open = true;
      this.setBetData();
    });

    this.moment = moment;
    this.emitter.on("event:search", (event) => {
      this.betData.event = event.event;
      if (event.match) {
        this.betData.date = moment(event.match.date).format("YYYY-MM-DDTHH:mm");
        this.betData.match_id = event.match.id;
      } else {
        this.betData.date = null;
        this.betData.match_id = null;
      }
    });

    this.emitter.on("event:edit", (event) => {
      this.bet = event;
      this.emitter.emit("betForm:show");
    });

    this.emitter.on("event:clear", (event) => {
      this.betData = this.$inertia.form({
        event: null,
        selection: null,
        bookie: null,
        stake: null,
        odds: null,
        tipster: null,
        sport: null,
        type: null,
        date: moment().format("YYYY-MM-DDTHH:mm"),
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
    save() {
      var route = "";
      this.betForm = this.$inertia.form(this.betData);
      if (this.bet) {
        route = this.route("bet.update");
        this.betForm.put(route, {
          preserveScroll: true,
          onSuccess: () => {
            this.open = false;
            this.emitter.emit("event:clear");
            this.setBetData();
          },
        });
      } else {
        route = this.route("bet.store");
        this.betForm.post(route, {
          preserveScroll: true,
          onSuccess: () => {
            if (this.betForm.clearInputs) {
              this.emitter.emit("event:clear");
              this.open = false;
              this.setBetData();
            }
          },
        });
      }
    },

    setBetData() {
      if (!this.bet) {
        this.title = "New bet";
        this.betData = this.$inertia.form({
          event: null,
          selection: null,
          bookie: null,
          stake: null,
          odds: null,
          tipster: null,
          sport: null,
          type: null,
          date: moment().format("YYYY-MM-DDTHH:mm"),
          clearInputs: true,
        });
        this.setUserSettings();
      } else {
        this.betData = this.bet;
        this.title = "Edit " + this.bet.event;
        this.betData.date = moment(this.betData.date).format(
          "YYYY-MM-DDTHH:mm"
        );

        if (!this.betData.category) {
          this.betData.category = [];
        } else {
          this.betData.category = JSON.parse(this.betData.category);
        }
      }
    },
  },

  computed: {
    formattedBetTypes() {
      return Object.keys(this.betTypes).map((key) => {
        return { value: key, label: this.betTypes[key].name };
      });
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
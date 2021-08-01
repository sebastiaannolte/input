<template>
  {{ betData.match_id }}
  <div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9 mb-5">
    <section aria-labelledby="payment-details-heading">
      <form action="#" method="POST">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="bg-white py-6 px-4 sm:p-6">
            <div>
              <h2
                id="payment-details-heading"
                class="text-xl leading-6 font-medium text-gray-900 font-bold"
              >
                Add new bet
              </h2>
              <p class="mt-1 text-sm text-gray-500">
                Update your billing information. Please note that updating your
                location could affect your tax rates.
              </p>
            </div>
            <div class="mt-6 grid grid-cols-4 gap-6">
              <div class="col-span-4 sm:col-span-2">
                <Events :bet="betData" :errors="errors" />
              </div>

              <div class="col-span-4 sm:col-span-2">
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

              <div class="col-span-4 sm:col-span-1">
                <autocomplete-input
                :options='["Unibet", "Bwin", "Betfair", "William hill"]'
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
              <div class="col-span-4 sm:col-span-1">
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

              <div class="col-span-4 sm:col-span-1">
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

              <div class="col-span-4 sm:col-span-1">
                <dropdown
                  v-model="betData.type"
                  :error="errors.type"
                  label="Type"
                  :options="{ prematch: 'Prematch', inplay: 'Inplay' }"
                />
              </div>
              <div class="col-span-4 sm:col-span-1" v-show="more">
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
              <div class="col-span-4 sm:col-span-1" v-show="more">
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

              <div class="col-span-4 sm:col-span-1" v-show="more">
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
            <div
              class="
                flex
                justify-end
                cursor-pointer
                text-indigo-500
                no-underline
                box-border
                hover:text-indigo-600
                focus:text-indigo-600
              "
              @click="more = !more"
            >
              <template v-if="!more">+ more</template>
              <template v-else>- less</template>
            </div>
          </div>

          <div
            class="
              flex
              justify-end
              items-center
              px-4
              py-3
              bg-gray-50
              text-right
              sm:px-6
            "
          >
            <div class="flex mr-5" v-if="hideClearInputs">
              <div class="text-sm">
                <label for="comments" class="font-medium text-gray-700 mr-2"
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
            <loading-button @click.prevent="save" :loading="processing">
              Save
            </loading-button>
          </div>
        </div>
      </form>
    </section>
  </div>
</template>



<script>
import Layout from "@/Layouts/Authenticated";
import Button from "@/Components/Button.vue";
import Events from "@/Components/Events.vue";
import TextInput from "@/Components/TextInput.vue";
import AutocompleteInput from "@/Components/AutocompleteInput";
import LoadingButton from "@/Components/LoadingButton";
import Dropdown from "@/Components/Dropdown";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import Bets from "@/Components/Bets.vue";
import Stats from "@/Components/Stats.vue";
import moment from "moment";

export default {
  components: {
    Button,
    Events,
    TextInput,
    Bets,
    Stats,
    TextInputWithAddOn,
    Dropdown,
    LoadingButton,
    AutocompleteInput
  },
  layout: Layout,

  props: {
    errors: Object,
    bet: Object,
    processing: Boolean,
  },
  data() {
    return {
      betData: {},
      more: false,
      hideClearInputs: false,
    };
  },

  created() {
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
        clearInputs: true,
      });
      this.setUserSettings();
    });

    if (!this.bet) {
      this.hideClearInputs = true;
      this.betData = this.$inertia.form({
        event: null,
        selection: null,
        bookie: null,
        stake: null,
        odds: null,
        tipster: null,
        sport: null,
        type: null,
        clearInputs: true,
      });
      this.setUserSettings();
    } else {
      this.betData = this.bet;
      this.betData.date = moment(this.betData.date).format("YYYY-MM-DDTHH:mm");
    }
  },

  methods: {
    setUserSettings() {
      var userSettings = this.$page.props.auth.settings;
      for (const key in userSettings) {
        var setting = userSettings[key];
        this.betData[key] = setting;
      }
    },
    save() {
      this.$emit("betFormSubmit", this.betData);
    },
  },

  computed: {},
};
</script>

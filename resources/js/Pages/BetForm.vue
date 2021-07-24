<template>
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
                <text-input
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
                  v-model="betData.tipster"
                  :error="errors.tipster"
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

              <div
                class="text-right cursor-pointer"
                :class="{ 'col-span-4': !more, 'col-span-2': more }"
                @click="more = !more"
              >
                + more
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <button
              @click.prevent="save"
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
      </form>
    </section>
  </div>
</template>



<script>
import Layout from "@/Layouts/Layout";
import Button from "@/Components/Button.vue";
import Events from "@/Components/Events.vue";
import TextInput from "@/Components/TextInput.vue";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import Bets from "@/Components/Bets.vue";
import Stats from "@/Components/Stats.vue";

export default {
  components: { Button, Events, TextInput, Bets, Stats, TextInputWithAddOn },
  layout: Layout,

  props: {
    errors: Object,
    bet: Object,
  },
  data() {
    return {
      betData: {},
      more: false,
    };
  },

  created() {
    this.emitter.on("event:search", (event) => {
      this.betData.event = event.event;
      if (event.match) {
        this.betData.date = event.match.date + " " + event.match.time;
        this.betData.match_id = event.match.id;
      } else {
        this.betData.date = null;
        this.betData.match_id = null;
      }
    });

    if (!this.bet) {
      this.betData = this.$inertia.form({
        event: null,
        selection: null,
        bookie: null,
        stake: null,
        odds: null,
        tipster: null,
      });
    } else {
      this.betData = this.bet;
    }
  },

  methods: {
    save() {
      this.$emit("betFormSubmit", this.betData);
    },
  },

  computed: {},
};
</script>

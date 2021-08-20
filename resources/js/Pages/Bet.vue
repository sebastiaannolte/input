<template>
  <Head :title="bet.event" />
  <div
    class="
      px-4
      mb-5
      w-full
      leading-4
      text-right
      sm:text-center
      bg-white
      p-4
      shadow
      sm:rounded-md
      sm:overflow-hidden
    "
  >
    <div class="sm:flex pb-5">
      <div class="hidden sm:block">
        <inertia-link
          :href="backUrl"
          class="
            bg-white
            border border-gray-300
            rounded-md
            shadow-sm
            py-2
            px-4
            inline-flex
            justify-center
            text-sm
            font-medium
            text-black
            hover:bg-gray-100
            focus:outline-none
            focus:ring-2 focus:ring-offset-2 focus:ring-gray-300
          "
        >
          🠔 All bets
        </inertia-link>
      </div>
      <div class="flex sm:flex-row flex-grow flex-col-reverse">
        <h2
          id="payment-details-heading"
          class="
            text-2xl
            leading-6
            font-medium
            text-gray-900
            font-bold
            text-left
            p-2
            flex-1
          "
        >
          {{ bet.event }}
        </h2>
        <div class="flex justify-between">
          <div>
            <inertia-link
              :href="backUrl"
              class="
                block
                sm:hidden
                bg-white
                border border-gray-300
                rounded-md
                shadow-sm
                py-2
                px-4
                inline-flex
                justify-center
                text-sm
                font-medium
                text-black
                hover:bg-gray-100
                focus:outline-none
                focus:ring-2 focus:ring-offset-2 focus:ring-gray-300
              "
            >
              🠔 All bets
            </inertia-link>
          </div>
          <div>
            <button
              @click="destroy"
              class="
                mr-2
                bg-white
                border border-gray-300
                rounded-md
                shadow-sm
                py-2
                px-4
                inline-flex
                justify-center
                text-sm
                font-medium
                text-black
                hover:bg-gray-100
                focus:outline-none
                focus:ring-2 focus:ring-offset-2 focus:ring-gray-300
              "
            >
              Delete
            </button>
            <inertia-link
              :href="route('bet.edit', bet.id)"
              class="
                bg-red-500
                border border-transparent
                rounded-md
                shadow-sm
                py-2
                px-4
                mr-2
                inline-flex
                justify-center
                text-sm
                font-medium
                text-white
                hover:bg-red-600
                focus:outline-none
                focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
              "
            >
              Edit
            </inertia-link>
          </div>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-8 gap-4 text-left">
      <div class="col-span-8 sm:col-span-4">
        <div class="p-2">Event</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.event }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Selection</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.selection }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Category</div>
        <div class="p-2 mb-2 font-bold">
          {{ categoriesAsString() }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Bookie</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.bookie }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Stake</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.stake }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Odds</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.odds }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Result</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.result }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Date</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.date.split(" ")[0] }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Time</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.date.split(" ")[1] }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Tipster</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.tipster }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Status</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.status }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Type</div>
        <div class="p-2 mb-2 font-bold capitalize">
          {{ bet.type }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import { forIn } from "lodash";

export default {
  layout: Layout,

  props: {
    bet: Object,
    betTypes: Object,
  },
  data() {
    return {
      backUrl: this.route("userhome", this.$page.props.auth.user.username),
    };
  },

  created() {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("backUrl")) {
      this.backUrl = urlParams.get("backUrl");
    }
  },

  methods: {
    destroy() {
      if (confirm("Are you sure you want to delete this bet?")) {
        this.$inertia.delete(this.route("bet.delete", this.bet.id));
      }
    },

    categoriesAsString() {
      if(!this.bet.category){
        return;
      }
      var categories = JSON.parse(this.bet.category);
      console.log(categories);
      var categoryNames = [];
      for (var key in categories) {
        var value = categories[key];
        categoryNames.push(this.betTypes[value]);
      }
      return categoryNames.join(", ");
    },
  },
};
</script>

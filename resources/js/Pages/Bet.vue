<template>
  <Head :title="bet.event" />
  <div
    class="mb-4 w-full rounded-md bg-white p-4 px-4 text-right leading-4 shadow sm:overflow-hidden sm:text-center"
  >
    <div class="pb-5 sm:flex">
      <div class="hidden sm:block">
        <inertia-link
          :href="backUrl"
          class="flex inline-flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-black shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2"
        >
          <ArrowNarrowLeftIcon class="mr-1.5 h-4 w-4" aria-hidden="true" /> All
          bets
        </inertia-link>
      </div>
      <div class="flex flex-grow flex-col-reverse sm:flex-row">
        <h2
          id="payment-details-heading"
          class="flex-1 p-2 text-left text-2xl font-medium font-bold leading-6 text-gray-900"
        >
          {{bet.event}}
        </h2>
        <div class="flex justify-between">
          <div>
            <inertia-link
              :href="backUrl"
              class="block flex inline-flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-black shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 sm:hidden"
            >
              <ArrowNarrowLeftIcon class="mr-1.5 h-4 w-4" aria-hidden="true" />
              All bets
            </inertia-link>
          </div>
          <div>
            <button
              @click="destroy"
              class="mr-2 inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-black shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2"
            >
              Delete
            </button>
            <button
              @click="editBet"
              class="inline-flex justify-center rounded-md border border-transparent bg-red-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
            >
              Edit
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-8 gap-4 text-left">
      <div class="col-span-8 sm:col-span-4">
        <div class="p-2">Event</div>
        <div class="mb-2 p-2 font-bold">
          {{bet.event}}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Selection</div>
        <div class="mb-2 p-2 font-bold">
          {{ bet.selection }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Category</div>
        <div class="mb-2 p-2 font-bold">
          {{ categoryName }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Bookie</div>
        <div class="mb-2 p-2 font-bold">
          {{ bet.bookie }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Stake</div>
        <div class="mb-2 p-2 font-bold">
          {{ bet.stake }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Odds</div>
        <div class="mb-2 p-2 font-bold">
          {{ bet.odds }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Result</div>
        <div class="mb-2 p-2 font-bold">
          {{ betResult }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Date</div>
        <div class="mb-2 p-2 font-bold">
          {{ moment(bet.date).format("YYYY-MM-DD HH:mm") }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Tipster</div>
        <div class="mb-2 p-2 font-bold">
          {{ bet.tipster }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Status</div>
        <div class="mb-2 p-2 font-bold">
          {{ bet.status }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Type</div>
        <div class="mb-2 p-2 font-bold capitalize">
          {{ bet.type }}
        </div>
      </div>
      <div class="col-span-4 sm:col-span-2">
        <div class="p-2">Placed on</div>
        <div class="mb-2 p-2 font-bold capitalize">
          {{ moment(bet.created_at).format("YYYY-MM-DD HH:mm") }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated.vue";
import { ArrowNarrowLeftIcon } from "@heroicons/vue/outline";
import moment from "moment";

export default {
  layout: Layout,

  components: {
    ArrowNarrowLeftIcon,
  },

  props: {
    bet: Object,
  },
  data() {
    return {
      backUrl: this.route("userhome", this.$page.props.userInfo.user.username),
    };
  },

  created() {
    this.moment = moment;
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("backUrl")) {
      this.backUrl = urlParams.get("backUrl");
    }
  },

  methods: {
    editBet() {
      this.emitter.emit("event:edit", Object.assign({}, this.bet));
    },
    destroy() {
      if (confirm("Are you sure you want to delete this bet?")) {
        this.$inertia.delete(this.route("bet.delete", this.bet.id));
      }
    },
  },

  computed: {
    betResult() {
      return this.bet.result ? this.bet.result : 'No result';
    },
  
    categoryName() {
      if (!this.bet.category) {
        return;
      }
      var categories = this.bet.category.split(', ');
      var categoryNames = [];
      for (var key in categories) {
        var value = categories[key];
        categoryNames.push(
          this.$page.props.betTypes[this.bet.sport].find((betType) => betType.id == value).name
        );
      }
      return categoryNames.join(", ");
    },
  }
};
</script>

<template>
  <div class="grid grid-cols-2 gap-4">
    <div
      class="
        px-4
        mb-5
        w-full
        leading-4
        text-center
        bg-white
        p-4
        shadow
        sm:rounded-md
        sm:overflow-hidden
      "
    >
      <span class="text-indigo-500 font-bold">Next bets:</span>
      <div>
        <div
          v-for="(bet, key) in firstBets"
          :key="key"
          class="first:text-lg first:font-bold"
        >
          {{ moment(bet.date).format("MMM DD HH:mm") }} - {{ bet.event }}
        </div>
      </div>
    </div>
    <div
      class="
        px-4
        mb-5
        w-full
        leading-4
        text-center
        bg-white
        p-4
        shadow
        sm:rounded-md
        sm:overflow-hidden
      "
    >
      <div class="flex justify-center items-center flex-row h-full">
        <div class="flex flex-col flex-1">
          <span>Bets</span>
          <span class="pt-3 font-bold">{{ stats.totalBets }}</span>
        </div>
        <div class="flex flex-col flex-1">
          <span>ROI</span>
          <span class="pt-3 font-bold">{{ stats.roi }}%</span>
        </div>
        <div class="flex flex-col flex-1">
          <span>Profit</span>
          <span class="pt-3 font-bold">{{ stats.units }} units</span>
        </div>
        <div class="flex flex-col flex-1">
          <span>Won bets</span>
          <span class="pt-3 font-bold"
            >{{ stats.wonbets }} ({{ stats.winprecentage }}%)</span
          >
        </div>
      </div>
    </div>
  </div>
</template>



<script>
import Layout from "@/Layouts/Authenticated";
import moment from "moment";

export default {
  layout: Layout,

  props: {
    stats: Object,
    upcommingBets: Object,
  },
  data() {
    return {};
  },

  created() {
    this.moment = moment;
  },

  methods: {},

  computed: {
    firstBets() {
      return Object.fromEntries(
        Object.entries(this.upcommingBets).filter(([key, value]) => key < 3)
      );
    },
  },
};
</script>

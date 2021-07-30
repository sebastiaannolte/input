<template>
  <Head :title="bet.event" />
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
    <div class="flex pb-5">
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
      <inertia-link
        :href="route('bet.delete', bet.id)"
        method="delete"
        class="
          bg-gray-800
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
          hover:bg-gray-900
          focus:outline-none
          focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
        "
      >
        Delete
      </inertia-link>
      <inertia-link
        :href="route('bet.edit', bet.id)"
        class="
          bg-gray-800
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
          hover:bg-gray-900
          focus:outline-none
          focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
        "
      >
        Edit
      </inertia-link>
    </div>
    <div class="grid grid-cols-8 gap-4 text-left">
      <div class="col-span-4">
        <div class="p-2">Event</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.event }}
        </div>
      </div>
      <div class="col-span-4">
        <div class="p-2">Selection</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.selection }}
        </div>
      </div>
      <div class="col-span-2">
        <div class="p-2">Bookie</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.bookie }}
        </div>
      </div>
      <div class="col-span-2">
        <div class="p-2">Stake</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.stake }}
        </div>
      </div>
      <div class="col-span-2">
        <div class="p-2">Odds</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.odds }}
        </div>
      </div>
      <div class="col-span-2">
        <div class="p-2">Result</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.result }}
        </div>
      </div>
      <div class="col-span-2">
        <div class="p-2">Date</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.date.split(" ")[0] }}
        </div>
      </div>
      <div class="col-span-2">
        <div class="p-2">Time</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.date.split(" ")[1] }}
        </div>
      </div>
      <div class="col-span-2">
        <div class="p-2">Tipster</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.tipster }}
        </div>
      </div>
      <div class="col-span-2">
        <div class="p-2">Status</div>
        <div class="p-2 mb-2 font-bold">
          {{ bet.status }}
        </div>
      </div>
      <div class="col-span-2">
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

export default {
  layout: Layout,

  props: {
    bet: Object,
  },
  data() {
    return {};
  },

  created() {},

  methods: {},
};
</script>

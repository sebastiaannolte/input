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
      <div
        class="flex justify-center items-center p-0 m-auto text-center box-border"
      >
        <!-- profile image -->
        <img
          class="
            inline-block
            max-w-full
            h-auto
            text-gray-800
            align-middle
            box-border
          "
          src="https://s3.eu-central-1.amazonaws.com/cdn.bettin.gs/img/noprofileimage.png"
          style="max-height: 40px; border-radius: 35px"
        />
        <!-- /profile image -->

        <div
          class="
            flex-shrink
            justify-center
            items-center
            text-gray-800
            box-border
          "
        >
          <!-- alias -->
          <h4
            class="
              py-0
              px-2
              my-2
              mx-0
              font-sans
              text-lg
              not-italic
              leading-5
              box-border
              truncate
              md:text-2xl
            "
          >
            {{stats.username}}
          </h4>
          <!-- /alias -->

          <!-- pro icon -->
          <div class="p-0 m-0 cursor-default box-border"></div>
          <!-- /pro icon -->
        </div>
      </div>

      <!-- followers -->
      <div
        id="followers-count-container"
        class="
          justify-center
          items-center
          p-0
          my-2
          mx-auto
          text-gray-800
          box-border
        "
        style="
          direction: ltr;
          display: -webkit-flex;
          flex-flow: row wrap;
          max-width: 225px;
          font-weight: bold;
        "
      >
        <a
          class="
            m-0
            font-bold
            text-red-500
            no-underline
            box-border
            hover:text-red-500
            focus:text-red-500
          "
          ><span id="followers-count" class="flex-grow m-0 h-full box-border"
            >0</span
          >
          Followers</a
        >
        <span class="flex-grow m-0 h-full font-bold box-border"></span>
        <a
          class="
            m-0
            font-bold
            text-red-500
            no-underline
            box-border
            hover:text-red-500
            focus:text-red-500
          "
          >Following
          <span id="following-count" class="flex-grow m-0 h-full box-border"
            >0</span
          ></a
        >
      </div>
      <!-- /followers -->
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

export default {
  layout: Layout,

  props: {
    stats: Object,
  },
  data() {
    return {};
  },

  created() {},

  methods: {},
};
</script>

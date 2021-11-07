<template>
  <section
    id="bottom-navigation"
    class="md:hidden block fixed inset-x-0 bottom-0 z-10 bg-white shadow"
  >
    <section
      id="bottom-navigation"
      class="block fixed inset-x-0 bottom-0 z-10 bg-white shadow"
    >
      <div id="tabs" class="flex justify-between">
        <span
          @click="clickAction(item)"
          v-for="item in menu"
          :key="item.name"
          class="
            w-full
            focus:text-teal-500
            hover:text-teal-500
            justify-center
            inline-block
            text-center
            pt-2
            pb-1
          "
        >
          <component class="w-6 h-6 inline-block mb-1" :is="item.icon" />
          <span class="tab tab-home block text-xs">{{ item.name }}</span>
        </span>
      </div>
    </section>
  </section>
</template>

<script>
import { computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";

import {
  HomeIcon,
  PlusIcon,
  CogIcon,
  SearchCircleIcon,
  TrendingUpIcon,
} from "@heroicons/vue/outline";

export default {
  props: {
    errors: Object,
    bet: Object,
  },
  components: {
    PlusIcon,
    HomeIcon,
    CogIcon,
    SearchCircleIcon,
    TrendingUpIcon,
  },

  data() {
    return {};
  },

  setup() {
    const user = computed(() => usePage().props.value.auth.user);

    let menu = [
      {
        name: "Your bets",
        url: "/" + user.value.username,
        icon: "HomeIcon",
      },
      {
        name: "Your stats",
        url: "/" + user.value.username + "/stats",
        icon: "TrendingUpIcon",
      },
      {
        name: "Special stats",
        url: "/" + user.value.username + "/special",
        icon: "SearchCircleIcon",
      },
      {
        name: "Settings",
        url: "/" + user.value.username + "/settings",
        icon: "CogIcon",
      },
      {
        name: "New bet",
        url: "",
        click: "openBet",
        icon: "PlusIcon",
      },
    ];

    return {
      user,
      menu,
    };
  },

  methods: {
    openBet() {
      this.emitter.emit("betForm:show");
    },

    clickAction(item) {
      if (item.url) {
        this.$inertia.get(item.url);
        return;
      }
      this[item.click]();
    },
  },
};
</script>
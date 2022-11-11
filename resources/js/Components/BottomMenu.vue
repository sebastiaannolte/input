<template>
  <section
    id="bottom-navigation"
    class="fixed inset-x-0 bottom-0 z-10 block bg-white shadow sm:hidden"
  >
    <section
      id="bottom-navigation"
      class="fixed inset-x-0 bottom-0 z-10 block bg-white shadow"
    >
      <div id="tabs" class="flex justify-between">
        <span
          @click="clickAction(item)"
          v-for="item in menu"
          :key="item.name"
          class="inline-block w-full justify-center pt-2 pb-1 text-center"

        >
          <component class="mb-1 inline-block h-6 w-6" :is=" activeItem == item.name ? item.icon+'Solid' : item.icon" />
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
  PlusCircleIcon,
  CogIcon,
  SearchCircleIcon,
  DocumentReportIcon,
} from "@heroicons/vue/outline";

import {
  HomeIcon as HomeIconSolid,
  PlusCircleIcon as PlusCircleIconSolid,
  CogIcon as CogIconSolid,
  SearchCircleIcon as SearchCircleIconSolid,
  DocumentReportIcon as DocumentReportIconSolid,
} from "@heroicons/vue/solid";

export default {
  props: {
    errors: Object,
    bet: Object,
  },
  components: {
    PlusCircleIcon,
    HomeIcon,
    CogIcon,
    SearchCircleIcon,
    DocumentReportIcon,

    HomeIconSolid,
    PlusCircleIconSolid,
    CogIconSolid,
    SearchCircleIconSolid,
    DocumentReportIconSolid,
  },

  data() {
    return {
      activeItem: null,
    };
  },

  created() {
    this.getActiveItem();
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
        icon: "DocumentReportIcon",
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
        icon: "PlusCircleIcon",
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

    getActiveItem(item) {
      if (item && item.url) {
        this.activeItem = item.name;
        return;
      }
      this.menu.forEach((item) => {
        if (item.url == "/" + window.location.pathname.substr(1)) {
          this.activeItem = item.name;
          return;
        }
      });
    },

    clickAction(item) {
      this.getActiveItem(item);
      if (item.url) {
        this.$inertia.visit(item.url);
        return;
      }
      this[item.click]();
    },
  },
};
</script>
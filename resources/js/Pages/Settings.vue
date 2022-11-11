<template>
  <!-- <Head title="Settings" /> -->
  <div class="sm:hidden mb-2">
    <label for="tabs" class="sr-only">Select a tab</label>
    <select
      @change="onDropdownTabChange"
      id="tabs"
      name="tabs"
      class="
        block
        w-full
        pl-3
        pr-10
        py-2
        text-base
        border-gray-300
        focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
        sm:text-sm
        rounded-md
      "
    >
      <option v-for="tab in tabs" :key="tab.name" :selected="tab.current">
        {{ tab.name }}
      </option>
    </select>
  </div>
  <div class="flex flex-col items-center">
    <div
      class="
        mb-4
        leading-4
        bg-white
        shadow
        rounded-md
        sm:overflow-hidden
        w-full
      "
    >
      <div class="hidden sm:block">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8 justify-center" aria-label="Tabs">
            <span
              v-for="tab in tabs"
              :key="tab.name"
              class="cursor-pointer capitalize"
              :class="[
                tab.current
                  ? 'border-indigo-500 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              ]"
              :aria-current="tab.current ? 'page' : undefined"
              @click="openTab(tab.name, tab)"
            >
              {{ tab.name }}
            </span>
          </nav>
        </div>
      </div>

      <div class=""><slot /></div>
    </div>
  </div>
</template>

<script>
import Layout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import Multiselect from "@vueform/multiselect";
import { Inertia } from "@inertiajs/inertia";
import pickBy from "lodash/pickBy";

export default {
  components: { Button, TextInput, LoadingButton, Multiselect },
  layout: Layout,

  props: {},

  data() {
    return {
      currentTab: null,
      tabs: [
        {
          name: "Settings",
          route: this.route(
            "userSettings.index",
            this.$page.props.auth.user.username
          ),
          current: false,
        },
        {
          name: "Profile",
          route: this.route(
            "profile.index",
            this.$page.props.auth.user.username
          ),
          current: false,
        },
      ],
    };
  },

  created() {
    this.setCurrentTab();
  },

  methods: {
    openTab(value, tab) {
      this.changeTab(value, tab);
    },
    url() {
      return location.pathname.substr(1).split("/").pop();
    },

    setCurrentTab() {
      this.tabs.forEach((tab) => {
        if (tab.name.toLowerCase() == this.url().toLowerCase()) {
          tab.current = true;
        }
      });
    },

    onDropdownTabChange(event) {
      var tab = this.tabs.find((tab) => tab.name == event.target.value);
      this.changeTab(event.target.value, tab);
    },

    changeTab(value, tab) {
      this.currentTab = this.tabs.find((tab) => tab.name == value);
      this.tabs.forEach((tab) => {
        if (tab.name.toLowerCase() == this.url().toLowerCase()) {
          tab.current = false;
        }
      });
      tab.current = true;
      Inertia.get(
        tab.route,
        pickBy({
          type: this.currentTab.option,
          filters: null, // reset filters if tab changes
        }),
        {
          only: ["type", "filters"],
        }
      );
    },
  },

  computed: {},
};
</script>
<style src="@vueform/multiselect/themes/default.css"></style>

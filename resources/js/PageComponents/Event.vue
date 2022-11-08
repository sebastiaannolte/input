<template>
  <div
    class="mb-2 p-2 leading-4 bg-white shadow rounded-md border border-gray-10"
  >
    <div class="flex items-center">
      <EventsV2
        v-model="betData.event"
        :bet="betData"
        :errors="errors"
        :index="index"
        :sport="sport"
        class="w-full mr-2 z-50"
      />

      <ChevronUpIcon
        v-if="panelOpen"
        class="h-4 w-4"
        aria-hidden="true"
        @click="togglePanel"
      />

      <ChevronDownIcon
        v-else
        class="h-4 w-4"
        aria-hidden="true"
        @click="togglePanel"
      />
    </div>
    <div class="ml-2 mt-2" v-show="panelOpen">
      <div class="grid grid-cols-4 gap-4">
        <div class="col-span-4 sm:col-span-4">
          <text-input
            v-model="betData.selection"
            :error="errors['games.' + index + '.selection']"
            class="
              mt-4
              block
              w-full
              border border-gray-300
              rounded-md
              shadow-sm
              py-2
              px-3
              focus:outline-none focus:ring-gray-900 focus:border-gray-900
              sm:text-sm
            "
            label="Selection"
          />
        </div>

        <div class="col-span-4 sm:col-span-4">
          <label
            class="
              block
              text-sm
              font-medium
              text-gray-700
              dark:text-gray-400
              capitalize
            "
            >Category:</label
          >
          <Multiselect
            v-model="betData.category"
            :searchable="true"
            :options="this.betTypes[sport]"
            label="name"
            valueProp="id"
            trackBy="name"
          />
          <div
            v-if="errors['games.' + index + '.category']"
            class="form-error text-gray-400"
          >
            <small>{{ errors["games." + index + ".category"] }}</small>
          </div>
        </div>
        <div class="col-span-4 sm:col-span-4">
          <text-input
            v-model="betData.date"
            :error="errors['games.' + index + '.date']"
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
              focus:outline-none focus:ring-gray-900 focus:border-gray-900
              sm:text-sm
            "
          />
        </div>
      </div>
      <div class="flex justify-end mt-2">
        <button
          class="
            inline-flex
            items-center
            px-2.5
            py-1.5
            border border-transparent
            text-xs
            font-medium
            rounded
            shadow-sm
            text-white
            bg-indigo-600
            hover:bg-indigo-700
            focus:outline-none
            focus:ring-2
            focus:ring-offset-2
            focus:ring-indigo-500
          "
          @click.prevent="deleteGame(index)"
        >
          Remove
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import Button from "@/Components/Button.vue";
import Events from "@/PageComponents/Events.vue";
import EventsV2 from "@/PageComponents/EventsV2.vue";
import TextInput from "@/Components/TextInput.vue";
import AutocompleteInput from "@/Components/AutocompleteInput.vue";
import LoadingButton from "@/Components/LoadingButton.vue";
import Dropdown from "@/Components/Dropdown";
import TextInputWithAddOn from "@/Components/TextInputWithAddOn.vue";
import moment from "moment";
import Multiselect from "@vueform/multiselect";
import {
  Dialog,
  DialogOverlay,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { XIcon } from "@heroicons/vue/outline";
import { ref } from "vue";

import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { ChevronUpIcon, ChevronDownIcon } from "@heroicons/vue/outline";

export default {
  components: {
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    XIcon,
    Button,
    Events,
    EventsV2,
    TextInput,
    TextInputWithAddOn,
    Dropdown,
    LoadingButton,
    AutocompleteInput,
    Multiselect,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    ChevronDownIcon,
    ChevronUpIcon,
  },
  setup() {
    const open = ref(false);

    return {
      open,
    };
  },

  props: {
    errors: Object,
    modelValue: Object,
    game: Object,
    index: Number,
    isEdit: Boolean,
    sport: Object,
  },
  data() {
    return {
      betData: {},
      betTypes: this.$page.props.betTypes,
      title: null,
      panelOpen: false,
    };
  },

  created() {
    this.moment = moment;
    this.setBetData();

    this.save();

    this.emitter.on("event:clear", (event) => {
      this.betData = {
        event: null,
        selection: null,
        date: moment().format("YYYY-MM-DDTHH:mm"),
        clearInputs: true,
      };
      this.setUserSettings();
    });
  },

  methods: {
    setUserSettings() {
      var userSettings = this.$page.props.auth.settings;
      for (const key in userSettings) {
        var setting = userSettings[key];
        this.betData[key] = setting.value;
      }
    },
    togglePanel() {
      this.panelOpen = !this.panelOpen;
    },
    save() {
      this.$emit("update:modelValue", this.betData);
    },

    deleteGame(index) {
      this.emitter.emit("game:delete", index);
    },

    setBetData() {
      if (!this.game) {
        this.title = "New bet";
        this.betData = {
          event: null,
          selection: null,
          date: moment().format("YYYY-MM-DDTHH:mm"),
          clearInputs: true,
        };
        // this.setUserSettings();
      } else {
        this.betData = this.$inertia.form(this.game);
        // this.title = "Edit " + this.bet.event;
        this.betData.date = moment(this.betData.date).format(
          "YYYY-MM-DDTHH:mm"
        );

        if (!this.betData.category) {
          this.betData.category = [];
        } else {
          this.betData.category = JSON.parse(this.betData.category);
        }
      }
    },
  },

  watch: {
    open: function (newOpen, oldOpen) {
      if (newOpen == false) {
        this.bet = null;
      }
    },
    "betData.event": function (newOpen, oldOpen) {
      if (this.betData && newOpen && !this.isEdit) {
        this.panelOpen = true;
      }
      if (
        this.betData &&
        newOpen &&
        newOpen.hasOwnProperty("match") &&
        newOpen.match
      ) {
        this.betData.date = moment(newOpen.match.date).format(
          "YYYY-MM-DDTHH:mm"
        );
      }
    },
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
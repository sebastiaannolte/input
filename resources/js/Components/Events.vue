<template>
  <text-input
    :error="errors.event"
    label="Event"
    id="event"
    type="text"
    autocomplete="off"
    v-model="betData.event"
    @keydown.down.prevent="onArrowDown"
    @keydown.up.prevent="onArrowUp"
    @keydown.enter.prevent="onEnter"
    @focus="onChange(betData.event)"
    ref="input"
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
  <div
    v-show="isOpen"
    ref="scrollContainer"
    class="
      absolute
      z-10
      mt-2
      w-80
      rounded-md
      shadow-lg
      bg-white
      ring-1 ring-black ring-opacity-5
      focus:outline-none
      overflow-auto
    "
    style="max-height: 300px"
  >
    <div
      v-for="(result, i) in results"
      :key="i"
      @click="setResult(result, i)"
      :ref="`card-${i}`"
      class="
        block
        px-4
        py-2
        text-sm
        hover:bg-indigo-500
        cursor-pointer
        hover:text-white
      "
      :class="{
        'bg-indigo-500 text-white': i === arrowCounter,
        'text-gray-700': i != arrowCounter,
      }"
    >
      {{ result.match }}
    </div>
  </div>
</template>

<script>
import TextInput from "@/Components/TextInput.vue";

export default {
  components: { TextInput },
  props: {
    errors: Object,
    bet: Object,
  },
  data() {
    return {
      results: null,
      matchId: null,
      items: null,
      isOpen: false,
      arrowCounter: 0,
      betData: {},
      isFirstLoad: true,
      selected: false,
    };
  },

  created() {
    if (this.bet.event) {
      this.betData = this.bet;
    }

    this.emitter.on("event:clear", () => {
      this.betData.event = null;
      this.betData.match = null;
    });
  },

  methods: {
    focus() {
      this.$refs.input.focus();
    },
    onChange(searchValue) {
      if (!this.isFirstLoad) {
        this.matchId = null;
        this.getResults(searchValue);
      }
      this.isFirstLoad = false;
    },

    getResults(searchValue) {
      if (searchValue && searchValue.length >= 3) {
        this.$http
          .get(this.route("event.search", searchValue))
          .then((response) => {
            if (response.data) {
              this.results = Object.values(response.data);
              if (this.results.length > 0) {
                if (!this.selected) {
                  this.isOpen = true;
                }
                this.isFirstLoad = false;
                this.selected = false;
              }
            }
          });
      }
    },
    setResult(result, i) {
      this.arrowCounter = i;
      this.betData.event = result.match;
      this.matchId = result.id;
      this.isOpen = false;
      this.selected = true;
      this.findGame(this.matchId);
    },
    onArrowDown() {
      if (this.arrowCounter < Object.keys(this.results).length - 1) {
        this.arrowCounter = this.arrowCounter + 1;
        this.fixScrolling();
      }
    },
    onArrowUp() {
      if (this.arrowCounter > 0) {
        this.arrowCounter = this.arrowCounter - 1;
        this.fixScrolling();
      }
    },
    fixScrolling() {
      const liH = this.$refs["card-" + this.arrowCounter].clientHeight;
      this.$refs.scrollContainer.scrollTop = liH * this.arrowCounter;
    },
    onEnter() {
      this.betData.event = this.results[this.arrowCounter].match;
      this.matchId = this.results[this.arrowCounter].id;
      this.isOpen = false;
      this.selected = true;

      this.arrowCounter = -1;
      this.findGame(this.matchId);
    },

    checkWithoutSelect(searchValue) {
      for (var key in this.results) {
        if (searchValue == this.results[key].match) {
          this.matchId = this.results[key].id;
          this.findGame(this.matchId);
        }
      }
    },

    handleClickOutside(evt) {
      if (!this.$el.contains(evt.target)) {
        this.isOpen = false;
        this.arrowCounter = -1;
      }
    },
    findGame(matchId) {
      if (matchId) {
        this.$http.get(this.route("event.match", matchId)).then((response) => {
          if (response.data) {
            this.betData.match = response.data;
            this.setBet();
            this.isOpen = false;
            return;
          }
        });
      }
      this.betData.match = null;
      this.setBet();
    },

    setBet() {
      this.emitter.emit("event:search", this.betData);
    },
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
  },
  destroyed() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  watch: {
    "betData.event": function (newVal, oldVal) {
      if (newVal != oldVal && oldVal) {
        this.betData.match = null;
        this.setBet();
      }
      this.onChange(newVal);
    },
  },
};
</script>


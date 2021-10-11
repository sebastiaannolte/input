<template>
  <div class="flex">
    <Multiselect
      v-model="betData.event"
      mode="single"
      placeholder="Search a match"
      noOptionsText="Start typing to find a match"
      :filterResults="false"
      :minChars="3"
      :resolveOnLoad="false"
      :delay="100"
      :searchable="true"
      @select="onSelect"
      :createTag="true"
      :object="true"
      ref="multiselect"
      add-tag-on="['enter']"
      :clearOnSelect="false"
      :clearOnSearch="false"
      :options="
        async function (query) {
          return await fetchMatches(query, searchType);
        }
      "
      class="rounded-r-0"
    >
      <template v-slot:option="{ option }">
        <img class="h-4 mr-2" :src="option.icon" /> {{ option.label }}
      </template>
    </Multiselect>
    <button
      type="button"
      @click="setSearchType('simple')"
      :class="{ 'bg-gray-200 text-white inner-shadow': searchType == 'simple' }"
      class="
        inline-flex
        items-center
        px-2.5
        py-1.5
        border border-gray-300
        shadow-sm
        text-xs
        font-medium
        text-gray-700
        bg-white
      "
    >
      Simple
    </button>
    <button
      type="button"
      @click="setSearchType('full')"
      :class="{ 'bg-gray-200 text-white inner-shadow': searchType == 'full' }"
      class="
        inline-flex
        items-center
        px-2.5
        py-1.5
        border border-gray-300
        shadow-sm
        text-xs
        font-medium
        rounded-r
        text-gray-700
        bg-white
        hover:bg-gray-50
        focus:outline-none
        focus:bg-gray-300
      "
    >
      Full
    </button>
  </div>
</template>

<script>
import TextInput from "@/Components/TextInput.vue";
import Multiselect from "@vueform/multiselect";

export default {
  components: { TextInput, Multiselect },
  props: {
    errors: Object,
    bet: Object,
  },

  setup() {
    const fetchMatches = async (query, searchType) => {
      const response = await fetch(
        "/api/search/" + query + "/" + searchType,
        {}
      );
      const data = await response.json();
      return Object.values(data).map((item) => {
        return { value: item.id, label: item.match, icon: item.icon };
      });
    };

    return {
      fetchMatches,
    };
  },
  data() {
    return {
      betData: {},
      searchType: "simple",
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

  mounted() {
    if (this.bet.event) {
      this.bet.event = {
        value: this.bet.match_id ? this.bet.match_id : 0,
        label: this.bet.event,
      };
      // this.$refs.multiselects.refreshOptions();
    }
  },

  methods: {
    onSelect(option) {
      this.findGame(option.value);
    },

    findGame(matchId) {
      if (matchId) {
        this.$http.get(this.route("event.match", matchId)).then((response) => {
          if (response.data) {
            this.betData.match = response.data;
            this.setBet();
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

    setSearchType(type) {
      this.searchType = type;
      // this.$refs.multiselect.open();
    },
  },
};
</script>


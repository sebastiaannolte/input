<template>
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
    ref="multiselects"
    add-tag-on="['enter']"
    :options="
      async function (query) {
        return await fetchMatches(query);
      }
    "
  />
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
    const fetchMatches = async (query) => {
      const response = await fetch("/api/search/" + query, {});
      const data = await response.json();
      return Object.values(data).map((item) => {
        return { value: item.id, label: item.match };
      });
    };

    return {
      fetchMatches,
    };
  },
  data() {
    return {
      betData: {},
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
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
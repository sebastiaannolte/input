<template>
  <Head :title="title" />
  <stats :stats="stats" :upcommingBets="upcommingBets" />
  <bets
    :bets="bets"
    :filters="filters"
    :showFilter="showFilter"
    :filterButton="true"
    :filter-route="
      this.route('userhome', this.$page.props.auth.user.username)
    "
  />
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import Bets from "@/Components/Bets.vue";
import Stats from "@/Components/Stats.vue";
import BetForm from "@/Pages/BetForm.vue";

export default {
  components: {
    Bets,
    Stats,
    BetForm,
  },
  layout: Layout,

  props: {
    errors: Object,
    bets: Object,
    stats: Object,
    filters: Array,
    showFilter: Boolean,
    upcommingBets: Object,
    betTypes: Object,
  },
  data() {
    return {
      betForm: this.$inertia.form(this.bet),
      title: null,
    };
  },

  created() {
    this.setPageTitle();
  },

  methods: {
    handleSubmit(bet) {
      this.betForm = this.$inertia.form(bet);
      this.betForm.post(this.route("bet.store"), {
        preserveScroll: true,
        onSuccess: () =>
          this.betForm.clearInputs ? this.emitter.emit("event:clear") : "",
      });
    },

    setPageTitle() {
      this.title = "Your bets";
      if (!this.$page.props.userInfo.myPage) {
        this.title = "Bets by " + this.$page.props.userInfo.user.name;
      }
    },
  },
};
</script>

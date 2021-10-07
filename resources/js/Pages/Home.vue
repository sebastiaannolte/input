<template>
  <Head :title="title" />
  <stats :stats="stats" :upcommingBets="upcommingBets" />
  <bets
    :bets="bets"
    :filters="filters"
    :filterButton="true"
    :filter-route="
      this.route('userhome', this.$page.props.userInfo.user.username)
    "
  />
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import Bets from "@/PageComponents/Bets.vue";
import Stats from "@/PageComponents/Stats.vue";

export default {
  components: {
    Bets,
    Stats,
  },
  layout: Layout,

  props: {
    errors: Object,
    bets: Object,
    stats: Object,
    filters: Array,
    upcommingBets: Object,
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

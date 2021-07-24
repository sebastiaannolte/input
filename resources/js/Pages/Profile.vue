<template>
  <Head :title="title" />
  <stats :bets="bets" />
  <bet-form
    v-if="$page.props.userInfo.myPage"
    :errors="errors"
    @betFormSubmit="handleSubmit"
  ></bet-form>
  <bets :bets="bets"/>
</template>

<script>
import Layout from "@/Layouts/Layout";
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
  },
  data() {
    return {
      bet: this.$inertia.form({
        event: null,
        selection: null,
        bookie: null,
        stake: null,
        odds: null,
        tipster: null,
        match: null,
      }),
      title: null,
    };
  },

  created() {
    this.setPageTitle();
  },

  methods: {
    handleSubmit(bet) {
      this.$inertia.post(this.route("bet.store"), bet, {
        preserveScroll: true, // bets are not added to frontend
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

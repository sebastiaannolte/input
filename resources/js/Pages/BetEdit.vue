<template>
<Head :title="'Edit '+bet.event " />
  <bet-form
    :bet="betForm"
    :errors="errors"
    @betFormSubmit="handleSubmit"
    :processing="betForm.processing"
    :betTypes="this.betTypes"
  ></bet-form>
</template>

<script>
import Layout from "@/Layouts/Authenticated";
import BetForm from "@/Pages/BetForm";



export default {
  layout: Layout,
  components: {
    BetForm,
  },
  props: {
    bet: Object,
    betTypes: Object,
    errors: Object,
  },
  data() {
    return {
      betForm: this.$inertia.form(this.bet),
    };
  },

  created() {},

  methods: {
    handleSubmit(bet) {
      this.betForm.put(this.route("bet.update"), bet, {
        preserveScroll: true, // bets are not added to frontend
      });
    },
  },
};
</script>

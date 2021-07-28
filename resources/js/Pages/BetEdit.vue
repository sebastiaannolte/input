<template>
<Head :title="'Edit '+bet.event " />
  <bet-form
    :bet="bet"
    :errors="errors"
    @betFormSubmit="handleSubmit"
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
    errors: Object,
  },
  data() {
    return {};
  },

  created() {},

  methods: {
    handleSubmit(bet) {
      this.$inertia.put(this.route("bet.update"), bet, {
        preserveScroll: true, // bets are not added to frontend
      });
    },
  },
};
</script>

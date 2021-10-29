<template>
  <Head :title="pageTitle" />
  <table-filter-header :title="type.name" />
  <span>
    <bets
      :bets="bets.bets"
      :filters="filters"
      :filter-route="
        this.route(this.filterRoute, [
          this.$page.props.userInfo.user.username,
          type.id,
        ])
      "
      :is-stats="true"
    />
  </span>
</template>


<script>
import Layout from "@/Layouts/Authenticated";
import Bets from "@/PageComponents/Bets";
import ShowFilterButton from "@/Components/ShowFilterButton";
import TableFilterHeader from "@/PageComponents/TableFilterHeader";

export default {
  layout: Layout,
  components: {
    Bets,
    ShowFilterButton,
    TableFilterHeader,
  },
  props: {
    bets: Object,
    type: Object,
    filters: Array,
    filterRoute: String,
    title: String,
  },

  data() {
    return {
      pageTitle: null,
    };
  },

  created() {
    this.setPageTitle();
  },

  methods: {
    setPageTitle() {
      this.pageTitle = "Your " + this.title + " stats";
      if (!this.$page.props.userInfo.myPage) {
        this.pageTitle =
          this.$page.props.userInfo.user.name + "'s " + this.title + " stats";
      }
    },
  },
  computed: {},
};
</script>

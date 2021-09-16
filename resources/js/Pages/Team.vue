<template>
  <Head :title="title" />
  <table-filter-header :title="teamName" />
  <bets
    :bets="bets.bets"
    :filters="filters"
    :filter-route="
      this.route('team', [$page.props.auth.user.username, team.id, league.id])
    "
  />
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
    team: Object,
    league: Object,
    filters: Array,
  },

  data() {
    return {
      currentTable: null,
      localFilters: {},
    };
  },

  created() {
    this.currentTable = this.bets;
    this.setPageTitle();
  },

  methods: {
    setPageTitle() {
      this.title = "Your team stats";
      if (!this.$page.props.userInfo.myPage) {
        this.title = this.$page.props.userInfo.user.name + "'s team stats";
      }
    },
  },
  computed: {
    teamName() {
      var teamName = this.team.name;
      if (this.league) {
        teamName += " - " + this.league.name;
      }
      return teamName;
    },
  },
};
</script>

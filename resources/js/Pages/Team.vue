<template>
  <Head :title="title" />
  <div class="md:flex md:items-center md:justify-between">
    <div class="flex-1 min-w-0">
      <h2
        class="
          text-2xl
          font-bold
          leading-7
          text-gray-900
          sm:text-3xl
          sm:truncate
        "
      >
        {{ team.name }}
      </h2>
    </div>
    <div class="mt-4 flex md:mt-0 md:ml-4">
      <show-filter-button />
    </div>
  </div>
  <span v-for="(team, key) in teamTable" :key="key">
    <div class="md:flex md:items-center md:justify-between my-3">
      <div class="flex-1 min-w-0">
        <h5
          class="
            text-lg
            font-bold
            leading-7
            text-gray-900
            sm:text-xl
            sm:truncate
          "
        >
          {{ team.competition.name }}
        </h5>
      </div>
    </div>
    <bets
      :bets="team.bets"
      :filters="filters"
      :showFilter="showFilter"
      :filter-route="
        this.route('team', [this.$page.props.auth.user.username, this.team.id])
      "
    />
  </span>
</template>


<script>
import Layout from "@/Layouts/Authenticated";
import Bets from "@/Components/Bets";
import ShowFilterButton from "@/Components/ShowFilterButton";

export default {
  layout: Layout,
  components: {
    Bets,
    ShowFilterButton,
  },
  props: {
    teamTable: Object,
    team: Object,
    filters: Array,
    showFilter: Boolean,
  },

  data() {
    return {
      currentTable: null,
      localFilters: {},
    };
  },

  created() {
    this.currentTable = this.teamTable;
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
  computed: {},
};
</script>

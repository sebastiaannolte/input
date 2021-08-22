<template>
  <Head :title="pageTitle" />
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
        {{ type.name }}
      </h2>
    </div>
    <div class="mt-4 flex md:mt-0 md:ml-4">
      <show-filter-button />
    </div>
  </div>
  <span>
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
        ></h5>
      </div>
    </div>
    <bets
      :bets="bets.bets"
      :filters="filters"
      :filter-route="
        this.route(this.filterRoute, [
          this.$page.props.auth.user.username,
          type.id,
        ])
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

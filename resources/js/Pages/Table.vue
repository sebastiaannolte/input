<template>
  <Head :title="pageTitle" />
  <table-filter-header :title="type.name" />
  <span>
    <bets
      :bets="bets.bets"
      :filters="filters"
      :filter-route="
        route(filterRoute, [
          $page.props.userInfo.user.username,
          type.id,
        ])
      "
      :is-stats="true"
    />
  </span>
</template>


<script>
import Layout from '@/Layouts/Authenticated.vue'
import Bets from '@/PageComponents/Bets.vue'
import ShowFilterButton from '@/Components/ShowFilterButton.vue'
import TableFilterHeader from '@/PageComponents/TableFilterHeader.vue'

export default {
  components: {
    Bets,
    ShowFilterButton,
    TableFilterHeader,
  },
  layout: Layout,
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
    }
  },
  computed: {},

  created() {
    this.setPageTitle()
  },

  methods: {
    setPageTitle() {
      this.pageTitle = 'Your ' + this.title + ' stats'
      if (!this.$page.props.userInfo.myPage) {
        this.pageTitle =
          this.$page.props.userInfo.user.name + '\'s ' + this.title + ' stats'
      }
    },
  },
}
</script>

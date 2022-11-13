<template>
  <Layout :title="title" :errors="errors">
    <Items 
      :table="table"
      :sort="sort"
      :filters="filters"
      :pagination="pagination"
      :title="title"
      :route="route"
    />
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Authenticated.vue'
import Items from '@/Components/Items.vue'

defineProps({
  table: Object,
  sort: Object,
  filters: Array,
  pagination: Object,
  errors: Object,
  title: String,
  route: Object,
})

</script>

<template>
  <div>
    <page-header>CSE GameJam</page-header>
    <div>
      <router-link :to="'/register'">register</router-link>
    </div>
  </div>
</template>

<script>
import PageHeader from './sub-components/PageHeader.vue';
export default {
  name: "Home",
  components: { PageHeader }
};
</script>

<style scoped>

</style>

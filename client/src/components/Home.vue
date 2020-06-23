<template>
  <div>
    <page-header>CSE GameJam</page-header>
    <div><router-link :to="'/register'">register</router-link></div>
    <event />
  </div>
</template>

<script>
import PageHeader from './sub-components/PageHeader.vue';
import Event from './Event.vue';
export default {
  name: "Home",
  components: { PageHeader, Event }
};
</script>

<style scoped>

</style>

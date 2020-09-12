<template>
  <div>
    <page-header>CSE GameJam</page-header>
    <div><router-link :to="'/register'">register</router-link></div>
    <event :submissions="submissions" :teams="teams" />
  </div>
</template>

<script>
import PageHeader from './sub-components/PageHeader.vue';
import Event from './Event.vue';
import { serverURL } from "@/constants";
export default {
  name: "Home",
  components: { PageHeader, Event },
  data() {
    return {
      submissions: {},
      teams: [],
    }
  },
  created() {
    this.loading = true;
    this.$http.get(serverURL+"/event.php")
    .then(response => {
      this.submissions = response.answers;
      this.teams = response.teams;
    })
    .catch(err => this.$emit("warn", err))
    .finally(() => this.loading = false);
  },
};
</script>

<style scoped>

</style>

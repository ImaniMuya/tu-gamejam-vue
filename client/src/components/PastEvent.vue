<template>
  <div>
    <page-header>{{title}}</page-header>
    <event :pastEvent="eventName" :submissions="submissions" :teams="teams" />
  </div>
</template>

<script>
import PageHeader from './sub-components/PageHeader.vue';
import Event from './Event.vue';
import { serverURL } from "@/constants";

export default {
  name: "PastEvent",
  components: { PageHeader, Event },
  data() {
    return {
      title: "",
      submissions: {},
      teams: [],
      eventName: ""
    }
  },
  created() {
    this.eventName = this.$route.params.eventName
    this.getEventData(this.eventName)
  },
  // beforeRouteUpdate(to, from, next) { //breaks reactivity (figure this out to remove :key hack in App.vue)
  //   this.getEventData(to.params.eventName);
  //   next();
  // },
  methods: {
    getEventData(eventName) {
      this.$http.get(serverURL + `/past.php?event=${eventName}`)
      .then(response => {
        this.title = response.title;
        this.submissions = response.answers;
        this.teams = response.teams;
      })
      .catch(err => this.$emit("warn", err))
    }
  }
}
</script>

<style scoped>

</style>
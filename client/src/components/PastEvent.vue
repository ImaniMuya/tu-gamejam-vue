<template>
  <div>
    <page-header :msg="eventName">{{ title }}</page-header>
    <!-- <page-header>{{ title }}</page-header> -->
    <loader id="page-loader" v-if="loading" :circlesNum="5"/>    
    <event v-else
      :pastEvent="eventName"
      :submissions="submissions"
      :teams="teams"
      :awards="awards"
      :galleryUrls="galleryUrls"
      :eventStatement="eventStatement"
    />
  </div>
</template>

<script>
import PageHeader from './sub-components/PageHeader.vue';
import Event from './Event.vue';
import Loader from '@/components/sub-components/Loader.vue';
import marked from 'marked';
import { serverURL } from "@/constants";

export default {
  name: "PastEvent",
  components: { PageHeader, Event, Loader },
  data() {
    return {
      title: "",
      submissions: {},
      teams: [],
      awards: [],
      galleryUrls: [],
      eventStatement: "",
      eventName: "",
      loading: false
    }
  },
  created() {
    this.eventName = this.$route.params.eventName
    this.getEventData(this.eventName)
  },
  beforeRouteUpdate(to, from, next) { //breaks reactivity (figure this out to fix award links)
    this.eventName = to.params.eventName;
    this.getEventData(to.params.eventName);
    next();
  },
  methods: {
    getEventData(eventName) {
      this.loading = true;
      this.$http.get(serverURL + `/past.php?event=${eventName}`)
      .then(response => {
        this.title = response.title;
        this.submissions = response.answers;
        this.teams = response.teams;
        this.awards = response.awards;
        this.galleryUrls = response.gallery_urls && response.gallery_urls.map(
          x => serverURL + `/past/${eventName}/files/gallery/${x}`
        );
        this.eventStatement = response.event_statement && marked(response.event_statement);
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => this.loading = false)
    }
  }
}
</script>

<style scoped>
#page-loader {
  margin: 40px auto;
}
</style>
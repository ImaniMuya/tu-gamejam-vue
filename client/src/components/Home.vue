<template>
  <div>
    <!-- <timeline /> -->
    <page-header>CSE GameJam</page-header>
    <template v-if="$teamCookieExists">
      <div><router-link :to="'/team'">my team</router-link></div>
      <div><router-link :to="'/submission'">our submission</router-link></div>
    </template>
    <div v-else><router-link :to="'/register'">register</router-link></div>
    <event
      @toast="$emit('toast', $event)"
      @warn="$emit('warn', $event)"
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
// import Timeline from './sub-components/Timeline.vue';
import Event from './Event.vue';
import { serverURL } from "@/constants";
import marked from 'marked';
export default {
  name: "Home",
  components: { PageHeader, Event },
  data() {
    return {
      submissions: {},
      teams: [],
      awards: [],
      galleryUrls: [],
      eventStatement: "",
    }
  },
  created() {
    this.getEventData();
  },
  methods: {
    // logout() {
    //   if (!this.$teamCookieExists) return;
    //   document.cookie = "gja=;expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
    //   document.cookie = "gja=;expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
    // },

    getEventData() {
      this.loading = true;
      this.$http.get(serverURL+"/event.php")
      .then(response => {
        this.submissions = response.answers;
        this.teams = response.teams;
        this.awards = response.awards;
        this.galleryUrls = response.gallery_urls.map(
          x => serverURL + "/files/gallery/" + x
        );
        this.eventStatement = response.event_statement && marked(response.event_statement);
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => this.loading = false);
    }
  }
}
</script>

<style scoped>

</style>

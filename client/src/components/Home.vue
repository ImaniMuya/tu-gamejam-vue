<template>
  <div>
    <page-header>CSE GameJam</page-header>
    <div><router-link :to="'/register'">register</router-link></div>
    <event 
      @toast="$emit('toast', $event)"
      @warn="$emit('warn', $event)"
      :submissions="submissions"
      :teams="teams"
      :awards="awards"
      :galleryUrls="galleryUrls"
    />
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
      awards: [],
      galleryUrls: [],
    }
  },
  created() {
    this.getEventData();
  },
  methods: {
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
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => this.loading = false);
    }
  }
}
</script>

<style scoped>

</style>

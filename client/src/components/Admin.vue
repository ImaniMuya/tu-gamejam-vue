<template>
  <div>
    <page-header>Admin</page-header>
    <h2>Schedule</h2>
    <timecode />

    <h2>Theme Editor</h2>
    <theme-grid />

    
    <h2>Awards</h2>
    <award-grid />

    <h2>Archive Event</h2>
    <input type="text" v-model="eventName" placeholder="S2020" />
    <input type="text" v-model="eventTitle" placeholder="GameJam Spring 2020" />
    <button class="submitbtn" @click="postArchive">Archive</button>

    <h2>All teams</h2>
    <div id="teamGrid">
      <template v-for="team in teams">
        <div :key="team.id">
          {{ team.id }}
           <a :key="team.id+'-login'" :href="loginLink(team)">login as {{ team.name }}</a>
        </div>
        <div :key="team.id+'-name'">{{ team.name }}</div>
        <ul :key="team.id+'-members'">
          <li v-for="(member, idx) in team.members" :key="idx">
            {{ member }}
          </li>
        </ul>
      </template>
    </div>
  </div>
</template>

<script>
import PageHeader from "./sub-components/PageHeader";
import ThemeGrid from './sub-components/ThemeGrid.vue';
import AwardGrid from './sub-components/AwardGrid.vue';
import Timecode from './sub-components/Timecode.vue';
import { serverURL } from "@/constants";
export default {
  name: "Admin",
  components: { PageHeader, Timecode, ThemeGrid, AwardGrid },
  data() {
    return {
      eventName: "",
      eventTitle: "",
      loadingTeams: false,
      teams: {},
    }
  },

  created() {
    window.vm = this;
    this.loadingTeams = true;
    this.$http.get(serverURL + "/teams.php")
    .then(json => this.teams = json)
    .catch(err => this.emit("warn", err))
    .finally(() => this.loadingTeams = false);
  },
  methods: {
    loginLink(team) {
      return `/login?t=${team.id}&s=${team.secret}`;
    },

    postArchive() {
      //TODO: front end validate that past name doesn't already exist... or a confirm window?
      this.$http.post(serverURL + "/archive.php", {}, {
        name: this.eventName,
        title: this.eventTitle
      })
      .then(text => this.$emit("toast", text))
      .catch(err => this.$emit("warn", err))
    }
  }
}
</script>
<style scoped>

</style>
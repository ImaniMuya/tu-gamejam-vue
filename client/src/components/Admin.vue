<template>
  <div v-if="!isAdmin" class="password-container">
    <form @submit.prevent>
      <input type="password" v-model="password">
      <button @click="signIn">Log in</button>
    </form>
  </div>
  <div v-else>
    <page-header>Admin</page-header>
    <a @click="logout">Logout</a>
    <h2>Schedule</h2>

    <h2>Theme Editor</h2>
    <theme-grid 
      @toast="$emit('toast', $event)"
      @warn="$emit('warn', $event)"
    />

    
    <h2>Awards</h2>
    <award-grid :teams="teams"
      @toast="$emit('toast', $event)"
      @warn="$emit('warn', $event)"
    />

    <h2>Archive Event</h2>
    <div class="row">
      <div class="col">
        <label for="event-name">Short Name for link</label>
        <input type="text" id="event-name" v-model="eventName" placeholder="S2020" />
      </div>
      <div class="col">
        <label for="event-title">Event Description</label>
        <input type="text" id="event-title" v-model="eventTitle" placeholder="GameJam Spring 2020" />
      </div>
      <button id="archive-btn" class="submitbtn" @click="postArchive">Archive</button>
    </div>
    <h2>Password</h2>
    <div class="password-container">
      <form @submit.prevent>
        <input type="password" v-model="newPassword" />
        <button id="password-btn" @click="updatePassword">Change Password</button>
      </form>
    </div>
    
    <h2>All teams</h2>
    <div v-if="loadingTeams">Loading...</div> 
    <div v-else id="teamGrid">
      <template v-for="(team, id) in teams">
        <div :key="id">
          {{ id }}
        </div>
        <div :key="id+'login'">
           <a :key="id+'-login'" :href="loginLink(id, team)">login as {{ team.name }}</a>
        </div>
        <div :key="id+'-name'">{{ team.name }}</div>
        <ul :key="id+'-members'" class="members">
          <li
            class="member"
            v-for="(member, idx) in team.members"
            :key="idx"
          >{{ member }}</li>
        </ul>
      </template>
    </div>
  </div>
</template>

<script>
import PageHeader from "./sub-components/PageHeader";
import ThemeGrid from './sub-components/ThemeGrid.vue';
import AwardGrid from './sub-components/AwardGrid.vue';
// import Timecode from './sub-components/Timecode.vue';
import { serverURL } from "@/constants";
import sjcl from 'sjcl';

export default {
  name: "Admin",
  components: { PageHeader, ThemeGrid, AwardGrid },
  data() {
    return {
      eventName: "",
      eventTitle: "",
      loadingTeams: false,
      teams: {},
      newPassword: "",
      password: "",
      isAdmin: false,
    }
  },

  async mounted() {
    let adminCookie = document.cookie.split(';').some(item => item.trim().startsWith("gja="));
    if (adminCookie) {
      this.isAdmin = true;
      this.loadTeams();
    }

  },
  methods: {
    async signIn() {
      try {
        const pwBitArray = sjcl.hash.sha256.hash(this.password);
        const pwHash = sjcl.codec.hex.fromBits(pwBitArray)
        this.password = ""
        let sessionId = await this.$http.post(serverURL + "/admin.php", {}, pwHash) //TODO: hash before sending
        document.cookie = `gja=${sessionId}; expires=${this.getFutureTimestamp(3)}`;
        this.isAdmin = true;
        this.loadTeams();
      } catch (err) { 
        this.$emit('warn', err);
        this.$router.push({ name: 'Home'});
        return;
      }
    },

    logout() {
      document.cookie = "gja=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
      this.$router.push({ name: 'Home'});
      this.$emit('toast', "Signed out.");
    },

    getFutureTimestamp(days) {
      days = days || 1;
      let date = new Date();
      date.setTime(date.getTime()+(days*24*60*60*1000));
      return date.toGMTString();
    },

    loadTeams() {
      this.loadingTeams = true;
      this.$http.get(serverURL + "/teams.php")
      .then(json => {
        json.forEach(team => {
          this.$set(this.teams, team.id, {
            name: team.name,
            members: team.members,
            secret: team.secret
          });
        });
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => this.loadingTeams = false);
    },

    loginLink(id, team) {
      return `/login?t=${id}&s=${team.secret}`;
    },

    postArchive() {
      //TODO: front end validate that past name doesn't already exist... or a confirm window?
      this.$http.post(serverURL + "/archive.php", {}, {
        name: this.eventName,
        title: this.eventTitle
      })
      .then(text => this.$emit("toast", text))
      .catch(err => this.$emit("warn", err))
    },

    updatePassword() {
      const pwBitArray = sjcl.hash.sha256.hash(this.newPassword)
      const pwHash = sjcl.codec.hex.fromBits(pwBitArray)

      this.$http.put(serverURL + "/admin.php", {credentials: 'include'}, pwHash)
      .then(text => this.$emit("toast", text))
      .catch(err => this.$emit("warn", err))
      .finally(() => this.newPassword = "");
    }
  }
}
</script>
<style scoped>
.password-container {
  display: flex;
  justify-content: center;
  margin: 20px;
}

a {
  cursor: pointer;
}


#archive-btn {
  display: inline-block;
  margin: 0 20px;
}

#teamGrid {
  display: grid;
  grid-template-columns: auto auto auto auto;
  align-content: center;
  justify-content: center;;
  gap: 10px;
  margin: 20px;
}

#teamGrid > ul.members {
  margin: 0;
  padding: 0;
}

#teamGrid > ul > li.member {
  display: inline;
  list-style-type: none;
}

#teamGrid > ul > li.member + li.member::before {
  content: ", ";
}
</style>
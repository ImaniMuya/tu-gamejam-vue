<template>
  <div id="app">
    <nav-bar ref="nav" :team="team" :pastEvents="pastEvents"/>
    <div class="app-content" @click="tuckNav">
      <router-view
        @toast="toast"
        @warn="warn"
        @login="attemptLogin()"
        :team="team"
      ></router-view>
    </div>
    <toaster ref="toaster" />
  </div>
</template>

<script>
import NavBar from './components/NavBar.vue'
import Toaster from './components/Toaster.vue';
import { serverURL } from "./constants";

export default {
  name: 'App',
  components: { NavBar, Toaster },
  data() {
    return {
      team: {name: "", id: null},
      warnToast: false,
      loginAttempted: false,
      pastEvents: {}
    }
  },
  methods: {
    tuckNav() {
      this.$refs.nav.tucked = true;
    },
    toast(msg, duration=3000) {
      this.$refs.toaster.toast(msg, duration)
    },
    warn(msg, duration=3000) {
      this.$refs.toaster.warnToast(msg, duration)
    },
    attemptLogin() {
      if (!this.$teamCookieExists) return;
      this.loginAttempted = true;
      this.$http.get(serverURL + "/login.php", {credentials: "include"})
      .then(json => {
        this.team.name = json.name;
        this.team.id = json.id;
        sessionStorage.setItem("team", JSON.stringify(json));
        this.toast("Logged into team: " + json.name);
      })
      .catch(err => {
        document.cookie = `gjt= ; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
        document.cookie = `gjs= ; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
        sessionStorage.removeItem("team");
        this.warn(err);
      });
    }
  },
  created() {
    this.$http.get(serverURL + `/past.php`)
    .then(events => {
      this.pastEvents = events;
    })
    .catch(err => this.$emit("warn", err))
  },
  mounted() {
    if (this.team.id != null) return;
    if (sessionStorage.getItem("team")) this.team = JSON.parse(sessionStorage.getItem("team"));
    if (this.$teamCookieExists && !this.loginAttempted) {
      this.attemptLogin();
    }
  }
}

</script>

<style>
:root {
  --primcolor: #1D2B53;
  --seccolor:#FFA300;
  --tercolor:#FFFFF8;
  --quadcolor: #4F4A41;
}
#app {
  font-family: 'Montserrat', Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin: 0;
  overflow: auto;
}
body {
  margin: 0;
}

.app-content {
  min-height: 100vh;
  margin-left: 75px;
}

a {
  text-decoration: none;
}

a::before {
  display: inline-block;
  text-align: center;
  opacity: 0.3;
  width: 1ch;
  font-family: "Ubuntu Mono", sans-serif;
  content: "[";
}

a::after {
  display: inline-block;
  text-align: center;
  opacity: 0.3;
  width: 1ch;
  font-family: "Ubuntu Mono", sans-serif;
  content: "]";
}

a:hover::before {
  content: "<";
}

a:hover::after {
  content: ">";
}

input[type=text] {
  border: 2px solid var(--quadcolor);
  border-radius: 5px;
  padding: 5px;
}
input[type=text]:focus, textarea:focus {
  border: 2px solid var(--seccolor);
  outline: none;
}

textarea {
  box-sizing: border-box;
  border-radius: 5px;
  padding: 5px;
  border: 2px inset var(--quadcolor);
  max-width: 300px;
  max-height: 500px;
  min-width: 50px;
  min-height: 50px;
}

.submitbtn {
  display: block;
  width: 100px;
  background-color: var(--primcolor);
  color: var(--tercolor);
  margin: 10px auto;
  padding: 4px;
  border-radius: 5px;
  cursor: pointer;
}

.row {
  display: flex;
  flex-flow: row;
  align-items: center;
  justify-content: space-evenly;
}

.col {
  display: flex;
  flex-flow: column;
  align-items: center;
  justify-content: space-evenly;
}

</style>

<template>
  <div id="app">
    <nav-bar ref="nav"/>
    <div class="app-content" @click="tuckNav">
      <router-view @toast="toast($event)"></router-view>
    </div>
    <toaster ref="toaster"/>
  </div>
</template>

<script>
import NavBar from './components/NavBar.vue'
import Toaster from './components/Toaster.vue';

export default {
  name: 'App',
  components: { NavBar, Toaster },
  data() {
    return {
      team: null
    }
  },
  methods: {
    tuckNav() {
      this.$refs.nav.tucked = true;
    },
    toast(msg) {
      this.$refs.toaster.toast(msg);
    },
    teamCookieExists() {
      return (document.cookie.split(';').some(item => item.trim().startsWith("team=")));
    }
  },
  mounted() {
    if (!this.teamCookieExists()) return;
    fetch("http://localhost:4321/login.php", {credentials: "include"}).then(response => {
      response.text().then(text => {
        this.toast(text); //TODO: use other toast for error
        if (response.status == 200) {
          this.team = text;
        }
      });
    });
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
  border-radius: 5px;
  padding: 5px;
  max-width: 250px;
}

</style>

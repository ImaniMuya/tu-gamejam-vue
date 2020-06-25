<template>
  <div>
    <theme-grid />
    <input type="text" v-model="eventName" placeholder="S2020" />
    <input type="text" v-model="eventTitle" placeholder="GameJam Spring 2020" />
    <button @click="postArchive">Archive Event</button>
  </div>
</template>

<script>
import ThemeGrid from './sub-components/ThemeGrid.vue';
import { serverURL } from "@/constants";
export default {
  name: "Admin",
  components: { ThemeGrid },
  data() {
    return {
      eventName: "",
      eventTitle: "",
    }
  },
  created() {
    window.vm = this;
  },
  methods: {
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
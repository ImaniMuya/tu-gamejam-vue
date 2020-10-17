<template>
  <div class="row">
    <div class="col">
      <label for="event-name">Short Name for link</label>
      <input type="text" id="event-name" v-model="eventName" placeholder="S2020" />
    </div>
    <div class="col">
      <label for="event-title">Event Description</label>
      <input type="text" id="event-title" v-model="eventTitle" placeholder="GameJam Spring 2020" />
    </div>
    <button id="archive-btn" class="submitbtn" @click="postArchive" :disabled="archiving">Archive</button>
  </div>
</template>

<script>
import { serverURL } from "@/constants";
export default {
  name: "Archiver",
  data() {
    return {
      archiving: false,
      eventName: "",
      eventTitle: "",
    }
  },
  methods: {
    postArchive() {
      this.archiving = true;
      this.$http.post(serverURL + "/archive.php", { credentials: 'include' }, {
        name: this.eventName,
        title: this.eventTitle
      })
      .then(text => {
        this.archiving = false;
        this.eventName = "";
        this.eventTitle = "";
        this.$emit("toast", text);
      })
      .catch(err => this.$emit("warn", err))
    },
  }
}
</script>

<style scoped>
#archive-btn {
  display: inline-block;
  margin: 0 20px;
}
</style>
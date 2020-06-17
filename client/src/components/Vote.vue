<template>
  <div>
    <page-header msg="Cast Your Vote!">Theme Voting</page-header>
    <loader id="page-loader" v-if="loading" :circlesNum="5"/>
    <div v-else-if="submitting">
      <div v-for="theme in themes" :key="theme.id" class="preview">
        {{ theme.name }}
      </div>
      <loader id="page-loader" :circlesNum="3"/>
    </div>
    <div v-else class="outer">
      <ul id="choices" ref="choices">
        <li v-for="(theme, index) in themes"
          :key="theme.id"
          :class="['choice', {over: dropSpot == index}, {ghost: index == heldThemeIdx}]"
          @dragstart="handleDragStart($event, index)"
          @dragover="handleDragOver($event, index)"
          @dragend="handleDragEnd()"
          :value="theme.id"
        >
          {{ theme.name }}
        </li>
        <div id="dummyChoice"
          :class="{over: dropSpot == themes.length}"
          @dragover="handleDragOver($event, themes.length)"
          @dragend="handleDragEnd()"
        ></div> 
      </ul>
      <button @click="sendVote()">Submit</button>
    </div>
  </div>
</template>

<script>
import PageHeader from './sub-components/PageHeader.vue';
import Loader from './sub-components/Loader.vue';
import { serverURL } from "../constants";

export default {
  name: "Vote",
  components: { PageHeader, Loader },
  data() {
    return {
      loading: false,
      submitting: false,
      themes: [],
      heldThemeIdx: null,
      dropSpot: null,
    }
  },
  created() {
    if (!this.$teamCookieExists) {
      this.$emit("warn", "You need to login first.")
      this.$router.push({ name: 'Home'});
      return;
    }
    this.loading = true;
    this.$http.get(serverURL+"/vote.php", {credentials: "include"})
    .then(json => {
      json.forEach(theme => {
        this.themes.push(this.createThemeEntry(theme));
      });
    })
    .catch(err => this.$emit("warn", err))
    .finally(() => this.loading = false);
  },

  methods: {
    createThemeEntry(theme) {
      return {
        name: theme.theme,
        id: theme.theme_id,
        rank: theme.ranking,
      };
    },
    handleDragStart(e, index) {
      this.heldThemeIdx = index;
      e.target.classList.add('ghost');
    },
    handleDragOver(e, index) {
      if (e.preventDefault) e.preventDefault(); // Allows us to drop.
      this.dropSpot = index;
      return false;
    },
    handleDragEnd() {
      let dropIdx = this.dropSpot
      if (dropIdx > this.heldThemeIdx) dropIdx--;
      this.themes.splice(dropIdx, 0, this.themes.splice(this.heldThemeIdx, 1)[0]);
      this.heldThemeIdx = null;
      this.dropSpot = null;
    },
    getVoteArray() {
      const arr = []
      this.$refs.choices.children.forEach(x => {
        if (x.value != null) arr.push(x.value)
      })
      return arr;
    },
    sendVote() {
      this.submitting = true;
      this.$http.post(serverURL+"/vote.php", {credentials: "include"}, this.getVoteArray())
      .then(text => this.$emit("toast", text))
      .catch(err => this.$emit("warn", err))
      .finally(() => this.submitting = false)
    }
  }
}
</script>

<style scoped>
#page-loader {
  margin: 20px auto;
}

#choices {
  list-style-type: none;
  padding-inline-start: 0;
  display: flex;
  flex-flow: column;
  align-items: center;
}

.choice {
  display: block;
  width: 250px;
  padding: 5px;
  background-color: #ccc;
  text-align: center;
  cursor: grab;
  border-radius: 10px;
  border: 2px solid var(--tercolor);
  transition: all .2s ease-in-out;
  font-size: 23px;
  overflow-wrap: break-word;

  -moz-user-select: none;
  -khtml-user-select: none;
  -webkit-user-select: none;
  user-select: none;
  -khtml-user-drag: element;
  -webkit-user-drag: element;

}

.choice:hover {
  transform: scale(1.025);
}

.choice.ghost {
  opacity: 0.4;
}

.over {
  border-top: 2px solid var(--primcolor) !important;
}


#dummyChoice {
  display: block;
  height: 10px;
  padding: 5px;
  width: 250px;
  border-top: 2px solid transparent;
  -moz-user-select: none;
  -khtml-user-select: none;
  -webkit-user-select: none;
  -khtml-user-drag: element;
  -webkit-user-drag: element;
  user-select: none;
}

button {
  display: block;
  width: 100px;
  background-color: var(--primcolor);
  color: var(--tercolor);
  margin: 10px auto;
  padding: 0.65%;
  border-radius: 5px;
  transition: all .2s ease-in-out;
}

button:hover {
  cursor: pointer;
}

.preview {
  margin: 5px auto;
  text-align: center;
}

</style>

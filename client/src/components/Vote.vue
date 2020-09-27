<template>
  <div>
    <page-header msg="Cast Your Vote!">Theme Voting</page-header>
    <loader id="page-loader" v-if="loading" :circlesNum="5"/>
    <div v-else-if="submitting">
      <div class="preview">
        <div v-for="theme in themes" :key="theme.id">
          {{ theme.name }}
        </div>
      </div>
      <loader id="page-loader" :circlesNum="3"/>
    </div>
    <div v-else class="outer">
      <ul id="choices" ref="choices">
        <li v-for="(theme, index) in themes"
          :key="theme.id"
          draggable="true"
          :style="extraStyle"
          :class="['choice', {over: dropSpot == index}, {ghost: index == heldThemeIdx}]"
          @dragstart="handleDragStart($event, index)"
          @dragover="handleDragOver($event, index)"
          @dragend="handleDragEnd()"
          @drop.prevent
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
      <button class="submitbtn" @click="sendVote()">Submit</button>
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
  mounted() {
    if (!this.$teamCookieExists) {
      this.$emit("warn", "You need to login first.");
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
  computed: {
    extraStyle() {
      return {
        '--margin-duration': this.heldThemeIdx == null ? "0s" : ".3s"
      }
    }
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
      e.dataTransfer.setData('text/html', index);
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
  position: relative;
}

.choice {
  cursor: grab;
  -moz-user-select: none;
  -khtml-user-select: none;
  -webkit-user-select: none;
  user-select: none;
  -khtml-user-drag: element;
  -webkit-user-drag: element;
  
  display: block;
  box-sizing: border-box;
  min-height: 40px;
  width: 250px;
  padding: 5px;
  background-color: #ccc;
  text-align: center;
  border-radius: 10px;
  margin: 2px;
  font-size: 23px;
  overflow-wrap: break-word;
  transition: margin var(--margin-duration), opacity .1s;
}

.choice:hover {
  transform: scale(1.025);
}

.choice.ghost {
  opacity: 0;
  margin-bottom: -42px;
}

.choice.over {
  margin-top: 44px;
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
  user-select: none;
}

button:hover {
  cursor: pointer;
}

.preview {
  margin: 16px auto;
  text-align: center;
  display: flex;
  flex-flow: column;
  align-items: center;
}

.preview > div {
  box-sizing: border-box;
  min-height: 40px;
  padding: 5px;
  width: 250px;
  margin: 2px;
  font-size: 23px;
  overflow-wrap: break-word;
}
</style>

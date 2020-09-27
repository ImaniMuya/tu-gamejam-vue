<template>
  <loader id="grid-loader" v-if="loadingThemes" :circlesNum="5"/>
  <div v-else id="theme-grid">
    <div></div>
    <div>
      <button v-if="editingAnyTheme" @click="themes.forEach(x => saveTheme(x))">Save All</button>
      <button v-else @click="themes.forEach(x => editTheme(x))">Edit All</button>
    </div>
    <template v-for="(theme, index) in themes">
      <template v-if="theme.editing">
        <input type="text" :key="index" v-model="theme.name" :disabled="theme.saving" maxlength="50" />
        <div :key="index + 'btns'" class="btn-container">
          <loader v-if="theme.saving" :key="index+'save'" :circlesNum="1" />
          <img v-else class="icon" src="../../assets/save.png" alt="Save" @click="saveTheme(theme)" title="Save">
          <loader v-if="theme.saving" :circlesNum="1" />
          <img v-else class="icon" src="../../assets/cancel.png" alt="Cancel" @click="cancelEditTheme(theme)" title="Cancel">
        </div>
      </template>
      <template v-else>
        <div :key="index">{{ theme.name }}</div>
        <div :key="index + 'btns'" class="btn-container">
            <loader v-if="theme.deleting" :circlesNum="1"/>
            <img v-else class="icon" src="../../assets/edit.png" alt="Edit" @click="editTheme(theme)" title="Edit"/>
            <loader v-if="theme.deleting" :circlesNum="1"/>
            <img v-else class="icon" src="../../assets/delete.png" alt="Delete" @click="deleteTheme(theme)" title="Remove"/>
        </div>
      </template>
    </template>
    <input type="text" placeholder="New Theme" v-model="newTheme" :disabled="addingTheme" maxlength="50" />
    <div class="btn-container">
      <loader v-if="addingTheme" :circlesNum="1"/>
      <img v-else class="icon" src="../../assets/plus.png" alt="Add" title="Add New" @click="addTheme()">
    </div>
  </div>
</template>

<script>
import Loader from './Loader.vue';
import { serverURL } from "../../constants";

export default {
  name: "ThemeGrid",
  components: { Loader },
  
  data() {
    return {
      loadingThemes: true,
      addingTheme: false,
      themes: [],
      newTheme: "",
    }
  },
  created() {
    this.loadingThemes = true;
    this.$http.get(serverURL + "/theme.php", {credentials: "include"})
    .then(json => json.forEach(x => this.themes.push(this.createTheme(x))))
    .catch(err => this.$emit("warn", err))
    .finally(() => this.loadingThemes = false);
  },
  computed: {
    editingAnyTheme() {
      return this.themes.some(x => x.editing);
    }
  },
  methods: {
    createTheme(dbTheme) {
      return {
        id: dbTheme.theme_id,
        name: dbTheme.theme,
        editing: false,
        saving: false,
        deleting: false,
      }
    },
    editTheme(theme) {
      theme.editing = true;
      this.$set(theme, "origName", theme.name);
    },
    cancelEditTheme(theme) {
      if (theme.saving) return;
      theme.editing = false;
      theme.name = theme.origName;
    },
    saveTheme(theme) {
      const nameChange = theme.name != theme.origName;
      if (!nameChange || !theme.editing) {
        theme.editing = false;
        return;
      }
      if (!theme.name) {
        this.$emit("warn", "Name missing.");
        return;
      }
      theme.saving = true;
      this.$http.put(serverURL + "/theme.php?id=" + theme.id, {credentials: "include"}, theme.name)
      .then(name => {
        theme.name = name;
        theme.editing = false;
      })
      .catch(err => {
        this.$emit("warn", err);
      })
      .finally(() => theme.saving = false)
    },
    deleteTheme(theme) {
      if (!confirm(`Remove ${theme.name}?`)) return;
      theme.deleting = true;
      this.$http.delete(serverURL + "/theme.php?id=" + theme.id, {credentials: "include"})
      .then(text => {
        this.$emit("toast", text);
        this.themes = this.themes.filter(x => x.id != theme.id);
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => theme.deleting = false)
    },
    addTheme() {
      this.newTheme = this.newTheme.trim();
      if (!this.newTheme) {
        this.$emit("warn", "Can't add empty theme.");
        return;
      }

      this.addingTheme = true;
      this.$http.post(serverURL + "/theme.php", {credentials: "include"}, this.newTheme)
      .then(theme => {
        this.themes.push(this.createTheme(theme));
        this.newTheme = "";
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => this.addingTheme = false);
    },

  }

}
</script>
<style scoped>
#theme-grid {
  display: grid;
  grid-template-columns: auto max-content;
  gap: 10px 5px;
  margin: 15px;
  margin-left: 75px;
  align-items: center;
}

#theme-grid > div {
  max-width: 350px;
  overflow-wrap: break-word;
}

.btn-container {
  display: flex;
  flex-flow: row wrap;
  justify-content: flex-start;
  align-items: center;
}

.grid-loader {
  margin: 20px auto;  
}

.icon {
  width: 24px;
  cursor: pointer;
}

.icon + .icon {
  margin-left: 4px;
}
</style>

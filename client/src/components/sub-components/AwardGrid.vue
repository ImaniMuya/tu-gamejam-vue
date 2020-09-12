<template>
  <loader id="grid-loader" v-if="loadingAwards" :circlesNum="5"/>
  <div v-else id="award-grid">
    <div></div>
    <div></div>
    <div>
      <button v-if="editingAnyAward" @click="awards.forEach(x => saveAward(x))">Save All</button>
      <button v-else @click="awards.forEach(x => editAward(x))">Edit All</button>
    </div>
    <template v-for="(award, index) in awards">
      <template v-if="award.editing">
        <input type="text" :key="index+'-name'" v-model="award.name"
          :disabled="award.saving" maxlength="50" />
        <select :key="index+'-winner'" v-model="award.winner">
          <template v-for="(team, id) in teams">
            <option :key="id+'-team'" :value="id">{{ team.name }}</option>
          </template>
        </select>
        <div :key="index + 'btns'" class="btn-container">
          <loader v-if="award.saving" :key="index+'save'" :circlesNum="1" />
          <img v-else class="icon" src="../../assets/save.png" alt="Save"
            @click="saveAward(award)" title="Save">
          <loader v-if="award.saving" :circlesNum="1" />
          <img v-else class="icon" src="../../assets/cancel.png" alt="Cancel"
            @click="cancelEditAward(award)" title="Cancel">
        </div>
      </template>
      <template v-else>
        <div :key="index+'-name'">{{ award.name }}</div>
        <div :key="index+'-winner'">{{ getTeamName(award.winner) }}</div>
        <div :key="index + 'btns'" class="btn-container">
            <loader v-if="award.deleting" :circlesNum="1"/>
            <img v-else class="icon" src="../../assets/edit.png" alt="Edit"
              @click="editAward(award)" title="Edit"/>
            <loader v-if="award.deleting" :circlesNum="1"/>
            <img v-else class="icon" src="../../assets/delete.png" alt="Delete"
              @click="deleteAward(award)" title="Remove"/>
        </div>
      </template>
    </template>
    <input type="text" placeholder="New Award" v-model="newAward"
      :disabled="addingAward" maxlength="50" />
    <div></div>
    <div class="btn-container">
      <loader v-if="addingAward" :circlesNum="1"/>
      <img v-else class="icon" src="../../assets/plus.png" alt="Add"
        title="Add New" @click="addAward()">
    </div>
  </div>
</template>

<script>
import Loader from './Loader.vue';
import { serverURL } from "../../constants";
export default {
  name: "AwardGrid",
  components: { Loader },
  props: ['teams'],

  data() {
    return {
      loadingAwards: true,
      addingAward: false,
      newAward: "",
      newAwardWinner: "",
      awards: [],
    }
  },
  computed: {
    editingAnyAward() {
      return this.awards.some(x => x.editing);
    }
  },
  created() {
    this.loadingAwards = true;
    this.$http.get(serverURL + "/award.php")
    .then(json => json.forEach(x => 
      this.awards.push(this.createAward(x))
    ))
    .catch(err => this.emit("warn", err))
    .finally(() => this.loadingAwards = false);
  },
  methods: {
    createAward(dbAward) {
      return {
        id: dbAward.award_id,
        name: dbAward.award,
        winner: dbAward.team_id,
        editing: false,
        saving: false,
        deleting: false,
      }
    },
    editAward(award) {
      award.editing = true;
      this.$set(award, "origName", award.name);
      this.$set(award, "origWinner", award.winner);
    },
    cancelEditAward(award) {
      if (award.saving) return;
      award.editing = false;
      award.name = award.origName;
      award.winner = award.origWinner;
    },
    saveAward(award) {
      if (award.saving) return;
      if (!award.editing) return;
      if (!award.name) {
        this.$emit("warn", "Name missing.");
        return;
      }

      const sameName = award.name == award.origName;
      const sameWinner = award.winner == award.origWinner;
      if (sameName && sameWinner) {
        award.editing = false;
        return;
      }

      award.saving = true;
      this.$http.put(serverURL + "/award.php?id=" + award.id, {}, {
        name: award.name,
        winner: award.winner
      })
      .then(dbAward => {
        award.name = dbAward.award;
        award.winner = dbAward.team_id;
        award.editing = false;
      })
      .catch(err => {
        this.$emit("warn", err);
      })
      .finally(() => award.saving = false)
    },
    getTeamName(id) {
      let team = this.teams[id];
      if (!team) return "";

      return team.name;
    },
    addAward() {
      this.newAward = this.newAward.trim();
      if (!this.newAward) {
        this.$emit("warn", "Can't add empty award.");
        return;
      }

      this.addingAward = true;
      this.$http.post(serverURL + "/award.php", {}, this.newAward)
      .then(dbAward => {
        this.awards.push(this.createAward(dbAward));
        this.newAward = "";
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => this.addingAward = false);
    },
  }
}
</script>
<style scoped>
#award-grid {
  display: grid;
  grid-template-columns: auto auto max-content;
  gap: 10px 5px;
  margin: 15px;
  margin-left: 75px;
  align-items: center;
}

#award-grid > div {
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
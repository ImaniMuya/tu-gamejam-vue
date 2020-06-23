<template>
  <div>
    <div id="imgModal" :class="{tucked: modalTucked}" @click="modalTucked = true">
      <img :src="modalImgSrc"/>
    </div>
    <loader v-if="loading" :circlesNum="5"/>
    <div v-else class="submission-container">
      <submission-display v-for="team in teams" :key="team.id"
        :submission="submissions[team.id]"
        :team="team"
        @popup="popupImage"
      />
    </div>
  </div>
</template>

<script>
import Loader from './sub-components/Loader.vue';
import SubmissionDisplay from './sub-components/SubmissionDisplay.vue';
import { serverURL } from "../constants";
export default {
  name: "Event",
  components: { Loader, SubmissionDisplay },
  data() {
    return {
      loading: false,
      submissions: {},
      teams: [],
      modalTucked: true,
      modalImgSrc: "",
    }
  },
  created() {
    this.loading = true;
    this.$http.get(serverURL+"/event.php")
    .then(response => {
      this.submissions = response.answers;
      let submissionlessTeams = []
      for (let id in response.teams) {
        let team = {...response.teams[id], id};
        if (id in this.submissions) this.teams.push(team);
        else submissionlessTeams.push(team);
      }
      this.teams.push(...submissionlessTeams);
    })
    .catch(err => this.$emit("warn", err))
    .finally(() => this.loading = false);
  },
  methods: {
    popupImage($event) {
      this.modalImgSrc = $event;
      this.modalTucked = false;
    }
  }
}
</script>

<style scoped>

#imgModal {
  position: fixed;
  transition: transform .3s ease-in-out;
  transform: none;
  height: 100vh;
  width: 100vw;
  background-color: rgba(0,0,0,0.9);
  display: flex;
  flex-flow: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  top: 0;
  left: 0;
  z-index: 101;
  opacity: 1;
  cursor: pointer;

  transition: opacity .25s ease;
}

#imgModal.tucked {
  opacity: 0;
  visibility: hidden;
}

#imgModal > img {
  max-width: 90vw;
  max-height: 90vh;
}
</style>
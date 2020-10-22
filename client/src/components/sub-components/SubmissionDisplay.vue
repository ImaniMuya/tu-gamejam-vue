<template>
  <div class="outer" :id="team.id+'-'+team.name">
    <h2 v-if="gameName" class="game-heading">{{ gameName }}</h2>
    <h2 v-else-if="submission" class="game-heading">(untitled)</h2>
    <div class="display-row">
      <image-carousel v-if="imgSrcs.length" @popup="$emit('popup', $event)" :imgSrcs="imgSrcs"/>
      <div class="grid">
        <div class="field-label">Team Name</div>
        <div class="bold breakable">{{ team.name }}</div>
        <template v-if="team.members && team.members.length">
        <div class="field-label">Members</div>
        <div>
          <span v-for="(member, index) in team.members" class="member" :key="index">{{ member }}</span>
        </div>
        </template>
        <template v-for="(field, index) in displayFields">
        <div :key="index" class="field-label">{{ field.name }}</div>
        <div v-if="field.type == 'text'" :key="index+'-val'">{{ field.value }}</div>
        <div v-else-if="field.type == 'link'" :key="index+'-val'">
          <a :href="field.value">link</a>
        </div>
        <div v-else-if="field.type == 'download'" :key="index+'-val'">
          <a :href="field.value" download="submission.zip">download</a>
        </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { serverURL } from "../../constants";
import ImageCarousel from './ImageCarousel.vue';
export default {
  name: "SubmissionDisplay",
  components: { ImageCarousel },
  props: ["submission", "team", "pastEvent"],
  data: () => ({
    imgSrcs: [],
    displayFields: [],
    gameName: "",
  }),
  mounted() {
    if (this.submission == null) return;
    this.imgSrcs = [
      this.submission["Main Picture"],
      this.submission["Picture 1"],
      this.submission["Picture 2"]
    ]
    .filter(x => x != null)
    .map(x => this.getResourcePath(x.qid, x.value));
    
    // this.appendTextField("Name", "text");
    this.gameName = this.submission["Name"] ? this.submission["Name"].value : null;

    this.appendTextField("Description", "text");
    this.appendTextField("How To Play", "text");
    this.appendTextField("Game URL", "link");
    this.appendTextField("Submission zip", "download");
  },
  methods: {
    getResourcePath(qid, basename) {
      let teampath = `${this.team.id}/${qid}-${basename}`;
      if (!this.pastEvent) return serverURL + "/files/" + teampath;
      else return serverURL + `/past/${this.pastEvent}/files/${teampath}`;
    },
    appendTextField(name, type) {
      if (!this.submission[name]) return;
      let value = this.submission[name].value
      if (type === "download") value = this.getResourcePath(this.submission[name].qid, value);
      this.displayFields.push({ name, type, value });
    }
  }
}

</script>

<style scoped>
.outer {
  display: flex;
  flex-flow: column;
  padding: 30px 0;
}

.display-row {
  display: flex;
  flex-flow: row wrap;
  justify-content: center;
  align-items: flex-start;
  width: 100%;
}

.grid {
  display: inline-grid;
  grid-template-columns: max-content 1fr;
  margin: 20px;
  gap: 10px 10px;
  width: 500px;
  /* align-self: stretch; */
}

.grid > div {
  overflow-wrap: break-word;
  min-width: 0px;
  white-space: pre-wrap;
}

h2.game-heading {
  margin: 0;
  margin-bottom: 10px;
  text-align: center;
}

.bold {
  font-weight: bold;
}

.member + .member::before {
  content: ", "
}

@media screen and (max-width: 600px) {
  .grid {
    width: 100%;
    grid-template-columns: 1fr;
    gap: 5px;
  }
  .field-label {
    margin-top: 15px;
  }
}
</style>
<template>
  <div>
    <page-header :msg="team">Submit Your Game!</page-header>
    <loader id="page-loader" v-if="loading" :circlesNum="5"/>
    <div v-else class="grid">
      <template v-for="field of submission">
        <div :key="field.id">
          {{field.id}}: {{field.label}} - {{field.value}}
        </div>
      </template>
      
    </div>
  </div>
</template>

<script>
import PageHeader from "./sub-components/PageHeader";
import Loader from "./sub-components/Loader";
import { serverURL } from "../constants";

export default {
  name: "Submission",
  components: { PageHeader, Loader },
  props: { team: String },
  data() {
    return {
      loading: false,
      submission: [],
    }
  },
  
  created() {
    window.vm=this;
    if (!this.$teamCookieExists) {
      this.$emit("warn", "You need to login first.")
      this.$router.push({ name: 'Home'});
      return;
    }
    this.loading = true;
    this.$http.get(serverURL+"/submission.php", {credentials: "include"})
    .then(fields => {
      fields.forEach(dbSubmissionField => {
        this.submission.push(this.createSubmissionField(dbSubmissionField));
      });
    })
    .catch(err => this.$emit("warn", err))
    .finally(() => this.loading = false);
  },

  methods: {
    createSubmissionField(dbSubmissionField) {
      return {
        id: dbSubmissionField.question_id,
        type: dbSubmissionField.category_name,
        label: dbSubmissionField.question,
        value: dbSubmissionField.answer
      };
    }
    // mapping of type -> input field
  }
}
</script>
<style scoped>
#page-loader {
  margin: 20px auto;
}

.grid {
  display: grid;
  grid-template-columns: auto;
  gap: 10px 5px;
  margin: 15px 20px;
  align-items: center;
}



</style>

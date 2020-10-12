<template>
  <div>
    <timeline />
    <page-header :msg="team.name">Submit Your Game!</page-header>
    <loader v-if="loading" id="page-loader" :circlesNum="5"/>
    <template v-else>
      <form class="grid" @submit.prevent ref="form">
        <template v-for="field of submission">
          <label class="label" :for="field.id" :key="field.id + 'label'">{{ field.label }}</label>
          <submission-input
            :key="field.id"
            :name="field.id"
            :type="field.type"
            :placeholder="field.placeholder"
            :teamId="team.id"
            :disabled="submitting"
            :imgPristine="field.imgPristine"
            @imgDirty="field.imgPristine = false"
            v-model="field.value"
          />
        </template>
      </form>
      <loader v-if="submitting" id="submit-loader" :circlesNum="3"/>
      <button v-else @click="submit" class="submitbtn" :disabled="submitting">Submit</button>
    </template>
  </div>
</template>

<script>
import Timeline from "./sub-components/Timeline";
import PageHeader from "./sub-components/PageHeader";
import Loader from "./sub-components/Loader";
import SubmissionInput from "./sub-components/SubmissionInput";
import { serverURL } from "../constants";

export default {
  name: "Submission",
  components: { PageHeader, Loader, SubmissionInput, Timeline },
  props: ["team"],
  data() {
    return {
      loading: false,
      submitting: false,
      submission: [],
    }
  },  
  mounted() {
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
        value: dbSubmissionField.answer,
        placeholder: dbSubmissionField.placeholder,
        imgPristine: true
      };
    },
    submit() {
      let formData = new FormData(this.$refs.form)
      this.submitting = true;
      this.$http.post(serverURL + "/submission.php", {credentials: "include"}, formData)
      .then(text => {
        this.$emit("toast", text);
        this.submission.forEach(x => x.imgPristine = true)
      })
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

.grid {
  display: grid;
  grid-template-columns: max-content max-content;
  gap: 10px 10px;
  margin: 20px;
  margin-left: 60px;
  align-items: start;
  justify-content: center;
}

@media screen and (max-width: 600px) {
  .grid {
    grid-template-columns: auto;
    gap: 5px 5px;
    text-align: right;
  }
  .label {
    text-align: left;
    margin-top: 15px;
  }
}

#submit-loader {
  margin: 20px auto;
}

</style>

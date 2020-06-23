<template>
  <div>
    <input v-if="isText" type="text" :value="value" @input="$emit('input', $event.target.value)" 
      :name="name" :id="name" :disabled="disabled"
    />
    <textarea v-else-if="isTextArea" :value="value" @input="$emit('input', $event.target.value)"
      :name="name" :id="name" :disabled="disabled"
      rows='4' cols='40' wrap="soft" maxlength="2000"
    ></textarea>
    <div v-else-if="isImage">
      <input :hidden="value" type="file" :name="name" :id="name" @change="imgChanged" :disabled="disabled"/>
      <label :for="name" title="Click to select a file">
        <img v-if="value && imgPristine" :src="imgSrc" alt="image">
        <span v-else>{{ value }}</span>
      </label>
    </div>
    <div v-else-if="isFile">
      <input :hidden="value" type="file" :name="name" :id="name" 
        @change="$emit('input', $event.target.files[0].name);" :disabled="disabled"
      />
      <label :for="name" title="Click to select a file">{{ value }}</label>
    </div>
  </div>
</template>

<script>
import { serverURL } from "../../constants";
export default {
  name: "SubmissionInput",
  props: ["type", "value", "name", "teamId", "imgPristine", "disabled"],
  computed: {
     isText() { return this.type === "text" },
     isTextArea() { return this.type === "textarea" },
     isImage() { return this.type === "image" },
     isFile() { return this.type === "file" },
     imgSrc() {
       if (!this.imgPristine) return "";
       return serverURL + `/files/${this.teamId}/${this.name}-${this.value}`
     }
  },
  methods: {
    imgChanged($event) {
      this.$emit("imgDirty");
      let basename = $event.target.files[0].name;
      this.$emit("input", basename);
    }
  }
}
</script>

<style scoped>
label {
  cursor: pointer;
  font-weight: bold;
}
img {
  max-height: 300px;
  max-width: 300px;
}

input[type=text] {
  box-sizing: border-box;
  width: 300px;
}
</style>
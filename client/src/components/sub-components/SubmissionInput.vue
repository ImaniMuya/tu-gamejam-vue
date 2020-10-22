<template>
  <input v-if="isText" type="text" :value="value" @input="$emit('input', $event.target.value)" 
    :name="name" :id="name" :disabled="disabled" :placeholder="[[placeholder]]"
  />
  <textarea v-else-if="isTextArea" :value="value" @input="$emit('input', $event.target.value)"
    :name="name" :id="name" :disabled="disabled" :placeholder="[[placeholder]]"
    rows='4' cols='40' wrap="soft" maxlength="2000"
  ></textarea>
  <div class="file-container" v-else-if="isImage">
    <input :hidden="value" type="file" :name="name" ref="fileInput"
      :id="name" @change="fileChanged" :disabled="disabled"
     />
    <img v-if="value && pristine" :src="imgSrc"
      class="img-preview"
      alt="image"
      @click="downloadFile(imgSrc)"
      title="download"
     />
    <span v-if="value && pristine"></span>
    <label v-else :for="name" title="Select a file">{{ value }}</label>
    <div class="button-container">
      <template v-if="value">
        <label :for="name" title="Select a file" class="button-alike">Change</label>
        <img class="icon" src="../../assets/delete.png" alt="Delete"
          @click="removeFile()" title="Remove"/>
      </template>
    </div>
  </div>
  <div class="file-container" v-else-if="isFile">
    <input :hidden="value" type="file" :name="name" :id="name" ref="fileInput"
      @change="fileChanged" :disabled="disabled"
    />
    <div class="pointer" @click="downloadFile" title="download">{{ value }}</div>
    <div class="button-container">
      <template v-if="value">
        <label :for="name" title="Select a file" class="button-alike">Change</label>
        <img class="icon" src="../../assets/delete.png" alt="Delete"
          @click="removeFile()" title="Remove"/>
      </template>
    </div>
  </div>
</template>

<script>
import { serverURL } from "../../constants";
export default {
  name: "SubmissionInput",
  props: ["type", "value", "origValue", "name", "placeholder", "teamId", "pristine", "disabled"],
  computed: {
     isText() { return this.type === "text" },
     isTextArea() { return this.type === "textarea" },
     isImage() { return this.type === "image" },
     isFile() { return this.type === "file" },
     imgSrc() {
       if (!this.pristine) return "";
       return serverURL + `/files/${this.teamId}/${this.name}-${this.value}`
     }
  },
  methods: {
    fileChanged($event) {
      let files = $event.target.files;
      if (files.length == 0) { //selected then cancelled
        this.$emit('input', this.origValue);
        this.$emit('clean');
        return;
      }
      let basename = null;
      if (files.length > 0) basename = $event.target.files[0].name;
      this.$emit("dirty");
      this.$emit('input', basename)
    },
    removeFile() {
      if (!this.value) return;
      if (this.disabled) return;

      this.$refs.fileInput.value = null;
      this.$emit("input", null);
      this.$emit("deleteAnswer");
    },
    downloadFile() {
      const a = document.createElement('a');
      a.href = serverURL + `/files/${this.teamId}/${this.name}-${this.value}`;
      a.target = "_blank";
      a.download = this.value; // the filename to download as
      a.click();
    }
  }
}
</script>

<style scoped>
label {
  cursor: pointer;
  font-weight: bold;
  max-width: 300px;
  overflow-wrap: break-word;
}
img {
  max-height: 300px;
  max-width: 300px;
}

.button-alike {
  border-width: 2px;
  border-style: outset;
  border-color: -internal-light-dark(rgb(118, 118, 118), rgb(133, 133, 133));
  border-image: initial;
  font: 400 13.3333px;
  padding: 1px 6px;
  margin: 0 10px;
}

input[type=text] {
  box-sizing: border-box;
  width: 300px;
}

.icon {
  width: 24px;
  cursor: pointer;
}

.button-container {
  display: inline-flex;
  align-items: center;
}

.file-container {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
}

.img-preview {
  cursor: pointer;
  display: inline-block;
}

.pointer {
  cursor: pointer;
}

</style>
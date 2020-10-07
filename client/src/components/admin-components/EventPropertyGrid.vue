<template>
  <loader id="grid-loader" v-if="loadingProperties" :circlesNum="5"/>
  <div v-else id="property-grid">
    <template v-for="property of properties">
      <div :key="property.name">{{ property.name }}</div>
      <template v-if="property.editing">
        <textarea
          :key="property.name + '-input'"
          v-model="property.value"
          :disabled="property.saving"
          rows="5"
        ></textarea>

        <div :key="property.name + 'btns'" class="btn-container">
          <loader v-if="property.saving" :circlesNum="1" />
          <img v-else class="icon" src="../../assets/save.png" alt="Save"
            @click="saveProperty(property)" title="Save">
          <loader v-if="property.saving" :circlesNum="1" />
          <img v-else class="icon" src="../../assets/cancel.png" alt="Cancel"
            @click="cancelEditProperty(property)" title="Cancel">
        </div>
      </template>
      <template v-else>
        <div :key="property.name + '-val'">{{ property.value }}</div>
        <div :key="property.name + 'btns'" class="btn-container">
            <img class="icon" src="../../assets/edit.png" alt="Edit"
              @click="editProperty(property)" title="Edit"/>
        </div>
      </template>


    </template>
  </div>
</template>

<script>
import Loader from '../sub-components/Loader.vue';
import { serverURL } from "../../constants";
export default {
  name: "PropertyGrid",
  components: { Loader },
  data() {
    return {
      loadingProperties: false,
      properties: [],
    }
  },
  created() {
    this.loadingProperties = true;
    this.$http.get(serverURL + "/event_properties.php", {credentials: 'include'})
    .then(json => 
        json.forEach(dbProperty => 
          this.properties.push(this.createProperty(dbProperty))
    ))
    .catch(err => this.$emit("warn", err))
    .finally(() => this.loadingProperties = false);
  },

  methods: {
    createProperty(dbProperty) {
      return {
        name: dbProperty.name,
        value: dbProperty.value,
        editing: false,
        saving: false,
      }
    },
    editProperty(property) {
      property.editing = true;
      this.$set(property, "origValue", property.value);
    },
    cancelEditProperty(property) {
      if (property.saving) return;
      property.editing = false;
      property.value = property.origValue;
    },

    saveProperty(property) {
      if (property.saving) return;
      if (!property.editing) return;
      if (!property.name) {
        this.$emit("warn", "Name missing.");
        return;
      }
      if (property.name == property.origName) {
        property.editing = false;
        return;
      }

      property.saving = true;
      this.$http.put(serverURL + "/event_properties.php", {credentials: 'include'}, {
        name: property.name,
        value: property.value
      })
      .then(dbProperty => {
        property.name = dbProperty.name;
        property.value = dbProperty.value;
        property.editing = false;
      })
      .catch(err => {
        this.$emit("warn", err);
      })
      .finally(() => property.saving = false);
    },
  }

}
</script>

<style scoped>
#property-grid {
  display: grid;
  grid-template-columns: max-content auto max-content;
  align-items: center;
  justify-items: center;
  margin: 15px;
  margin-left: 75px;
}

.btn-container {
  display: flex;
  flex-flow: row wrap;
  justify-content: flex-start;
  align-items: center;
}

.icon {
  width: 24px;
  cursor: pointer;
}

.icon + .icon {
  margin-left: 4px;
}

textarea {
  max-width: 1000px;
}
</style>
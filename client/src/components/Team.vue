<template>
  <div>
    <page-header :msg="team.name">Team Members</page-header>
    <loader id="page-loader" v-if="loading" :circlesNum="5"/>
    <div v-else class="grid">
      <div class="grid-heading">Name</div>
      <div class="grid-heading">Email</div>
      <div class="grid-heading"></div>
      <template v-for="(person, index) in people">
        <template v-if="person.editing">
          <input type="text" :key="index + 'name'" v-model="person.name" :disabled="person.saving" maxlength="50" />
          <input type="text" :key="index + 'email'" v-model="person.email" :disabled="person.saving" maxlength="50" />
          <div :key="index + 'btns'" class="btn-container">
            <loader v-if="person.saving" :circlesNum="1" />
            <img v-else class="icon" src="../assets/save.png" alt="Save" @click="save(person)" title="Save">
            <loader v-if="person.saving" :circlesNum="1" />
            <img v-else class="icon" src="../assets/cancel.png" alt="Cancel" @click="cancel(person)" title="Cancel">
          </div>
        </template>
        <template v-else>
          <div :key="index + 'name'">{{ person.name }}</div>
          <div :key="index + 'email'">{{ person.email }}</div>
          <div :key="index + 'btns'" class="btn-container">
            <loader v-if="person.deleting" :circlesNum="1"/>
            <img v-else class="icon" src="../assets/edit.png" alt="Edit" @click="edit(person)" title="Edit"/>
            <loader v-if="person.deleting" :circlesNum="1"/>
            <img v-else class="icon" src="../assets/delete.png" alt="Delete" @click="remove(person)" title="Remove"/>
          </div>
        </template>
      </template>
      <input type="text" placeholder="New Name" v-model="newPerson.name" :disabled="adding" maxlength="50" />
      <input type="text" placeholder="New Email" v-model="newPerson.email" :disabled="adding" maxlength="50" />
      <div class="btn-container">
        <loader v-if="adding" :circlesNum="1"/>
        <img v-else class="icon" src="../assets/plus.png" alt="Add" title="Add New" @click="addPerson()">
      </div>

    </div>
  </div>
</template>

<script>
import PageHeader from "./sub-components/PageHeader";
import Loader from "./sub-components/Loader";
import { emailRE, serverURL } from "../constants";
export default {
  name: "Team",
  components: { PageHeader, Loader },
  props: ["team"],
  data() {
    return {
      people: [],
      loading: false,
      adding: false,
      newPerson: { name: "", email: "" }
    }
  },

  mounted() {
    if (!this.$teamCookieExists) {
      this.$emit("warn", "You need to login first.")
      this.$router.push({ name: 'Home'});
      return;
    }

    this.loading = true;
    this.$http.get(serverURL+"/team.php", {credentials: "include"})
    .then(json => {
      json.forEach(person => {
        this.people.push(this.createPerson(person));
      });
    })
    .catch(text => this.$emit("warn", text))
    .finally(() => this.loading = false);
  },
  methods: {
    createPerson(dbPerson) {
      return {
        id: dbPerson.person_id,
        name: dbPerson.person_name,
        email: dbPerson.email,
        editing: false,
        saving: false,
        deleting: false,
      }
    },

    addPerson() {
      this.newPerson.name = this.newPerson.name.trim();
      this.newPerson.email = this.newPerson.email.trim();
      if (!this.newPerson.name) {
        this.$emit("warn", "New person name missing.");
        return;
      }
      if (!this.newPerson.email) {
        this.$emit("warn", "New person email missing.");
        return;
      }
      else if (!emailRE.test(this.newPerson.email.toLowerCase())) {
        this.$emit("warn", "New person email is invalid.");
        return;
      }

      this.adding = true;
      this.$http.post(serverURL + "/team.php", {credentials: "include"}, this.newPerson)
      .then(person => {
        this.newPerson.email = "";
        this.newPerson.name = "";
        this.people.push(this.createPerson(person));
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => this.adding = false);
    },

    remove(person) {
      if (!confirm(`Remove ${person.name} from ${this.team.name}?`)) return;
      const id = person.id;
      person.deleting = true;
      this.$http.delete(serverURL + "/team.php?id=" + id, {credentials: "include"})
      .then(text => {
        this.$emit("toast", text);
        this.people = this.people.filter(x => x.id != id);
      })
      .catch(err => this.$emit("warn", err))
      .finally(() => person.deleting = false)
    },
    edit(person) {
      person.editing = true;
      this.$set(person, "origName", person.name);
      this.$set(person, "origEmail", person.email);
    },
    cancel(person) {
      if (person.saving) return;
      person.editing = false;
      person.name = person.origName;
      person.email = person.origEmail;
    },
    save(person) {
      const nameChange = person.name != person.origName;
      const emailChange = person.email != person.origEmail;
      if (!nameChange && !emailChange) {
        person.editing = false;
        return;
      }
      if (!person.name) {
        this.$emit("warn", "Name missing.");
        return;
      }
      if (!person.email) {
        this.$emit("warn", "Email missing.");
        return;
      }
      else if (!emailRE.test(person.email.toLowerCase())) {
        this.$emit("warn", "Email is invalid.");
        return;
      }

      person.saving = true;
      this.$http.put(serverURL + "/team.php", {credentials: "include"}, {
        id: person.id,
        name: nameChange ? person.name : "",
        email: emailChange ? person.email: ""
      })
      .then(dbPerson => {
        if (nameChange) person.name = dbPerson.person_name;
        if (emailChange) person.email = dbPerson.email;
        person.editing = false;
      })
      .catch(err => {
        this.$emit("warn", err);
      })
      .finally(() => person.saving = false)
    },
  }

}
</script>
<style scoped>
.grid {
  display: grid;
  grid-template-columns: auto auto max-content;
  gap: 10px 5px;
  margin: 15px 20px;
  align-items: center;
}

.grid-heading {
  font-weight: bold;
}

.grid > div {
  max-width: 350px;
  overflow-wrap: break-word;
}

.icon {
  width: 24px;
  cursor: pointer;
}

.icon + .icon {
  margin-left: 4px;
}

#page-loader {
  margin: 20px auto;
}

.btn-container {
  display: flex;
  flex-flow: row wrap;
  justify-content: flex-start;
  align-items: center;
}

@media screen and (max-width: 500px) {
  .grid {
    grid-template-columns: auto;
  }
  .grid-heading {
    display: none;
  }
}


</style>

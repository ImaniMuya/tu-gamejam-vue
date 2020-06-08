<template>
  <div>
    <page-header :msg="team">Team Members</page-header>
    <div class="grid">
      <div class="grid-heading">Name</div>
      <div class="grid-heading">Email</div>
      <div class="grid-heading"></div>
      <div class="grid-heading"></div>
      <template v-for="(person, index) in people">
        <!-- TODO: loading spinner -->
        <template v-if="person.editing">
          <input type="text" :key="index + 'name'" v-model="person.name" />
          <input type="text" :key="index + 'email'" v-model="person.email" />
          <img :key="index + 'save'" class="icon" src="../assets/plus.png" alt="Save" @click="person.editing = false" title="Save">
          <img :key="index + 'cancel'" class="icon" src="../assets/plus.png" alt="Cancel" @click="person.editing = false" title="Cancel">
        </template>
        <template v-else>
          <div :key="index + 'name'">{{ person.name }}</div>
          <div :key="index + 'email'">{{ person.email }}</div>
          <img :key="index + 'edit'" class="icon" src="../assets/edit.png" alt="Edit" @click="person.editing = true" title="Edit"/>
          <loader v-if="person.deleting" :key="index + 'delete'" :circlesNum="1"/>
          <img v-else :key="index + 'delete'" class="icon" src="../assets/delete.png" alt="Delete" @click="deleteClicked(person)" title="Delete"/>
        </template>
      </template>
      <div><input type="text" placeholder="New Name" v-model="newPerson.name"></div>
      <div><input type="text" placeholder="New Email" v-model="newPerson.email"></div>
      <loader v-if="adding" :circlesNum="1"/>
      <img v-else class="icon" src="../assets/plus.png" alt="Add" title="Add New" @click="addClicked()">

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
  props: { team: String },
  data() {
    return {
      people: [],
      adding: false,
      newPerson: { name: "", email: "" }
    }
  },

  created() { //TODO: should this be mounted?
    if (!this.$teamCookieExists) {
      this.$emit("toast", "You need to login first.")
      this.$router.push({ name: 'Home'});
      return;
    }

    fetch(serverURL+"/team.php", {credentials: "include"})
    .then(response => new Promise((resolve, reject) => {
        if (response.status == 200) {
          response.json().then(x => resolve(x));
          return;
        }

        response.text().then(x => reject(x));
        if (response.status == 403) {
          setTimeout(() => this.$router.push({ name: 'Home'}), 0);
        }
    }))
    .then(json => {
      json.forEach(person => {
        //restructure object...
        this.people.push({
          id: person.person_id,
          name: person.person_name,
          email: person.email,
          editing: false,
          deleting: false,
        })
      });
    })
    .catch(text => this.$emit("toast", text)); //TODO: error toast

    //api call to get team members (use cookie) catch bad cookie -> redirect home

    window.vm = this; //TODO: remove me
  },
  methods: {
    addClicked() {
      this.newPerson.name = this.newPerson.name.trim();
      this.newPerson.email = this.newPerson.email.trim();
      if (!this.newPerson.name) {
        this.$emit("toast", "New person name missing."); //TODO: error toast
        return;
      }
      if (!this.newPerson.email) {
        this.$emit("toast", "New person email missing."); //TODO: error toast
        return;
      }
      else if (!emailRE.test(this.newPerson.email.toLowerCase())) {
        this.$emit("toast", "New person email is invalid."); //TODO: error toast
        return;
      }

      this.adding = true;
      fetch(serverURL + "/team.php", {
        credentials: "include",
        method: "POST",
        body: JSON.stringify(this.newPerson)
      })
      .then(response => new Promise((resolve, reject) => {
          if (response.status == 200) {
            response.json().then(x => resolve(x));
            return;
          }

          response.text().then(x => reject(x));
          if (response.status == 403) {
            setTimeout(() => this.$router.push({ name: 'Home'}), 0);
          }
      }))
      .then(person => {
        this.newPerson.email = "";
        this.newPerson.name = "";
        this.people.push({
          id: person.person_id,
          name: person.person_name,
          email: person.email,
          editing: false,
          deleting: false,
        })
      })
      .catch(err => this.$emit("toast", err)) //TODO: error toast
      .finally(() => this.adding = false);
    },

    deleteClicked(person) {
      const id = person.id;
      person.deleting = true;
      fetch(serverURL + "/team.php?id=" + id, {
        credentials: "include",
        method: "DELETE",
      })
      .then(response => new Promise((resolve, reject) => {
        if (response.status == 200) {
          response.text().then(x => resolve(x));
          return;
        }

        response.text().then(x => reject(x));
        if (response.status == 403) {
          setTimeout(() => this.$router.push({ name: 'Home'}), 0);
        }
      }))
      .then(text => {
        this.$emit("toast", text);
        this.people = this.people.filter(x => x.id != id);
      })
      .catch(err => this.$emit("toast", err))  //TODO: error toast
      .finally(() => person.deleting = false)
    }
  }

}
</script>
<style scoped>
.grid {
  display: grid;
  grid-template-columns: auto auto min-content min-content;
  gap: 10px 10px;
  margin: 0 20px;
  align-items: center;
}
.grid-heading {
  font-weight: bold;
}

.icon {
  width: 24px;
  cursor: pointer;
}

</style>

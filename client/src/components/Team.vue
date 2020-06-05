<template>
  <div>
    <page-header :msg="teamName">Team Members</page-header>
    <div class="grid">
      <div class="grid-heading">Name</div>
      <div class="grid-heading">Email</div>
      <div class="grid-heading"></div>
      <template v-for="(person, index) in people">
        <!-- TODO: loading spinner -->
        <div :key="index + 'name'">{{ person.person_name }}</div>
        <div :key="index + 'email'">{{ person.email }}</div>
        <button :key="index + 'btn'">Edit</button>
      </template>
      <div><input type="text" placeholder="New Name"></div>
      <div><input type="text" placeholder="New Email"></div>
      <button>Add</button>
    </div>
  </div>
</template>

<script>
import PageHeader from "./sub-components/PageHeader";
export default {
  name: "Team",
  components: { PageHeader },
  data() {
    return {
      teamName: "My Team",
      people: []
    }
  },

  created() { //TODO: should this be mounted?
    if (!this.$teamCookieExists) {
      this.$emit("toast", "You need to login first.")
      this.$router.push({ name: 'Home'});
      return;
    }

    fetch("http://localhost:4321/team.php", {credentials: "include"}).then(response => 
      new Promise((resolve, reject) => {
        if (response.status == 200) {
          response.json().then(x => resolve(x));
          return;
        }

        response.text().then(x => reject(x));
        if (response.status == 403) {
          setTimeout(() => this.$router.push({ name: 'Home'}), 0);
        }
      })
    )
    .then(json => this.people = json)
    .catch(text => this.$emit("toast", text)); //TODO: error toast

    //api call to get team members (use cookie) catch bad cookie -> redirect home
  }
}
</script>
<style scoped>
.grid {
  display: grid;
  grid-template-columns: auto auto auto;
  gap: 10px 20px;
  margin: 0 20px;
  align-items: center;
}
.grid-heading {
  font-weight: bold;
}
</style>

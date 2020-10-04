<template>
  <loader id="grid-loader" v-if="loadingTeams" :circlesNum="5"/>
  <div v-else id="teamGrid">
    <template v-for="(team, id) in teams">
      <div :key="id">
        {{ id }}
      </div>
      <div :key="id+'login'">
          <a :key="id+'-login'" :href="loginLink(id, team)">login as {{ team.name }}</a>
      </div>
      <div :key="id+'-name'">{{ team.name }}</div>
      <ul :key="id+'-members'" class="members">
        <li
          class="member"
          v-for="(member, idx) in team.members"
          :key="idx"
        >{{ member }}</li>
      </ul>
    </template>
  </div>
</template>

<script>
import Loader from '../sub-components/Loader.vue';

export default {
  name: "TeamGrid",
  components: { Loader },
  props: ['teams'],

  data() {
    return {
      loadingTeams: false,
    }
  },

  methods: {
    loginLink(id, team) {
      return `/login?t=${id}&s=${team.secret}`;
    },
  }
}


</script>

<style scoped>
#teamGrid {
  display: grid;
  grid-template-columns: auto auto auto auto;
  align-content: center;
  justify-content: center;;
  gap: 10px;
  margin: 20px;
}

ul.members {
  margin: 0;
  padding: 0;
}

li.member {
  display: inline;
  list-style-type: none;
}

li.member + li.member::before {
  content: ", ";
}
</style>
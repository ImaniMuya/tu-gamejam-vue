<template>
  <div>
    <h2 class="winner-heading">GAME JAM WINNERS!</h2>
    <loader id="grid-loader" v-if="loadingAwards" :circlesNum="5"/>
    <div v-else>
       <template v-for="(award, index) in awards">
         <div :key="index + 'winners'" id="winner-container">
          <strong>{{ award.name }}</strong>
          <div>Team: <a :href="'#'+award.team.name">{{ award.team.name }}</a></div>
          <ul :key="index+'-members'" class="members">
            Congratulations to: 
            <li
              class="member"
              v-for="(member, index) in award.team.members"
              :key="index"
            >{{ member }}</li>
          </ul> 
         <br/>
         </div>
       </template>
    </div>
  </div>
</template>

<script>
import Loader from './Loader.vue';
import { serverURL } from "../../constants";

export default {
  name: "Winners",
  components: { Loader },
  props: ["teams"],
  data () {
    return {
      loadingAwards: true,
      awards: []
    }
  },
  created() {
    this.loadingAwards = true;
    this.$http.get(serverURL + "/winners.php")
    .then(json => json.forEach(x => 
      this.awards.push(this.createAward(x))
    ))
    .catch(err => this.$emit("warn", err))
    .finally(() => this.loadingAwards = false);
  },
  methods: {
    getTeamById(id) {
      return this.teams.find(x => x.id == id);
    },
    createAward(dbAward) {
      return {
        id: dbAward.award_id,
        name: dbAward.award,
        team: this.getTeamById(dbAward.team_id)
      };
    }
  }
}
</script>

<style>
#winner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  /* align-items: start; */
}

#winner-container > ul.members {
  margin: 0;
  padding: 0;
}

#winner-container > ul > li.member {
  display: inline;
  list-style-type: none;
}

#winner-container > ul > li.member + li.member::before {
  content: ", ";
}

h2.winner-heading {
  text-align: center;
}
</style>
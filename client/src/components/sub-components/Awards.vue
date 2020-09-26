<template>
  <div v-if="awards.length">
    <!-- TODO hide awards exist but no awards assigned -->
    <h2 class="winner-heading">GAME JAM WINNERS!</h2>
    <div>
       <template v-for="(award, index) in awards">
         <div :key="index + 'winners'" class="award-container">
          <strong>{{ award.award }}</strong>
          <div v-if="getTeam(award)">
            Team:
            <a :href="'#'+award.team_id+'-'+getTeam(award).name">{{ getTeam(award).name }}</a>
            <ul class="members">
              Congratulations to: 
              <li
                class="member"
                v-for="(member, index) in getTeam(award).members"
                :key="index"
              >{{ member }}</li>
            </ul> 
          </div>
         </div>
       </template>
    </div>
  </div>
</template>

<script>

export default {
  name: "Awards",
  props: ["teams", "awards"],
  computed: {
    teamDict() {
      let d = {}
      this.teams.forEach(team => {
        d[team.id] = team;
      });
      return d;
    }
  },
  methods: {
    getTeam(award) {
      return this.teamDict[award.team_id];
    }
  }
}
</script>

<style scoped>
.award-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  /* align-items: start; */
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

.winner-heading {
  text-align: center;
}
</style>
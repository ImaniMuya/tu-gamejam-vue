<template>
  <div id="navcol" :class="{tucked}">
  <div id="navbtn" @click="tucked = !tucked">
    <div id="navicon" :class="{tucked}">
        <div></div>
      </div>
    </div>
  <div class="navlinkcont">
    <span class="navlink" v-for="(name, index) in links" :key="index" @click="tucked = true">
      <router-link :to="{ name }">{{name}}</router-link>
    </span>
    <span class="navlink">
      <a @click="pastEventsTucked = !pastEventsTucked">Past Events</a>
    </span>
    <div class="dropdown" :class="{'tucked': pastEventsTucked}">
      <div class="navlink" v-for="(name, index) in pastEvents" :key="'past'+index" @click="tucked = true">
        <router-link :to="{ name: 'Past', params: { eventName: name } }">{{name}}</router-link>
      </div>
    </div>


    <template v-if="team.id">
      <span class="navlink" @click="tucked = true">
        <router-link :to="{ name: 'Team' }" class="breakable">
          <span>{{ team.name }}</span>
        </router-link>
      </span>
      <span class="navlink" @click="tucked = true">
        <router-link :to="{ name: 'Vote' }">Vote</router-link>
      </span>
      <span class="navlink" @click="tucked = true">
        <router-link :to="{ name: 'Submission' }">Submission</router-link>
      </span>
    </template>
  </div>
</div>

</template>

<script>
export default {
  name: 'NavBar',
  props: [ "team", "pastEvents" ],
  data() {
    return {
      tucked: true,
      links: ["Home", "Rules", "Resources"], //TODO: get links from router?
      pastEventsTucked: true
    }
  },
}

</script>

<style scoped>

#navcol{
  position: fixed;
  height: 100%;
  width: 300px;
  left: 0px; 
  background-color: var(--primcolor);
  transition: all .3s ease-in-out;
  z-index: 99;
}

#navcol.tucked{
  left: -225px;
}

.navlinkcont {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  height: 100%;
  width: 250px;
  position: relative;
  top: 10px;
}

span.navlink {
  font-size: 25px;
  margin: 7px 0;
}

.breakable {
  display: inline-flex;
  flex-flow: row nowrap;
  justify-content: center;
  align-items: center;
}

.breakable > span{
  display: inline-block;
  max-width: 165px;
  overflow-wrap: break-word;
}

span.navlink:hover{
  cursor: pointer;
  color: var(--seccolor);
}

.navlink, .navlink a {
  color: var(--tercolor);
}

#navbtn {
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  width: 100px;
  height: 100px;
  border-radius: 100%;
  background-color: var(--seccolor);
  border: solid;
  border-color: var(--primcolor);
  position: relative;
  top: 30px;
  left: 250px;  
}

#navicon {
  width: 40px;
}

#navicon::after, 
#navicon::before, 
#navicon div {
  background-color: var(--tercolor);
  border-radius: 3px;
  content: '';
  display: block;
  height: 5px;
  margin: 7px 0;
  transition: all .2s ease-in-out;
  position: relative; 
}

#navicon::before {
  transform: translateY(12px) rotate(135deg);
}

#navicon::after {
  transform: translateY(-12px) rotate(-135deg);
}

#navicon div {
  transform: scale(0);
}

#navicon.tucked::before,
#navicon.tucked::after,
#navicon.tucked div
{
  transform: none;
}

.dropdown.tucked {
  max-height: 0;
  opacity: 0;
  visibility: hidden;
  transition: all .5s, visibility 0s .5s;
}

.dropdown {
  opacity: 1;
  max-height: 200px; /* may need to change me eventually! */
  transition: all .5s;
  text-align: center;
}


</style>
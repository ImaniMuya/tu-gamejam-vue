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

    <template v-if="team">
      <span class="navlink" @click="tucked = true">
        <router-link :to="{ name: 'Team' }">{{ team }}</router-link>
      </span>
      <span v-if="team" class="navlink" @click="tucked = true">
        <router-link :to="{ name: 'Vote' }">Vote</router-link>
      </span>
      <span v-if="team" class="navlink" @click="tucked = true">
        <router-link :to="{ name: 'Submission' }">Submission</router-link>
      </span>
    </template>

    <!-- TODO: accordion for events -->
    <!-- <div id=navdropdown class="navlink"><a>Past Events</a> -->
      <!-- <div class="navdropdowncontent"><a href="/~gamejamdev/events/event_s2019.php">S2019</a></div>
      <div class="navdropdowncontent"><a>F2018</a></div>
      <div class="navdropdowncontent"><a>S2018</a></div>
      <div class="navdropdowncontent"><a>F2017</a></div>
      <div class="navdropdowncontent"><a>S2017</a></div>
      <div class="navdropdowncontent"><a>F2016</a></div> -->
    <!-- </div> -->
  </div>
</div>

</template>

<script>
export default {
  name: 'NavBar',
  props: { team: String },
  data() {
    return {
      tucked: true,
      links: ["Home", "Rules", "Resources"] //TODO: get links from router?
    }
  }
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

span.navlink:hover{
  cursor: pointer;
  color: var(--seccolor);
}

span.navlink:hover .navdropdowncontent{
  color: var(--tercolor);
}
span.navlink a{
  color: var(--tercolor);
}
/* #navdropdown {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  position: relative;
  padding-bottom: 50px;
  transition: all 5s ease-in-out;
}

.navdropdowncontent{
  display: none;
  position: relative;
  padding-top: 2%;
  color: var(--tercolor);
  z-index: 1;
} 

#navdropdown:hover .navdropdowncontent {
  display: inline-block;
}

.navdropdowncontent:hover{
  color: var(--seccolor);
} */

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

</style>
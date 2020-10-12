<template>
  <div :class="{'timeline-container': true, hidden }">
    <div class="line">
      <div class="flag start">🚩</div>
      <div class="flag finish">🏁</div>
      <div class="monkey" :style="monkeyStyle">🐵</div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Timeline",
  data() {
    return {
      hidden: true,
      monkeyStyle: {
        '--percentage': "0%"
      }
    };
  },
  mounted() {
    setTimeout(() => this.hidden = false, 0);
  },
};
</script>

<style scoped>
.timeline-container {
  position: relative;
  top: 10px;
  width: 100%;
  transition: top 1s cubic-bezier(.47,.01,.4,1.42) 1s;
  font-size: 30px;
  overflow: none;
  display: flex;
  justify-content: center;
}
.hidden {
  top: -60px;
}

.line {
  position: absolute;
  width: calc(100% - 100px);
  top: 35px;
  background-color: var(--primcolor);
  height: 8px;
  border-radius: 10px;
  overflow: none;
}


.hidden .monkey {
  left: 0;
}
.monkey {
  position: absolute;
  top: -30px;
  left: var(--percentage);
  transform: translateX(-50%);

  transition: left 5s cubic-bezier(0,0,.17,1) 2.5s;
}

.hidden .flag {
  top: -100px; 
}

.flag {
  position: absolute;
  top: -35px;
  transition: top 1s ease 2s;
}

.start {
  left: 50%;
}
.finish {
  left: 100%;
  transform: translateX(-25%);
}
</style>
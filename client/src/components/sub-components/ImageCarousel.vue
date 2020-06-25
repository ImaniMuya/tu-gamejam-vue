<template>
  <div class="carousel-outer">
    <div id="displayBox" @click="$emit('popup', chosenSrc)" title="Enhance">
      <img class="chosenImg"
        alt="image"
        :src="swap ? chosenSrc : fadeSrc" 
        :class="{hidden: !swap}"
      />
      <img class="chosenImg"
        alt="image"
        :src="swap ? fadeSrc : chosenSrc"
        :class="{hidden: swap}"
      />
    </div>
    <div id="ribbon" v-if="imgSrcs.length > 1">
      <div v-for="(src, index) in imgSrcs" :key="index"
        title="Show"
        :class="{selected: index == chosenIdx}"
        @click="index != chosenIdx ? swapImg(index) : false"
      >
        <img :src="src" alt="image" />
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "ImageCarousel",
  props: ["imgSrcs"],
  data() {
    return {
      chosenIdx: 0,
      fadeIdx: 0,
      modalTucked: true,
      swap: false
    }
  },
  computed: {
    chosenSrc() {
      return this.imgSrcs[this.chosenIdx];
    },
    fadeSrc() {
      return this.imgSrcs[this.fadeIdx]
    }
  },
  methods: {
    swapImg(index) {
      this.fadeIdx = this.chosenIdx;
      this.chosenIdx = index;
      this.swap = !this.swap;
    }
  }
}
</script>
<style scoped>
.carousel-outer {
  display: flex;
  flex-flow: row nowrap;
  padding: 20px;
  align-items: center;
  border-radius: 5px;
  border: 1px solid var(--primcolor);
  box-shadow: 4px 4px 10px;
  margin: 5px;
}

#displayBox {
  width: 300px;
  height: 300px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.chosenImg {
  position: absolute;
  max-width: 300px;
  max-height: 300px;
  transition: opacity .5s, visibility 0s;
  opacity: 1;
  visibility: visible;
}

.chosenImg.hidden {
  opacity: 0;
  visibility: hidden;
  transition: opacity .5s, visibility 0s .5s;
}

#ribbon {
  display: flex;
  flex-flow: column;
  justify-content: center;
  align-items: center;
  margin-left: 5px;
}

#ribbon > div {
  width: 40px;
  height: 40px;
  padding: 2px;
  margin: 2px 0;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.selected {
  border: 1px solid var(--seccolor);
}

#ribbon img {
  max-width: 100%;
  max-height: 100%;
}

@media screen and (max-width: 500px) {
  #displayBox {
    width: 200px;
    height: 200px;
  }
  .chosenImg {
    max-width: 200px;
    max-height: 200px;
  }

}

</style>
<template>
  <div class="center-container">
    <div class="gallery-outer">
      <svg viewBox="0 0 20 20" class="arrow-icon" @click="scroll(-2/3)">
        <path d="M20 0 L0 10 L20 20"/>
      </svg>
      <div class="gallery-inner" ref="galInner">
        <img class="gallery-img"
          v-for="(url, i) in urls"
          :key="i"
          :src="url"
          alt="pic"
          @click="$emit('popup', url)"
        />
      </div>
      <svg viewBox="0 0 20 20" class="arrow-icon" @click="scroll(2/3)">
        <path d="M0 0 L20 10 L0 20"/>
      </svg>
    </div>
  </div>
</template>

<script>
export default {
  name: "Gallery",
  props: [ "urls" ],

  mounted() {window.vm = this;},
  data() {
    return {
      loadingPhotos: false,
    }
  },
  methods: {
    scroll(amt) {
      const galInner = this.$refs.galInner;
      galInner.scrollBy(amt * galInner.clientWidth, 0)

      // const focusImg = galInner.children.item(this.scrollIdx);
      // focusImg.scrollIntoView();
    }
  }

}
</script>

<style scoped>
.center-container {
  width: 100%;
  display: flex;
  flex-flow: column;
  align-items: center;
}
.gallery-outer {
  display: flex;
  flex-flow: row;
  align-items: stretch;
  justify-content: space-between;
  max-width: 90%;
  box-sizing: border-box;
  margin: 10px auto;
  border-radius: 15px;
  border: 1px solid var(--primcolor);
  box-shadow: 4px 4px 10px;
  /* background-color: #BBB; */

}

.arrow-icon {
  fill: none;
  stroke: black;
  stroke-width: 4px;
  width: 1rem;
  padding: 0 10px;
  border-radius: 15px;

  cursor: pointer;
}
.arrow-icon:hover {
  background-color: #DDD;
}

.gallery-inner {
  position: relative;
  height: 640px;
  width: 1000px;
  display: flex;
  flex-flow: column wrap;
  justify-content: space-evenly;
  align-content: flex-start;
  align-items: center;
  padding-left: 10px;
  padding-top: 10px;
  overflow: hidden;
  scroll-behavior: smooth;
}


.gallery-img {
  max-height: 200px;
  margin-right: 10px;
  margin-bottom: 10px;
  cursor: pointer;

  -webkit-touch-callout: none;
    -webkit-user-select: none;
     -khtml-user-select: none;
       -moz-user-select: none;
        -ms-user-select: none;
            user-select: none;                   
}

</style>
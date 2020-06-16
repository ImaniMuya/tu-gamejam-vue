<template>
  <div :class="{ show, warn }" @click="handleClick()">
    <img v-if="warn" src="../assets/warn.png" alt="Warning"/>
    {{ msg }}
  </div>
</template>

<script>
export default {
  name: "Toaster",
  props: { warn: Boolean },
  data() {
    return {
      msg: "Default Snackbar Message!", 
      show: false, //TODO: different style for error/warn toasts
      hideTimeout: null
    }
  },
  methods: {
    handleClick() {
      if (this.show) {
        clearTimeout(this.hideTimeout);
        this.show = false;
      }
    },

    toast(msg) {
      this.msg = msg;
      this.show = true;
      if (this.hideTimeout != null) clearTimeout(this.hideTimeout);
      this.hideTimeout = setTimeout(() => {
        this.show = false;
      }, 10000);
    }
  }
}
</script>

<style scoped>
div {
  visibility: hidden;
  min-width: 250px;
  margin-left: -125px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 100;
  left: 50%;
  font-size: 17px;

  bottom: 0px;
  opacity: 0;
  transition: all 0.5s;

  display: flex;
  justify-content: center;
  align-items: center;
}

div.show {
  visibility: visible;
  cursor: pointer;
  bottom: 30px;
  opacity: 1;
}

div.warn {
  background-color: var(--seccolor);
}

img { 
  height: 1em;
  margin-right: .25em;
}
</style>
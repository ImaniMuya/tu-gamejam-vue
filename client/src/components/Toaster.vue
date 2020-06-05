<template>
  <div :class="{ show }" @click="handleClick()">{{ msg }}</div>
</template>

<script>
export default {
  name: "Toaster",
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
      console.log(this.hideTimeout);
      if (this.hideTimeout != null) clearTimeout(this.hideTimeout);
      this.hideTimeout = setTimeout(() => {
        this.show = false;
        console.log("hide")
      }, 3000);
    }
  }
}
</script>

<style scoped>
div {
  /* visibility: hidden; */
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

}

div.show {
  cursor: pointer;
  bottom: 30px;
  opacity: 1;
}
</style>
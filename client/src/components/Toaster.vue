<template>
  <div :class="{ show }">{{ msg }}</div>
</template>

<script>
export default {
  name: "Toaster",
  data() {
    return {
      msg: "Default Snackbar Message!", 
      show: false //TODO: different style for error/warn toasts
    }
  },
  methods: {
    toast(msg) {
      this.msg = msg;
      this.show = true;
      setTimeout(() => this.show = false, 3000);
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
  bottom: 30px;
  font-size: 17px;
}

div.show {
  visibility: visible;
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

@-webkit-keyframes fadein {
  from {bottom: 0; opacity: 0;} 
  to {bottom: 30px; opacity: 1;}
}

@keyframes fadein {
  from {bottom: 0; opacity: 0;}
  to {bottom: 30px; opacity: 1;}
}

@-webkit-keyframes fadeout {
  from {bottom: 30px; opacity: 1;} 
  to {bottom: 0; opacity: 0;}
}

@keyframes fadeout {
  from {bottom: 30px; opacity: 1;}
  to {bottom: 0; opacity: 0;}
}
</style>
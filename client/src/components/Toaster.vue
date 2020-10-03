<template>
  <transition name="fade" :duration="500">
    <div v-if="show" id="toast" :class="{ show, warn }" :style="toastStyleVars" @click="handleClick()">
      <img class="icon" v-if="warn" src="../assets/warn.png" alt="Warning"/>
      {{ msg }}
    </div>
  </transition>
</template>

<script>
export default {
  name: "Toaster",
  data() {
    return {
      msg: "Default Snackbar Message!", 
      show: false, //TODO: different style for error/warn toasts
      hideTimeout: null,
      duration: 0,
      warn: false
    }
  },
  computed: {
    toastStyleVars() {
      return {
        '--duration': this.duration > 0 ? this.duration + 'ms' : "0s"
      }
    }
  },
  methods: {
    handleClick() {
      if (this.show) {
        clearTimeout(this.hideTimeout);
        this.show = false;
      }
    },
    warnToast(msg, duration) {
      this.warn = true;
      this.showMsg(msg, duration);
      console.error("Toast Error: ", msg);
    },
    toast(msg, duration) {
      this.warn = false;
      this.showMsg(msg, duration);
    },
    showMsg(msg, duration) {
      this.msg = msg;
      this.duration = duration
      this.show = false;
      setTimeout(() => this.show = true, 0); // retrigger animation
      if (duration > 0) { //non-positive duration -> last until closed or replaced
        if (this.hideTimeout != null) clearTimeout(this.hideTimeout);
        this.hideTimeout = setTimeout(() => this.show = false, this.duration);
      }
    }
  }
}
</script>

<style scoped>
#toast {
  min-width: 250px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 100;
  right: 20px;
  font-size: 17px;

  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 4px 4px var(--quadcolor);
  cursor: pointer;

  opacity: 1;
  bottom: 20px;
  transition: all .5s;
}

.fade-enter, .fade-leave-to { /* hidden */
  opacity: 0;
  bottom: 0px;
}

#toast.warn {
  background-color: var(--seccolor);
}

#toast::after {
  content: '\A';
  position: absolute;
  background: white;
  top: 0; left: 0; 
  height: 4px;
  opacity: 50%;
  transition: width var(--duration) ease;
  width: 100%;
}
#toast.fade-enter::after {
  width: 0%;
}

#toast.leave-active::after {
  width: 100%;
}

.icon {
  height: 1em;
  margin-right: .25em;
}
</style>
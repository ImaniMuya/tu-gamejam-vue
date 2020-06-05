import Vue from 'vue'
import router from './router'
import App from './App.vue'

Vue.config.productionTip = false

Object.defineProperty(Vue.prototype, '$teamCookieExists', {
  get: function()  {return document.cookie.split(';').some(item => item.trim().startsWith("gjt=")); }
});

new Vue({
  router,
  render: h => h(App),
}).$mount('#app')

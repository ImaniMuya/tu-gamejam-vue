import Vue from 'vue'
import router from './router'
import App from './App.vue'

Vue.config.productionTip = false

Object.defineProperty(Vue.prototype, '$teamCookieExists', {
  get: function() { return document.cookie.split(';').some(item => item.trim().startsWith("gjt=")); }
});


function standardResponseHandler(goodStatus) {
  return response => {
    if (response.status == goodStatus) {
      if (response.headers.get("content-type") == "application/json") return response.json();
      else return response.text();
    }
    if (response.status == 403) {
      sessionStorage.removeItem("team");
      setTimeout(() => router.push({ name: 'Home'}), 0);
    }
    return response.text().then(x => { throw x });
  }
}

Object.defineProperty(Vue.prototype, '$http', {
  value: {
    get(url, settings) {
      return fetch(url, settings).then(standardResponseHandler(200));
    },
    post(url, settings, body) {
      if (body instanceof FormData || typeof body == "string") settings.body = body;
      else settings.body = JSON.stringify(body);
      settings.method = "POST";
      return fetch(url, settings).then(standardResponseHandler(201));
    },
    put(url, settings, body) {
      if (body instanceof FormData || typeof body == "string") settings.body = body;
      else settings.body = JSON.stringify(body);
      settings.method = "PUT";
      return fetch(url, settings).then(standardResponseHandler(200));
    },
    delete(url, settings) {
      settings.method = "DELETE";
      return fetch(url, settings).then(standardResponseHandler(200));
    },
  }
});

new Vue({
  router,
  render: h => h(App),
}).$mount("#app")

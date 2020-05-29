import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/components/Home'
import Rules from '@/components/Rules'
import Resources from '@/components/Resources'
import Register from '@/components/Register'

Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/rules',
      name: 'Rules',
      component: Rules
    },
    {
      path: '/resources',
      name: 'Resources',
      component: Resources
    },
    {
      path: '/register',
      name: 'Register',
      component: Register
    }
  ]
})

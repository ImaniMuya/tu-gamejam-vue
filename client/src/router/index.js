import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/components/Home'
import Rules from '@/components/Rules'
import Resources from '@/components/Resources'
import Register from '@/components/Register'
import Team from '@/components/Team'
import Vote from '@/components/Vote'
import Admin from '@/components/Admin'
import Login from '@/components/Login'

Vue.use(Router)
//TODO: check cookie in router.beforeEach?
export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/login',
      name: 'Login',
      component: Login
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
    },
    {
      path: '/team',
      name: 'Team',
      component: Team
    },
    {
      path: '/vote',
      name: 'Vote',
      component: Vote
    },
    {
      path: '/admin',
      name: 'Admin',
      component: Admin
    }
  ]
})

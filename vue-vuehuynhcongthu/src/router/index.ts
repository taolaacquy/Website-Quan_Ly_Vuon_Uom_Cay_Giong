import { createRouter, createWebHistory } from 'vue-router'
import Intro from '../views/Intro.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Home from '../views/Home.vue'
import Admin from '../views/Admin.vue'

const routes = [
  { path: '/', component: Intro },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/home', component: Home },
  { path: '/admin', component: Admin }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router

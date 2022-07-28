import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './index.css'
import App from './App.vue'
import { axiosIns } from './services/axios'
import { createRouter, createWebHistory } from 'vue-router'
import { routes } from './services/router'

const router = createRouter({
  history: createWebHistory(),
  routes,
})

createApp(App)
const app = createApp(App)
const pinia = createPinia()

app.config.globalProperties.$axios = axiosIns

app.use(pinia)
app.use(router)
app.mount('#app')

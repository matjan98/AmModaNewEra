import { defineRouter } from '#q-app/wrappers'
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from 'vue-router'
import routes from './routes'
import { checkAdminAuth } from '../composables/useAdminAuth.js'



export default defineRouter(function () {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory)

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    
    
    
    history: createHistory(process.env.VUE_ROUTER_BASE)
  })

  Router.beforeEach(async (to) => {
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth)
    const guestOnly = to.matched.some((record) => record.meta.guestOnly)

    if (!requiresAuth && !guestOnly) {
      return true
    }

    const isAuthenticated = await checkAdminAuth()

    if (requiresAuth && !isAuthenticated) {
      return { path: '/admin/login' }
    }

    if (guestOnly && isAuthenticated) {
      return { path: '/admin' }
    }

    return true
  })

  return Router
})

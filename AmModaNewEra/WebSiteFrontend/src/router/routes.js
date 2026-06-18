// Same component reference for both public routes so Vue Router reuses the
// IndexPage instance when switching between Info ('/') and Gallery ('/galeria'),
// keeping the slide transition and keep-alive intact.
const IndexPage = () => import('pages/IndexPage.vue')

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', name: 'home', component: IndexPage },
      { path: 'galeria', name: 'gallery', component: IndexPage },
    ]
  },

  {
    path: '/admin/login',
    component: () => import('layouts/AdminGuestLayout.vue'),
    meta: { guestOnly: true },
    children: [
      { path: '', component: () => import('pages/admin/AdminLoginPage.vue') },
    ],
  },
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', component: () => import('pages/admin/AdminDashboardPage.vue') },
    ],
  },

  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes

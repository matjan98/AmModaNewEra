<template>
  <q-layout view="lHh Lpr lFf" class="admin-layout">
    <q-header class="admin-layout__header" elevated>
      <q-toolbar class="admin-layout__toolbar">
        <q-toolbar-title class="admin-layout__title">
          Panel admina AM Moda
        </q-toolbar-title>
        <q-btn
          flat
          no-caps
          label="Wyloguj"
          class="admin-layout__logout-btn"
          @click="onLogout"
        />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { logoutAdmin } from '../composables/useAdminAuth.js'

const router = useRouter()

async function onLogout() {
  await logoutAdmin()
  await router.replace('/admin/login')
}
</script>

<style scoped>
.admin-layout {
  color: rgba(0, 0, 0, 0.87);
  background: #f7f8fb;
}

.admin-layout__header {
  background: #111118;
  color: #fff;
}

.admin-layout__toolbar {
  min-height: 56px;
}

.admin-layout__title {
  font-size: 1rem;
  font-weight: 600;
}

.admin-layout__logout-btn {
  color: rgba(255, 255, 255, 0.9);
}
</style>

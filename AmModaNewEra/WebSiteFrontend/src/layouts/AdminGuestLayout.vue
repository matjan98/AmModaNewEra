<template>
  <q-layout view="hHh lpR fFf" class="admin-guest-layout">
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { useHead } from '@unhead/vue'

useHead({ meta: [{ name: 'robots', content: 'noindex, nofollow' }] })
</script>

<style scoped>
.admin-guest-layout {
  color: rgba(0, 0, 0, 0.87);
  background: #f3f4f7;
}
</style>

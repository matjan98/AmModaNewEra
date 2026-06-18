<template>
  <q-page class="admin-login-page flex flex-center">
    <q-card class="admin-login-page__card">
      <q-card-section>
        <h1 class="admin-login-page__title">Logowanie</h1>
        <p class="admin-login-page__subtitle">Panel administracyjny AM Moda</p>
      </q-card-section>

      <q-card-section>
        <q-form class="admin-login-page__form" @submit.prevent="onSubmit">
          <q-input
            v-model="username"
            label="Login"
            outlined
            dense
            autocomplete="username"
            class="admin-login-page__field"
          />
          <q-input
            v-model="password"
            label="Hasło"
            type="password"
            outlined
            dense
            autocomplete="current-password"
            class="admin-login-page__field"
          />

          <q-banner
            v-if="errorMessage"
            dense
            rounded
            class="admin-login-page__error bg-negative text-white"
          >
            {{ errorMessage }}
          </q-banner>

          <q-btn
            type="submit"
            color="primary"
            label="Zaloguj"
            no-caps
            unelevated
            class="admin-login-page__submit"
            :loading="loading"
          />
        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { loginAdmin } from '../../composables/useAdminAuth.js'

const router = useRouter()

const username = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

async function onSubmit() {
  errorMessage.value = ''
  loading.value = true

  try {
    const result = await loginAdmin(username.value.trim(), password.value)
    if (!result.ok) {
      errorMessage.value = result.error ?? 'Logowanie nie powiodło się.'
      return
    }

    await router.replace('/admin')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.admin-login-page {
  min-height: 100vh;
  background: #f3f4f7;
}

.admin-login-page__card {
  width: min(100%, 420px);
  border-radius: 14px;
}

.admin-login-page__title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.admin-login-page__subtitle {
  margin: 8px 0 0;
  color: rgba(0, 0, 0, 0.6);
}

.admin-login-page__form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.admin-login-page__submit {
  margin-top: 8px;
}
</style>

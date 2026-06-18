import { ref } from 'vue'
import { apiGetJson, apiPostJson } from '../utils/apiJson.js'

const authenticated = ref(false)
const authChecked = ref(false)

export async function checkAdminAuth() {
  const res = await apiGetJson('api/auth.php', { credentials: 'include' })
  const isAuth = Boolean(res.ok && res.data?.authenticated)
  authenticated.value = isAuth
  authChecked.value = true
  return isAuth
}

export async function loginAdmin(username, password) {
  const res = await apiPostJson(
    'api/auth.php',
    { username, password },
    { credentials: 'include' },
  )

  if (!res.ok || res.data?.ok !== true) {
    authenticated.value = false
    authChecked.value = true
    return { ok: false, error: res.data?.error ?? 'Logowanie nie powiodło się.' }
  }

  authenticated.value = true
  authChecked.value = true
  return { ok: true }
}

export async function logoutAdmin() {
  await apiPostJson('api/auth.php', { action: 'logout' }, { credentials: 'include' })
  authenticated.value = false
  authChecked.value = true
}

export function useAdminAuth() {
  return {
    authenticated,
    authChecked,
    checkAdminAuth,
    loginAdmin,
    logoutAdmin,
  }
}

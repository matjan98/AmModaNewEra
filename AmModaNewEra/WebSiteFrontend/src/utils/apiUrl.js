const DEFAULT_API_SUBPATH = 'server'

export function getApiUrl(path) {
  const apiBase = import.meta.env.VITE_API_BASE ?? ''
  const base = String(apiBase).replace(/\/$/, '')
  if (base) return `${base}/${path}`
  return `${DEFAULT_API_SUBPATH}/${path}`
}


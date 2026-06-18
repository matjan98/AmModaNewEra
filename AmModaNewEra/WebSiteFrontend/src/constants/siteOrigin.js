/**
 * Public site origin (no trailing slash) used to build absolute SEO URLs
 * (canonical, Open Graph) at runtime via @unhead/vue.
 *
 * Mirrors the `siteOrigin` value baked into index.html at build time. It is
 * provided through Quasar's `build.env` (see quasar.config.js) so it stays the
 * same in dev, prerender and production, regardless of `window.location`
 * (important: during prerender `window.location` points at a local server).
 */
export const SITE_ORIGIN = String(process.env.SITE_ORIGIN || 'https://ammoda.pl').replace(/\/$/, '')

/**
 * Build an absolute URL on the public origin for a given path.
 * @param {string} path Route path starting with '/'.
 * @returns {string}
 */
export function absoluteUrl(path) {
  const normalized = path === '/' ? '/' : `/${String(path).replace(/^\//, '')}`
  return `${SITE_ORIGIN}${normalized}`
}

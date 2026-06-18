import { defineBoot } from '#q-app/wrappers'
import { createHead } from '@unhead/vue/client'

/**
 * Register @unhead/vue so components can manage the document <head>
 * (per-route title/description/canonical/Open Graph + JSON-LD) via `useHead`.
 */
export default defineBoot(({ app }) => {
  const head = createHead()
  app.use(head)
})

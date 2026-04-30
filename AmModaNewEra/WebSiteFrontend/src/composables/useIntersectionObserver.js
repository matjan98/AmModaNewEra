import { isRef, onBeforeUnmount, watch } from 'vue'

function resolveBoolean(maybeRefOrBool) {
  return isRef(maybeRefOrBool) ? Boolean(maybeRefOrBool.value) : Boolean(maybeRefOrBool)
}

function resolveTarget(maybeRefOrEl) {
  return isRef(maybeRefOrEl) ? maybeRefOrEl.value : maybeRefOrEl
}

/**
 * Shared IntersectionObserver lifecycle helper.
 *
 * Supports observing multiple elements via `observe()` and can optionally auto-observe a single
 * `target` element/ref.
 *
 * @param {(entry: IntersectionObserverEntry) => void} onEntry
 * @param {(IntersectionObserverInit & { enabled?: import('vue').Ref<boolean>|boolean })} [options]
 * @param {import('vue').Ref<Element|null>|Element|null} [target]
 */
export function useIntersectionObserver(onEntry, options = {}, target = null) {
  const enabled = options.enabled ?? true
  const observerOptions = { ...options }
  delete observerOptions.enabled

  /** @type {IntersectionObserver|null} */
  let observer = null
  /** @type {Set<Element>} */
  const observed = new Set()

  const start = () => {
    if (observer) return
    if (typeof window === 'undefined' || typeof IntersectionObserver === 'undefined') return
    observer = new IntersectionObserver((entries) => {
      for (const entry of entries) onEntry(entry)
    }, observerOptions)
  }

  const stop = () => {
    if (observer) {
      observer.disconnect()
      observer = null
    }
    observed.clear()
  }

  const observe = (elOrRef) => {
    if (!resolveBoolean(enabled)) return
    const el = resolveTarget(elOrRef)
    if (!el) return
    start()
    if (!observer) return
    if (observed.has(el)) return
    observed.add(el)
    observer.observe(el)
  }

  const unobserve = (elOrRef) => {
    const el = resolveTarget(elOrRef)
    if (!el || !observer) return
    if (!observed.has(el)) return
    observed.delete(el)
    observer.unobserve(el)
  }

  if (target) {
    watch(
      () => resolveTarget(target),
      (el, prevEl) => {
        if (prevEl) unobserve(prevEl)
        if (el) observe(el)
      },
      { immediate: true },
    )
  }

  if (isRef(enabled)) {
    watch(
      enabled,
      (next) => {
        if (!next) stop()
      },
      { immediate: true },
    )
  }

  onBeforeUnmount(() => stop())

  return { observe, unobserve, stop }
}


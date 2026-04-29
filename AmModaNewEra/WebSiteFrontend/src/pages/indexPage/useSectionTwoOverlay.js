import { ref } from 'vue'

export function useSectionTwoOverlay() {
  const activeSectionTwoName = ref(null)

  function toggleSectionTwoOverlay(name) {
    activeSectionTwoName.value = activeSectionTwoName.value === name ? null : name
  }

  function closeSectionTwoOverlay() {
    activeSectionTwoName.value = null
  }

  function onSectionTwoOverlayDocumentPointerDown(e) {
    if (!activeSectionTwoName.value) return
    const target = e.target
    if (!(target instanceof Element)) return
    const insideActive = target.closest('.index-page-product-categories__card--active')
    if (!insideActive) closeSectionTwoOverlay()
  }

  function onSectionTwoOverlayKeydown(e) {
    if (!activeSectionTwoName.value) return
    if (e.key === 'Escape') closeSectionTwoOverlay()
  }

  return {
    activeSectionTwoName,
    toggleSectionTwoOverlay,
    closeSectionTwoOverlay,
    onSectionTwoOverlayDocumentPointerDown,
    onSectionTwoOverlayKeydown,
  }
}


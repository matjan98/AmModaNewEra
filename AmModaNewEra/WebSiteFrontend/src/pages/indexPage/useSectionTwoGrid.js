import { computed, ref } from 'vue'

export function useSectionTwoGrid({ sectionTwoItems, breakpointPx }) {
  const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : breakpointPx)

  const sectionTwoRows = computed(() => {
    const cols = windowWidth.value >= breakpointPx ? 4 : 2
    const rows = []
    for (let i = 0; i < sectionTwoItems.length; i += cols) {
      rows.push(sectionTwoItems.slice(i, i + cols))
    }
    return rows
  })

  function updateSectionTwoWindowWidth() {
    if (typeof window === 'undefined') return
    windowWidth.value = window.innerWidth
  }

  return {
    windowWidth,
    sectionTwoRows,
    updateSectionTwoWindowWidth,
  }
}


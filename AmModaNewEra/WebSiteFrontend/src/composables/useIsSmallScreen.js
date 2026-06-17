import { SMALL_SCREEN_MEDIA } from '../constants/breakpoints.js'
import { useMediaQuery } from './useMediaQuery.js'

/**
 * Tracks whether the viewport is below the unified small-screen breakpoint
 * (compact layout < 1000 px, desktop >= 1000 px). Centralized so every consumer agrees
 * on the same threshold.
 */
export function useIsSmallScreen() {
  return useMediaQuery(SMALL_SCREEN_MEDIA)
}

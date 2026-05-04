import { SMALL_SCREEN_MEDIA } from '../constants/breakpoints.js'
import { useMediaQuery } from './useMediaQuery.js'

/**
 * Tracks whether the viewport is below the unified small-screen breakpoint
 * (mobile < 750 px, desktop >= 750 px). Centralized so every consumer agrees
 * on the same threshold.
 */
export function useIsSmallScreen() {
  return useMediaQuery(SMALL_SCREEN_MEDIA)
}

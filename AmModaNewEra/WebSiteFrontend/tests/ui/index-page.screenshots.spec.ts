import { expect, test, type Page } from '@playwright/test'

const viewports = [
  { name: 'mobile', width: 390, height: 844 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'desktop', width: 1280, height: 800 },
] as const

async function gotoIndexWithCleanTabState(page: Page) {
  await page.addInitScript(() => {
    try {
      localStorage.removeItem('index-page-active-tab')
    } catch {
      // ignore
    }
  })

  await page.goto('/', { waitUntil: 'domcontentloaded' })
  await page.waitForLoadState('networkidle')
}

async function openHoursDropdownIfPresent(page: Page) {
  await page.evaluate(() => window.scrollTo(0, 0))

  const iconBtn = page.getByRole('button', { name: 'Godziny otwarcia' })
  if (await iconBtn.count()) {
    await iconBtn.first().evaluate((el) => (el as HTMLButtonElement).click())
    return
  }

  const legacyBtn = page.locator('.main-layout__open-status')
  if (await legacyBtn.count()) {
    await legacyBtn.first().evaluate((el) => (el as HTMLButtonElement).click())
  }
}

for (const vp of viewports) {
  test(`index-page ${vp.name} - baseline`, async ({ page }) => {
    await page.setViewportSize({ width: vp.width, height: vp.height })
    await gotoIndexWithCleanTabState(page)

    await expect(page.locator('.index-page')).toBeVisible()
    await expect(page).toHaveScreenshot(`index-${vp.name}-info.png`, { fullPage: true })
  })

  test(`index-page ${vp.name} - switch to gallery`, async ({ page }) => {
    await page.setViewportSize({ width: vp.width, height: vp.height })
    await gotoIndexWithCleanTabState(page)

    const galleryNav = page.getByRole('button', { name: 'Galeria' })
    await expect(galleryNav.first()).toBeVisible()
    await galleryNav.first().click({ force: true })

    await expect(page.locator('.index-page-gallery-panel')).toBeVisible()
    await page.waitForLoadState('networkidle')

    // Gallery transition can change document height; viewport screenshot is more stable across devices.
    await expect(page).toHaveScreenshot(`index-${vp.name}-gallery.png`, { fullPage: false })
  })

  test(`index-page ${vp.name} - opening hours expanded`, async ({ page }) => {
    await page.setViewportSize({ width: vp.width, height: vp.height })
    await gotoIndexWithCleanTabState(page)

    await openHoursDropdownIfPresent(page)
    const visibleDropdown = page.locator('.main-layout__hours-dropdown:visible').first()
    await expect(visibleDropdown).toBeVisible()
    await page.waitForTimeout(150)

    await expect(page).toHaveScreenshot(`index-${vp.name}-hours.png`, { fullPage: true })
  })
}


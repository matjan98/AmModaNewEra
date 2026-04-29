import fs from 'node:fs/promises'
import path from 'node:path'
import process from 'node:process'

const projectRoot = process.cwd()

const sourceDir = path.resolve(projectRoot, '../Logo/Favicon')
const publicDir = path.resolve(projectRoot, 'public')
const publicIconsDir = path.resolve(publicDir, 'icons')

const copyPairs = [
  { from: 'favicon.ico', to: path.join(publicDir, 'favicon.ico') },
  { from: 'favicon-16x16.png', to: path.join(publicIconsDir, 'favicon-16x16.png') },
  { from: 'favicon-32x32.png', to: path.join(publicIconsDir, 'favicon-32x32.png') },
  { from: 'apple-touch-icon.png', to: path.join(publicIconsDir, 'apple-touch-icon.png') },
  { from: 'android-chrome-192x192.png', to: path.join(publicIconsDir, 'android-chrome-192x192.png') },
  { from: 'android-chrome-512x512.png', to: path.join(publicIconsDir, 'android-chrome-512x512.png') }
]

async function ensureDir(dirPath) {
  await fs.mkdir(dirPath, { recursive: true })
}

async function copyFile(fromRel, toAbs) {
  const fromAbs = path.join(sourceDir, fromRel)
  await fs.copyFile(fromAbs, toAbs)
}

function toIconsSrc(src) {
  
  
  if (typeof src !== 'string' || src.length === 0) return src
  if (src.startsWith('/icons/')) return src
  if (src.startsWith('/')) return `/icons${src}`
  return `icons/${src}`
}

async function syncManifest() {
  const fromAbs = path.join(sourceDir, 'site.webmanifest')
  const toAbs = path.join(publicDir, 'site.webmanifest')

  const raw = await fs.readFile(fromAbs, 'utf8')
  let manifest
  try {
    manifest = JSON.parse(raw)
  } catch {
    
    await fs.writeFile(toAbs, raw)
    return
  }

  if (Array.isArray(manifest.icons)) {
    manifest.icons = manifest.icons.map((icon) => ({
      ...icon,
      src: toIconsSrc(icon?.src)
    }))
  }

  await fs.writeFile(toAbs, JSON.stringify(manifest, null, 2) + '\n')
}

async function main() {
  await ensureDir(publicIconsDir)

  await Promise.all(copyPairs.map((p) => copyFile(p.from, p.to)))
  await syncManifest()

  process.stdout.write('favicon synced\n')
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})


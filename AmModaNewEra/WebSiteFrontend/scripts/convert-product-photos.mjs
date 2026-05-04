import fs from 'node:fs/promises'
import path from 'node:path'
import process from 'node:process'
import sharp from 'sharp'

const projectRoot = process.cwd()
const sourceDir = path.resolve(projectRoot, 'src/assets/gallery')

const SUPPORTED_EXTS = new Set(['.jpg', '.jpeg', '.png'])

async function listImages() {
  const entries = await fs.readdir(sourceDir, { withFileTypes: true })
  return entries
    .filter((entry) => entry.isFile() && SUPPORTED_EXTS.has(path.extname(entry.name).toLowerCase()))
    .map((entry) => path.join(sourceDir, entry.name))
}

async function convertOne(srcPath, { keepOriginal }) {
  const parsed = path.parse(srcPath)
  const destPath = path.join(parsed.dir, `${parsed.name}.webp`)

  await sharp(srcPath).webp({ quality: 82, effort: 5 }).toFile(destPath)

  if (!keepOriginal) {
    await fs.unlink(srcPath)
  }
}

async function main() {
  const args = new Set(process.argv.slice(2))
  const keepOriginal = args.has('--keep-original')

  const files = await listImages()
  if (files.length === 0) {
    process.stdout.write('no images to convert\n')
    return
  }

  process.stdout.write(`converting ${files.length} image(s) to webp...\n`)
  for (const file of files) {
    await convertOne(file, { keepOriginal })
    process.stdout.write(`  ${path.relative(projectRoot, file)} -> .webp\n`)
  }
  process.stdout.write('done\n')
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})

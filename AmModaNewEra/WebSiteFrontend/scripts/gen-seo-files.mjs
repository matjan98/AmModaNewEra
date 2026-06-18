import fs from 'node:fs/promises'
import path from 'node:path'
import process from 'node:process'

// Generate public/sitemap.xml and public/robots.txt from the public origin so
// the URLs stay correct across environments (prod / staging) without editing
// them by hand. Output is deterministic - it only changes when SITE_ORIGIN,
// the route list or LAST_MODIFIED below change (no per-build git churn).

const projectRoot = process.cwd()
const publicDir = path.resolve(projectRoot, 'public')

const SITE_ORIGIN = String(process.env.SITE_ORIGIN || 'https://ammoda.pl').replace(/\/$/, '')

// Bump when public content/routes meaningfully change.
const LAST_MODIFIED = '2026-06-18'

const routes = [
  { loc: '/', changefreq: 'weekly', priority: '1.0' },
  { loc: '/galeria', changefreq: 'weekly', priority: '0.8' },
]

function buildSitemap() {
  const urls = routes
    .map((r) => {
      const loc = r.loc === '/' ? `${SITE_ORIGIN}/` : `${SITE_ORIGIN}${r.loc}`
      return [
        '  <url>',
        `    <loc>${loc}</loc>`,
        `    <lastmod>${LAST_MODIFIED}</lastmod>`,
        `    <changefreq>${r.changefreq}</changefreq>`,
        `    <priority>${r.priority}</priority>`,
        '  </url>',
      ].join('\n')
    })
    .join('\n')

  return `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${urls}\n</urlset>\n`
}

function buildRobots() {
  return [
    'User-agent: *',
    'Allow: /',
    '',
    'Disallow: /admin',
    'Disallow: /api/',
    'Disallow: /server/',
    '',
    `Sitemap: ${SITE_ORIGIN}/sitemap.xml`,
    '',
  ].join('\n')
}

async function main() {
  await fs.mkdir(publicDir, { recursive: true })
  await fs.writeFile(path.join(publicDir, 'sitemap.xml'), buildSitemap())
  await fs.writeFile(path.join(publicDir, 'robots.txt'), buildRobots())
  process.stdout.write('seo files generated\n')
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})

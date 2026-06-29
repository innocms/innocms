/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *
 * InnoCMS Asset Build Script (Vite)
 *
 * Usage:
 *   npm run build                        Build all core modules
 *   npm run prod                         Alias for build
 *   TARGET=front npm run build           Build only front module
 *   TARGET=panel npm run build           Build only panel module
 *   THEME=shopan npm run prod            Build only theme
 *   TARGET=panel THEME=shopan npm run prod  Build panel + theme
 */

import { execSync } from 'child_process';
import fs from 'fs';

const theme = process.env.THEME || '';
const target = process.env.TARGET || '';
const startTime = Date.now();

const coreEntries = [
    { name: 'front/css', input: 'innopacks/front/resources/css/app.scss', outDir: 'public/themes/default/css', outputName: 'app', group: 'front' },
    { name: 'front/js', input: 'innopacks/front/resources/js/app.js', outDir: 'public/themes/default/js', outputName: 'app', group: 'front' },
    { name: 'panel/css', input: 'innopacks/panel/resources/css/app.scss', outDir: 'public/build/panel/css', outputName: 'app', group: 'panel' },
    { name: 'panel/js', input: 'innopacks/panel/resources/js/app.js', outDir: 'public/build/panel/js', outputName: 'app', group: 'panel' },
    { name: 'install/css', input: 'innopacks/install/resources/css/app.scss', outDir: 'public/build/install/css', outputName: 'app', group: 'install' },
    { name: 'tenant/css', input: 'innopacks/tenant/resources/assets/css/app.css', outDir: 'public/build/tenant/css', outputName: 'app', group: 'tenant' },
    { name: 'tenant/js', input: 'innopacks/tenant/resources/assets/js/app.js', outDir: 'public/build/tenant/js', outputName: 'app', group: 'tenant' },
];

const entries = theme ? [] : coreEntries.filter(e => (!target || e.group === target) && fs.existsSync(e.input));

if (theme) {
    const themeDir = `themes/${theme}`;
    const themeOut = `public/themes/${theme}`;

    ['css', 'js'].forEach(dir => {
        const p = `${themeOut}/${dir}`;
        if (fs.existsSync(p)) fs.rmSync(p, { recursive: true, force: true });
    });

    if (fs.existsSync(`${themeDir}/assets/scss/app.scss`))
        entries.push({ name: 'theme/css', input: `${themeDir}/assets/scss/app.scss`, outDir: `${themeOut}/css`, outputName: 'app' });
    else if (fs.existsSync(`${themeDir}/assets/css/app.css`))
        entries.push({ name: 'theme/css', input: `${themeDir}/assets/css/app.css`, outDir: `${themeOut}/css`, outputName: 'app' });
    if (fs.existsSync(`${themeDir}/assets/js/app.js`))
        entries.push({ name: 'theme/js', input: `${themeDir}/assets/js/app.js`, outDir: `${themeOut}/js`, outputName: 'app' });
}

if (entries.length === 0) {
    console.log('No entries to build.');
    process.exit(0);
}

let failed = 0;
for (const entry of entries) {
    try {
        execSync(
            `BUILD_INPUT="${entry.input}" BUILD_OUTDIR="${entry.outDir}" BUILD_OUTPUT_NAME="${entry.outputName}" npx vite build`,
            { stdio: 'pipe', env: { ...process.env, BUILD_INPUT: entry.input, BUILD_OUTDIR: entry.outDir, BUILD_OUTPUT_NAME: entry.outputName } }
        );
        if (entry.input.endsWith('.js')) {
            const idx = `${entry.outDir}/index.js`;
            const dest = `${entry.outDir}/${entry.outputName}.js`;
            if (fs.existsSync(idx) && idx !== dest) {
                fs.renameSync(idx, dest);
            }
        }
        console.log(`  ✓ ${entry.name}`);
    } catch (e) {
        console.error(`  ✗ ${entry.name}`);
        const err = e.stderr?.toString() || '';
        err.split('\n').filter(l => l.includes('Error')).slice(0, 3).forEach(l => console.error(`    ${l}`));
        failed++;
    }
}

// Theme distribution: copy built assets back into theme's public/ directory
if (theme) {
    const themeOut = `public/themes/${theme}`;
    const distDir = `themes/${theme}/public`;
    ['css', 'js'].forEach(dir => {
        const src = `${themeOut}/${dir}`;
        const dest = `${distDir}/${dir}`;
        if (fs.existsSync(src)) {
            fs.mkdirSync(dest, { recursive: true });
            fs.readdirSync(src).forEach(file => fs.copyFileSync(`${src}/${file}`, `${dest}/${file}`));
        }
    });
}

const elapsed = ((Date.now() - startTime) / 1000).toFixed(2);
console.log(`\nDone in ${elapsed}s${failed ? ` (${failed} failed)` : ''}`);

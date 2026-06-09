/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *
 * Vite Configuration for InnoCMS Site
 *
 * Single-entry builds driven by environment variables, invoked once per entry by build.js.
 *
 * Environment variables:
 *   BUILD_INPUT       — Source file path
 *   BUILD_OUTDIR      — Output directory
 *   BUILD_OUTPUT_NAME — Output filename for JS entries (default: 'app')
 *   THEME             — Theme name for @theme alias
 */

import { defineConfig } from 'vite';
import { resolve } from 'path';
import tailwindcss from '@tailwindcss/vite';

const theme = process.env.THEME || 'funnlinkcn';
const frontResources = resolve('innopacks/front/resources');

const input = process.env.BUILD_INPUT;
const outDir = process.env.BUILD_OUTDIR;

if (!input || !outDir) {
    throw new Error('BUILD_INPUT and BUILD_OUTDIR env vars required. Use build.js for full builds.');
}

const isJS = input.endsWith('.js');
const outputName = process.env.BUILD_OUTPUT_NAME || 'app';

export default defineConfig({
    plugins: [tailwindcss()],
    publicDir: false,
    build: {
        emptyOutDir: false,
        lib: isJS ? {
            entry: resolve(input),
            name: 'app',
            formats: ['iife'],
            fileName: () => outputName,
        } : undefined,
        rollupOptions: {
            input: isJS ? undefined : resolve(input),
            output: {
                dir: outDir,
                entryFileNames: '[name].js',
                assetFileNames: (info) => {
                    const name = info.name || 'app';
                    return `${name}${info.ext || ''}`;
                },
            },
        },
    },
    resolve: {
        alias: {
            '@front': frontResources,
            '@theme': resolve(`themes/${theme}`),
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
                loadPaths: [resolve('.'), resolve('node_modules')],
            },
        },
    },
});

const mix = require('laravel-mix');
const fs = require('fs');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix — aligned with InnoShop Factory patterns
 |--------------------------------------------------------------------------
 |
 | • Default storefront bundle → public/themes/default/
 | • Named theme bundle → THEME=mytheme npm run dev (outputs to public/themes/mytheme/, then copies css/js → themes/mytheme/public/)
 | • Aliases @front / @theme for imports in theme SCSS/JS (same convention as Factory)
 |
 */

const config = {
  theme: process.env.THEME || '',
  paths: {
    themes: 'themes',
    front: 'innopacks/front/resources',
    panel: 'innopacks/panel/resources',
    install: 'innopacks/install/resources',
    defaultPub: 'public/themes/default',
  },
};

mix.webpackConfig({
  resolve: {
    alias: {
      '@front': path.resolve(__dirname, config.paths.front),
      '@theme':
        config.theme !== ''
          ? path.resolve(__dirname, `${config.paths.themes}/${config.theme}`)
          : path.resolve(__dirname, `${config.paths.themes}/default`),
    },
  },
});

const utils = {
  fileExists: (p) => fs.existsSync(p),
  createDir: (p) => fs.mkdirSync(p, { recursive: true }),
  removeDir: (p) => fs.rmSync(p, { recursive: true, force: true }),
  log: (message, emoji = 'ℹ️') => console.log(`${emoji} ${message}`),
};

const themeManager = {
  /**
   * Strip compiled css/js before rebuild (preserve images/, etc. under theme public/).
   */
  cleanup: () => {
    if (!config.theme || config.theme === 'default') {
      return;
    }
    const themeBuildPath = `public/themes/${config.theme}`;
    ['css', 'js'].forEach((sub) => {
      const p = `${themeBuildPath}/${sub}`;
      if (utils.fileExists(p)) {
        utils.removeDir(p);
        utils.log(`Cleaned up: ${p}`, '🧹');
      }
      utils.createDir(p);
    });
    utils.log(`Prepared ${themeBuildPath}/{css,js}`, '📁');
  },

  compile: () => {
    if (!config.theme || config.theme === 'default') {
      return;
    }
    const themeDir = `${config.paths.themes}/${config.theme}`;
    const outputDir = `public/themes/${config.theme}`;
    utils.log(`Compiling theme: ${config.theme}`, '🎨');

    const appScss = `${themeDir}/css/app.scss`;
    if (utils.fileExists(appScss)) {
      mix.sass(appScss, `${outputDir}/css/app.css`);
      utils.log(`Compiled: ${appScss}`, '✅');
    }

    const appJs = `${themeDir}/js/app.js`;
    if (utils.fileExists(appJs)) {
      mix.js(appJs, `${outputDir}/js/app.js`);
      utils.log(`Compiled: ${appJs}`, '✅');
    }

    const bootstrapScss = `${themeDir}/css/bootstrap/bootstrap.scss`;
    if (utils.fileExists(bootstrapScss)) {
      mix.sass(bootstrapScss, `${outputDir}/css/bootstrap.css`);
      utils.log(`Compiled: ${bootstrapScss}`, '✅');
    }

    utils.log(`Theme ${config.theme} compilation completed!`, '✅');
  },
};

const themeDistributor = {
  /** Copy freshly built css/js from public/themes/<name>/ into themes/<name>/public/ (for theme_asset sync + versioning). */
  copyToThemePublic: () => {
    if (!config.theme || config.theme === 'default') {
      utils.log('Skipping theme distribution (default)', 'ℹ️');
      return;
    }

    const sourceDir = `public/themes/${config.theme}`;
    const targetRoot = `${config.paths.themes}/${config.theme}/public`;

    if (!utils.fileExists(sourceDir)) {
      utils.log(`Source directory not found: ${sourceDir}`, '⚠️');
      return;
    }

    try {
      utils.createDir(`${targetRoot}/css`);
      utils.createDir(`${targetRoot}/js`);

      ['app.css', 'bootstrap.css'].forEach((file) => {
        const sourceFile = `${sourceDir}/css/${file}`;
        const targetFile = `${targetRoot}/css/${file}`;
        if (utils.fileExists(sourceFile)) {
          fs.copyFileSync(sourceFile, targetFile);
          utils.log(`Copied css: ${file}`, '📦');
        }
      });

      const jsFile = `${sourceDir}/js/app.js`;
      if (utils.fileExists(jsFile)) {
        fs.copyFileSync(jsFile, `${targetRoot}/js/app.js`);
        utils.log('Copied js: app.js', '📦');
      }

      [
        { source: `${sourceDir}/css/app.css.map`, target: `${targetRoot}/css/app.css.map` },
        { source: `${sourceDir}/css/bootstrap.css.map`, target: `${targetRoot}/css/bootstrap.css.map` },
        { source: `${sourceDir}/js/app.js.map`, target: `${targetRoot}/js/app.js.map` },
      ].forEach(({ source: s, target: t }) => {
        if (utils.fileExists(s)) {
          utils.createDir(path.dirname(t));
          fs.copyFileSync(s, t);
        }
      });

      utils.log(`Theme assets copied to: ${targetRoot}`, '✅');
    } catch (e) {
      utils.log(`Theme distribution failed: ${e.message}`, '❌');
    }
  },
};

const defaultResources = {
  storefront: () => {
    const front = config.paths.front;
    const out = config.paths.defaultPub;
    mix.sass(`${front}/css/bootstrap/bootstrap.scss`, `${out}/css/bootstrap.css`);
    mix.sass(`${front}/css/app.scss`, `${out}/css/app.css`);
    mix.js(`${front}/js/app.js`, `${out}/js/app.js`);
  },

  panel: () => {
    const { panel } = config.paths;
    const build = 'public/build/panel';
    mix.sass(`${panel}/css/bootstrap/bootstrap.scss`, `${build}/css/bootstrap.css`);
    mix.sass(`${panel}/css/app.scss`, `${build}/css/app.css`);
    mix.sass(`${panel}/css/file-manager.scss`, `${build}/css/file-manager.css`);
    mix.js(`${panel}/js/app.js`, `${build}/js/app.js`);
  },

  install: () => {
    const { install } = config.paths;
    mix.sass(`${install}/css/app.scss`, 'public/build/install/css/app.css');
  },
};

themeManager.cleanup();

defaultResources.storefront();
defaultResources.panel();
defaultResources.install();
themeManager.compile();

if (mix.inProduction()) {
  mix.version();
}

mix.options({
  terser: {
    extractComments: false,
  },
});

mix.then(() => {
  utils.log('Post-build: syncing theme sources…', '📦');
  themeDistributor.copyToThemePublic();
});

const Encore = require('@symfony/webpack-encore');
const FosRouting = require('fos-router/webpack/FosRouting');
const fsPromises = require("fs/promises");
const fs = require("fs");
const dotenv = require("dotenv");

let environmentVariables = {};

async function loadIfExists(file, environmentVariables) {
    try {
        await fsPromises.access(file, fs.constants.F_OK);
    } catch (error) {
        console.error(error);
        return;
    }

    const fileContent = await fsPromises.readFile(file);
    Object.assign(environmentVariables, dotenv.parse(fileContent));
}

async function loadEnvFiles() {
    // Load common environment files
    const commonFiles = [
        ".env",
        ".env.local",
    ];
    for (let file of commonFiles) {
        await loadIfExists(file, environmentVariables);
    }

    // Load environment specific possible extra files
    const APP_ENV = process.env.APP_ENV || environmentVariables['APP_ENV'] || 'dev';
    const environmentSpecificFiles = [
        `.env.${APP_ENV}`,
        `.env.${APP_ENV}.local`,
    ];
    for (const file of environmentSpecificFiles) {
        await loadIfExists(file, environmentVariables);
    }

    // Load all environment variables (if not already defined)
    for (const key in environmentVariables) {
        if (process.env[key] === undefined) {
            process.env[key] = environmentVariables[key];
        }
    }
}

async function loadWebpackConfig() {
    await loadEnvFiles();

    // Manually configure the runtime environment if not already configured yet by the "encore" command.
    // It's useful when you use tools that rely on webpack.config.js file.
    if (!Encore.isRuntimeEnvironmentConfigured()) {
        Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
    }

    Encore
        // directory where compiled assets will be stored
        .setOutputPath("public/build/")
        // public path used by the web server to access the output path
        .setPublicPath('/build')
        // only needed for CDN's or sub-directory deploy
        .setManifestKeyPrefix('build')

        /*
        * ENTRY CONFIG
        *
        * Each entry will result in one JavaScript file (e.g. app.ts)
        * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
        */
        .addEntry('app', './assets/app.ts')

        // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
        .splitEntryChunks()

        // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.ts)
        .enableStimulusBridge('./assets/controllers.json')

        // will require an extra script tag for runtime.js
        // but, you probably want this, unless you're building a single-page app
        .enableSingleRuntimeChunk()

        /*
         * FEATURE CONFIG
         *
         * Enable & configure other features below. For a full
         * list of features, see:
         * https://symfony.com/doc/current/frontend.html#adding-more-features
         */
        .cleanupOutputBeforeBuild()
        .enableBuildNotifications()
        .enableSourceMaps(!Encore.isProduction())
        // enables hashed filenames (e.g. app.abc123.css)
        .enableVersioning(Encore.isProduction())

        // configure Babel
        // .configureBabel((config) => {
        //     config.plugins.push('@babel/a-babel-plugin');
        // })

        // enables and configure @babel/preset-env polyfills
        .configureBabelPresetEnv((config) => {
            config.useBuiltIns = 'usage';
            config.corejs = '3.38';
        })

        // enables Sass/SCSS support
        .enableSassLoader()

        // uncomment if you use TypeScript
        .enableTypeScriptLoader()

        // uncomment if you use React
        .enableReactPreset()

        // uncomment to get integrity="..." attributes on your script & link tags
        // requires WebpackEncoreBundle 1.4 or higher
        //.enableIntegrityHashes(Encore.isProduction())

        // uncomment if you're having problems with a jQuery plugin
        //.autoProvidejQuery()

        .copyFiles({
            from: "./assets/images",

            // optional target path, relative to the output dir
            to: "images/[path][name].[ext]",

            // if versioning is enabled, add the file hash too
            // to: 'images/[path][name].[hash:8].[ext]',

            // only copy files matching this pattern
            pattern: /\.(png|jpg|jpeg)$/,
        })
        .copyFiles({
            from: "./assets/images",

            // optional target path, relative to the output dir
            //to: 'images/[path][name].[ext]',

            // if versioning is enabled, add the file hash too
            to: "images/[path][name].[ext]",
            // only copy files matching this pattern
            pattern: /\.(svg)$/,
        })
        .addPlugin(new FosRouting())
    ;
    return Encore.getWebpackConfig();
}

module.exports = loadWebpackConfig();

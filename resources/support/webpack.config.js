const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const CaseSensitivePathsPlugin = require('case-sensitive-paths-webpack-plugin');
const fs = require('fs');

let package_dir = "/hashtagcms";

// Resolve Admin UI Kit path dynamically
let ADMIN_UI_KIT_PATH = "";
try {
    const mainExportPath = require.resolve("@hashtagcms/admin-ui-kit");
    ADMIN_UI_KIT_PATH = path.resolve(path.dirname(mainExportPath), '..');
} catch (e) {
    console.warn("HashtagCms: @hashtagcms/admin-ui-kit not found or couldn't be resolved.");
}

// Standard SASS include paths
const SASS_INCLUDE_PATHS = [
    path.resolve(__dirname, 'node_modules'),
    ADMIN_UI_KIT_PATH
];

// Simplified Tailwind content paths
const TAILWIND_CONTENT_PATHS = [
    "./resources/assets/**/*.js",
    "./resources/views/**/*.blade.php"
];

function makeArrays(themes, resourceDir, targetDir) {
    let entries = {};
    let copies = [];
    for (let i = 0; i < themes.length; i++) {
        let current = themes[i];
        let theme = current.theme.source;
        let assets = current.assets;

        for (let k in assets) {
            let type = assets[k]["type"];
            let currentKeyNode = assets[k];
            switch (type) {
                case "js":
                case "css":
                    entries[`${targetDir}/${theme}/${currentKeyNode.target}`] = `./${resourceDir}/${theme}/${currentKeyNode.source}`;
                    break;
                case "copy":
                    let sourcePath = "";
                    // Check if source refer to node_modules
                    if (currentKeyNode.source.startsWith('node_modules') || currentKeyNode.source.startsWith('~node_modules')) {
                        // Resolve as absolute path from project root
                        // Strip '~' if present
                        let cleanSource = currentKeyNode.source.replace(/^~/, '');
                        sourcePath = path.resolve(__dirname, cleanSource);
                    } else {
                        // Default: Resolve from local resources/assets
                        sourcePath = path.resolve(resourceDir, theme, currentKeyNode.source);
                    }

                    if (fs.existsSync(sourcePath)) {
                        copies.push({
                            from: sourcePath,
                            to: `${targetDir}/${theme}/${currentKeyNode.target}`,
                            noErrorOnMissing: true
                        });
                    }
                    else {
                        // Debugging logs for missing paths can be helpful, but keeping it silent for now to match style
                        // console.warn(`Source path does not exist: ${sourcePath}`);
                    }
                    break;
            }
        }
    }
    return { entries, copies };
}

let themesForFrontend = [
    {
        theme: { source: 'modern', type: 'theme' }, //folder
        assets: [
            { source: 'js/app.js', target: 'js/app', type: 'js' },
            { source: 'sass/app.scss', target: 'css/app', type: 'css' },
            { source: 'img', target: 'img', type: 'copy' },
            { source: 'fonts', target: 'fonts', type: 'copy' }
        ]
    }
];

let themesForBackend = [
    {
        theme: { source: 'modern', type: 'theme' }, //folder
        assets: [
            { source: 'js/app.js', target: 'js/app', type: 'js' },
            { source: 'js/dashboard.js', target: 'js/dashboard', type: 'js' },
            { source: 'js/error-handler.js', target: 'js/error-handler', type: 'js' },
            { source: 'js/editor.js', target: 'js/editor', type: 'js' },
            { source: 'sass/app.scss', target: 'css/app', type: 'css' },
            { source: 'img', target: 'img', type: 'copy' },
            { source: 'sass/app.scss', target: 'css/app', type: 'css' },
            // Explicitly copy vendors from admin-ui-kit package using resolved path
            { source: path.join(ADMIN_UI_KIT_PATH, 'dist/modern/js/vendors'), target: 'js/vendors', type: 'copy' }
        ]
    }
];



let toBeBuildF = makeArrays(themesForFrontend, `resources/assets${package_dir}/fe`, `public/assets${package_dir}/fe`);
let toBeBuildB = makeArrays(themesForBackend, `resources/assets${package_dir}/be`, `public/assets${package_dir}/be`);
//add installer
toBeBuildB.entries[`public/assets/installer/js/installer`] = `./resources/assets${package_dir}/js/installer.js`;

let buildEntries = {};
let buildCopies = [];
let mode = process.env.MODE;

if (mode === 'fe') {
    console.log("Building Frontend...");
    buildEntries = toBeBuildF.entries;
    buildCopies = toBeBuildF.copies;
} else if (mode === 'be') {
    console.log("Building Backend...");
    buildEntries = toBeBuildB.entries;
    buildCopies = toBeBuildB.copies;
} else {
    console.log("Building Everything...");
    buildEntries = { ...toBeBuildB.entries, ...toBeBuildF.entries };
    buildCopies = [...toBeBuildB.copies, ...toBeBuildF.copies];
}
console.log(buildEntries);
console.log("Please wait. Building assets...");
module.exports = {
    stats: {
        all: false,
        errors: true,
        errorDetails: true
    },
    mode: 'development',
    entry: buildEntries,
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname),
        publicPath: 'auto',
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: 'babel-loader'
            },
            {
                test: /\.css$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    { loader: "css-loader", options: { url: false, importLoaders: 1 } },
                    {
                        loader: 'postcss-loader', options: {
                            postcssOptions: {
                                plugins: [
                                    require('@tailwindcss/postcss')({
                                        content: TAILWIND_CONTENT_PATHS
                                    }),
                                    autoprefixer(),
                                    ...(process.env.NODE_ENV === 'production' ? [cssnano()] : [])
                                ],
                            },
                        }
                    },
                ],
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    // Load the CSS, set url = false to prevent following urls to fonts and images.
                    { loader: "css-loader", options: { url: false, importLoaders: 1 } },
                    // Add browser prefixes and minify CSS.
                    {
                        loader: 'postcss-loader', options: {
                            postcssOptions: {
                                plugins: [
                                    require('@tailwindcss/postcss')({
                                        content: TAILWIND_CONTENT_PATHS
                                    }),
                                    autoprefixer(),
                                    ...(process.env.NODE_ENV === 'production' ? [cssnano()] : [])
                                ],
                            },
                        }
                    },
                    // Load the SCSS/SASS
                    {
                        loader: 'sass-loader',
                        options: {
                            sassOptions: {
                                includePaths: SASS_INCLUDE_PATHS
                            }
                        }
                    }
                ],
            },
            {
                test: /\.(png|jpe?g|gif|svg|webp)$/i,
                type: "asset/resource",
                generator: {
                    filename: (pathData) => {
                        const filepath = pathData.module.resource;
                        const themeMatch = filepath.match(/[\\/]themes[\\/]([^\\/]+)/);
                        const themeName = themeMatch ? themeMatch[1] : "common";
                        return `public/assets/be/${themeName}/img/[name][ext]`;
                    },
                },
            }
        ]
    },
    plugins: [

        new CaseSensitivePathsPlugin(),
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin({
            filename: '[name].css',
        }),
        new CopyWebpackPlugin({
            patterns: buildCopies
        }),
        {
            apply: (compiler) => {
                compiler.hooks.done.tap('everythingIsDone', (compilation) => {
                    console.log("All Done. Cheer!")
                });

            }
        },
    ],
    resolve: {
        alias: {
            // Vue
            vue: path.resolve(__dirname, 'node_modules/vue/dist/vue.esm-bundler.js'),
            // Admin UI Kit aliases
            '@hashtagcms/admin-ui-kit/helpers': path.resolve(ADMIN_UI_KIT_PATH, 'helpers/index.js'),
            '@hashtagcms/admin-ui-kit/themes/modern': path.resolve(ADMIN_UI_KIT_PATH, 'themes/modern'),
            '@hashtagcms/helpers': path.resolve(ADMIN_UI_KIT_PATH, 'helpers'),
        },
        extensions: ['.js', '.vue'],
        // symlinks:true is required for npm link to work correctly.
        // When admin-ui-kit is published and installed via npm, this has no effect.
        symlinks: true,
        modules: [
            path.resolve(__dirname, 'node_modules'),
            path.resolve(ADMIN_UI_KIT_PATH, 'node_modules')
        ]
    },
};




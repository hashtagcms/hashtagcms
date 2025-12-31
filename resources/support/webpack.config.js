const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');


const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');

const CaseSensitivePathsPlugin = require('case-sensitive-paths-webpack-plugin');


const fs = require('fs');

let package_dir = "/hashtagcms";

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
                    copies.push({ from: `${resourceDir}/${theme}/${currentKeyNode.source}`, to: `${targetDir}/${theme}/${currentKeyNode.target}` });
                    break;

            }
        }
    }
    return { entries, copies };
}

let themesForFrontend = [
    {
        theme: { source: 'basic', type: 'theme' }, //folder
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
        theme: { source: 'neo', type: 'theme' }, //folder
        assets: [
            { source: 'js/app.js', target: 'js/app', type: 'js' },
            { source: 'js/dashboard.js', target: 'js/dashboard', type: 'js' },
            { source: 'js/error-handler.js', target: 'js/error-handler', type: 'js' },
            { source: 'js/editor.js', target: 'js/editor', type: 'js' },
            { source: 'sass/app.scss', target: 'css/app', type: 'css' },
            { source: 'img', target: 'img', type: 'copy' }
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
                    { loader: 'postcss-loader', options: { postcssOptions: { plugins: [autoprefixer(), cssnano()], }, } },
                ],
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    // Load the CSS, set url = false to prevent following urls to fonts and images.
                    { loader: "css-loader", options: { url: false, importLoaders: 1 } },
                    // Add browser prefixes and minify CSS.
                    { loader: 'postcss-loader', options: { postcssOptions: { plugins: [autoprefixer(), cssnano()], }, } },
                    // Load the SCSS/SASS
                    { loader: 'sass-loader' }
                ],
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
            vue: path.resolve(__dirname, 'node_modules/vue/dist/vue.esm-bundler.js'),
        },
        extensions: ['.js', '.vue'],
        symlinks: false
    },
};
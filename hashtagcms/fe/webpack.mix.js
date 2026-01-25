const mix = require('laravel-mix');
require('dotenv').config();
//mix.disableNotifications();

let package_dir = "hashtagcms";

//Installer
//mix.js(`resources/assets${package_dir}/js/installer.js`, 'public/assets/installer/js');

let themesForBackend = [
    {
        vueOption:{ version: 3 },
        theme:{source:'neo', type:'theme'}, //folder
        assets: [
            {source:'js/app.js', target:'js', type:'js'},
            {source:'js/dashboard.js', target:'js', type:'js'},
            {source:'js/error-handler.js', target:'js', type:'js'},
            {source:'js/editor.js', target:'js', type:'js'},
            {source:'../common/js/vendors', target:'js/vendors', type:'copy'},
            {source:'sass/app.scss', target:'css', type:'css'},
            {source:'img', target:'img', type:'copy'}
        ]
    }
];



let themesForFrontend = [
    {
        vueOption:{ version: 3 },
        theme:{source:'basic', type:'theme'}, //folder
        assets: [
            {source:'js/app.js', target:'js', type:'js'},
            {source:'sass/app.scss', target:'css', type:'css'},
            {source:'img', target:'img', type:'copy'},
            {source:'fonts', target:'fonts', type:'copy'}
        ]
    }
];

function buildNow(themes, resourceDir, targetDir) {
    for(let i=0; i<themes.length; i++) {

        let current = themes[i];
        console.log(current);
        let theme = current.theme.source;
        let assets = current.assets;
        for(let k in assets) {
            let type = assets[k]["type"];
            let currentKeyNode = assets[k];
            switch (type) {
                case "js":
                    mix.js(`${resourceDir}/${theme}/${currentKeyNode.source}`, `${targetDir}/${theme}/${currentKeyNode.target}`).vue(themes.vueOption);
                    break;
                case "css":
                    mix.sass(`${resourceDir}/${theme}/${currentKeyNode.source}`, `${targetDir}/${theme}/${currentKeyNode.target}`).options({processCssUrls: false});
                    break;
                case "copy":
                    mix.copyDirectory(`${resourceDir}/${theme}/${currentKeyNode.source}`, `${targetDir}/${theme}/${currentKeyNode.target}`);
                    break;

            }
        }

    }
}
//Backend
buildNow(themesForBackend, `resources/assets${package_dir}/be`, `public/assets${package_dir}/be`);

//Frontend
buildNow(themesForFrontend, `resources/assets${package_dir}/fe`, `public/assets${package_dir}/fe`);

<?php

namespace HashtagCms\Core\Traits;

use HashtagCms\Models\Theme;

trait LayoutHandler
{
    private array $themeCache;

    private array $backupAssetFolder = ['base_url' => '', 'base_path' => '/assets/hashtagcms/fe', 'js' => 'js', 'css' => 'css', 'image' => 'img'];

    public function parseStringForPath(?string $str = null, string|int|null $theme_dir = null): ?string
    {
        if (empty($str)) {
            return $str;
        }
        //if theme dir is an id; fetch it from Theme
        if (gettype($theme_dir) == 'integer') {
            if (isset($this->themeCache[$theme_dir])) {
                $theme = $this->themeCache[$theme_dir];
            } else {
                $this->themeCache[$theme_dir] = $theme = Theme::withoutGlobalScopes()->find($theme_dir); //it's theme id when passed as an integer
            }
            $theme_dir = $theme->directory;
        }
        $host = request()->getHost(); //env('APP_URL');

        $assetPath = config('hashtagcms.info.assets_path');

        //Get it by domain or by config
        $assetSource = (isset($assetPath[$host])) ? $assetPath[$host] : $assetPath;

        //External url (CDN) is not setup.
        if (! isset($assetSource['base_url'])) {
            $assetSource = $this->backupAssetFolder;
        }

        $resourceUrl = $assetSource['base_url'] ?? '';
        $resourceDir = $assetSource['base_path'] ?? '';
        $jsFolder = $assetSource['js'] ?? '';
        $cssFolder = $assetSource['css'] ?? '';
        $imageFolder = $assetSource['image'] ?? '';

        $resourcePath = $resourceUrl.$resourceDir.'/'.$theme_dir;

        //css/js media path
        $cssPath = $resourcePath.'/'.$cssFolder;
        $jsPath = $resourcePath.'/'.$jsFolder;
        $imgPath = $resourcePath.'/'.$imageFolder;

        if ($str != '' && $str != null) {
            $patterns = [];
            $patterns[0] = '/%{resource_path}%/';
            $patterns[1] = '/%{css_path}%/';
            $patterns[2] = '/%{js_path}%/';
            $patterns[3] = '/%{image_path}%/';

            $replacements = [];
            $replacements[0] = '/'.$resourcePath;
            $replacements[1] = $cssPath;
            $replacements[2] = $jsPath;
            $replacements[3] = $imgPath;
            //info("asset: this->getJsPath() ".asset($this->getJsPath()) ." === ". $str);

            $str = preg_replace($patterns, $replacements, $str);

            return $str;
        }

        return '';
    }
}

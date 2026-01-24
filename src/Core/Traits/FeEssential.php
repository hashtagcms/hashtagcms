<?php

namespace HashtagCms\Core\Traits;

trait FeEssential
{
    /**
     * Bind Data for a view
     */
    protected function bindDataForView(string $viewName, array $data = []): void
    {
        app()->HashtagCms->layoutManager()->bindDataForView($viewName, $data);
    }

    /**
     * Bind Data for a view
     */
    protected function replaceViewWith(?string $sourceViewName = null, ?string $targetViewName = null, ?array $data = null): void
    {
        app()->HashtagCms->layoutManager()->replaceViewWith($sourceViewName, $targetViewName, $data);
    }

    /**
     * @todo: should not work after refactoring
     * Set everything to render a page
     *
     * @param  array  $obj
     *                      - site
     *                      - language
     *                      - platform
     *                      - theme
     *                      - category
     */
    protected function setEverything(array $obj)
    {

        $info = ['site' => $obj['site'],
            'language' => $obj['language'],
            'platform' => $obj['platform'],
            'theme' => $obj['theme'],
            'category' => $obj['category'],
        ];

        $infoLoader = app()->HashtagCmsInfoLoader;
        //set info
        $infoLoader->setObjInfo($info);

        $infoLoader->setMultiContextVars($obj['category']['id'], $obj['site']['id'], $obj['platform']['id'], $obj['microsite']);
        $infoLoader->setLanguageId($obj['language']['id'], $obj['language']['iso_code']);

        app()->HashtagCms->setThemePath($obj['theme']['directory']);
        app()->HashtagCms->setTheme($obj['theme']);

    }
}

<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Http\Request;
use HashtagCms\Http\Resources\PageResource;
use HashtagCms\Models\Page;

class BlogController extends FrontendBaseController
{
    /**
     * Render page (@override)
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $infoLoader = app()->HashtagCms->infoLoader();
        $callableValue = $infoLoader->getAppCallableValue();

        //check it's blog home
        if (empty($callableValue[0])) {
            $this->setModuleMandatoryCheck(false); //in base controlle

            $siteId = $infoLoader->getSiteId();
            $langId = $infoLoader->getLangId();
            $categoryName = $infoLoader->getCategoryName();

            $perPage = config('hashtagcms.blog_per_page');

            $moreCategories = config('hashtagcms.more_categories_on_blog_listing');
            $useMore = false;
            if (count($moreCategories) > 0) {
                $useMore = true;
                $moreCategories[] = $categoryName; //add one more
            }

            $requestCat = ($useMore) ? $moreCategories : $categoryName;
            $results = Page::getLatestBlog($siteId, $langId, $requestCat, $perPage);

            $data['results'] = PageResource::collection($results)->toArray($request);

            //replace mandatory module with new module.
            $this->replaceViewWith('story', 'stories', $data);

            $forComments = ['isBlogHome' => true];
            $this->bindDataForView('story-comments', $forComments);

            return parent::index($request);
        }

        return parent::index($request);

    }

    public function story($arg1, $arg2)
    {
        return ['From blog/story contoller', $arg1, $arg2];
    }
}

<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use HashtagCms\Core\Traits\LayoutHandler;

class CategorySiteResource extends JsonResource
{
    use LayoutHandler;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'categoryId' => $this->category_id,
            'siteId' => $this->site_id,
            'platformId' => $this->platform_id,
            'themeId' => $this->theme_id,
            'icon' => $this->icon,
            'iconCss' => $this->icon_css,
            'headerContent' => $this->parseStringForPath($this->header_content, $this->theme_id),
            'footerContent' => $this->parseStringForPath($this->footer_content, $this->theme_id),
            'excludeInListing' => $this->exclude_in_listing,
            'position' => $this->position,
            'cacheCategory' => $this->cache_category,
        ];
    }
}

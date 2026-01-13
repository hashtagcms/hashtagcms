<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use HashtagCms\Core\Traits\LayoutHandler;

class CategoryResource extends JsonResource
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

        $hasSiteInfo = ! empty($this->siteWise) ? true : false;

        $reseouce = [
            'id' => $this->id,
            'parentId' => $this->parent_id,
            'siteId' => $this->site_id,
            'isSiteDefault' => $this->is_site_default,
            'isRootCategory' => $this->is_root_category,
            'isNew' => $this->is_new,
            'hasWap' => $this->has_wap,
            'wapUrl' => $this->wap_url,
            'linkRewrite' => $this->link_rewrite,
            'linkNavigation' => $this->link_navigation,
            'linkRewritePattern' => $this->link_rewrite_pattern,
            'controllerName' => $this->controller_name,
            'hasSomeSpecialModule' => $this->has_some_special_module,
            'specialModuleAlias' => $this->special_module_alias,
            'requiredLogin' => $this->required_login,
            'insertBy' => $this->insert_by,
            'updateBy' => $this->update_by,
            'publishStatus' => $this->publish_status,
            'readCount' => $this->read_count,
            'name' => $this->lang->name,
            'title' => $this->lang->title,
            'excerpt' => $this->lang->excerpt,
            'content' => $this->lang->content,
            'activeKey' => $this->lang->active_key,
            'thirdPartyMappingKey' => $this->lang->third_party_mapping_key,
            'b2bMapping' => $this->lang->b2b_mapping,
            'isExternal' => $this->lang->is_external,
            'linkRelation' => $this->lang->link_relation,
            'target' => $this->lang->target,
            'metaTitle' => $this->lang->meta_title,
            'metaKeywords' => $this->lang->meta_keywords,
            'metaDescription' => $this->lang->meta_description,
            'metaRobots' => $this->lang->meta_robots,
            'metaCanonical' => $this->lang->meta_canonical,
        ];
        if ($hasSiteInfo) {
            $reseouce['icon'] = $this->siteWise->icon;
            $reseouce['iconCss'] = $this->siteWise->icon_css;
            $reseouce['headerContent'] = $this->parseStringForPath($this->siteWise->header_content, $this->siteWise->theme_id);
            $reseouce['footerContent'] = $this->parseStringForPath($this->siteWise->footer_content, $this->siteWise->theme_id);
            $reseouce['excludeInListing'] = $this->siteWise->exclude_in_listing;
            $reseouce['position'] = $this->siteWise->position;
            $reseouce['cacheCategory'] = $this->siteWise->cache_category;
        }

        return $reseouce;
    }
}

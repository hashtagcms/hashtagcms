<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'categoryLinkRewrite' => $this->category_link_rewrite,
            'userName' => $this->user_name,
            'readCount' => $this->read_count,
            'siteId' => $this->site_id,
            'micrositeId' => $this->microsite_id,
            'platformId' => $this->platform_id,
            'categoryId' => $this->category_id,
            'alias' => $this->alias,
            'excludeInListing' => $this->exclude_in_listing,
            'contentType' => $this->content_type,
            'position' => $this->position,
            'linkRewrite' => $this->link_rewrite,
            'menuPlacement' => $this->menu_placement,
            'enableComments' => $this->enable_comments,
            'attachment' => $this->attachment,
            'img' => $this->img,
            'author' => $this->author,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'pageContent' => $this->page_content,
            'linkRelation' => $this->link_relation,
            'target' => $this->target,
            'activeKey' => $this->active_key,
            'commentsCount' => $this->comments_count,
        ];
    }
}

<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    public $preserveKeys = true;

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
            'name' => $this->name,
            'title' => $this->title ?? $this->lang->title ?? null,
            'categoryId' => $this->category_id,
            'themeId' => $this->theme_id,
            'platformId' => $this->platform_id,
            'langId' => $this->lang_id,
            'countryId' => $this->country_id,
            'currencyId' => $this->currency_id,
            'underMaintenance' => $this->under_maintenance,
            'domain' => $this->domain,
            'context' => $this->context,
            'favicon' => $this->favicon,
            'langCount' => $this->lang_count,
        ];
    }
}

<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleSiteResource extends JsonResource
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
            'siteId' => $this->site_id,
            'micrositeId' => $this->microsite_id,
            'platformId' => $this->platform_id,
            'categoryId' => $this->category_id,
            'hookId' => $this->hook_id,
            'moduleId' => $this->module_id,
            'position' => $this->position,
            'publishStatus' => $this->publish_status,
            'insertBy' => $this->insert_by,
            'updateBy' => $this->update_by,
            'approvedBy' => $this->approved_by,
        ];
    }
}

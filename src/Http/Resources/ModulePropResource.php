<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModulePropResource extends JsonResource
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
            $this->name => $this->lang->value,
            'group' => $this->group,
        ];
    }
}

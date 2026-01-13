<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HookResource extends JsonResource
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
            'name' => $this->name,
            'alias' => $this->alias,
            'placeholder' => "%{cms.hook.{$this->alias}}%",
            'direction' => $this->direction,
            'description' => $this->description,
            'modules' => isset($this->modules) ? $this->modules : [],
        ];
    }
}

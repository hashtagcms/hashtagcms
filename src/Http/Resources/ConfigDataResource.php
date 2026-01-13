<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConfigDataResource extends JsonResource
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
            'site' => $this->site,
            'defaultData' => $this->defaultData,
            'platforms' => $this->platforms,
            'langs' => $this->langs,
            'currencies' => $this->currencies,
            'zones' => $this->zones,
            'countries' => $this->countries,
            'categories' => $this->categories,
            'props' => $this->props,
        ];
    }
}

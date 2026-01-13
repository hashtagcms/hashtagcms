<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LangResource extends JsonResource
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
            'isoCode' => $this->iso_code,
            'languageCode' => $this->language_code,
            'dateFormatLite' => $this->date_format_lite,
            'dateFormatFull' => $this->date_format_full,
            'isRtl' => $this->is_rtl,
        ];
    }
}

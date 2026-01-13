<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'isoCodeNum' => $this->iso_code_num,
            'sign' => $this->sign,
            'blank' => $this->blank,
            'format' => $this->format,
            'decimals' => $this->decimals,
            'conversionRate' => $this->conversion_rate,
            'conversionRateForSite' => $this->pivot->conversion_rate,
            'markup' => $this->pivot->markup,
            'markupType' => $this->pivot->markup_type,
        ];
    }
}

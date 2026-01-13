<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'name' => $this->lang->name,
            'zoneId' => $this->zone_id,
            'currencyId' => $this->currency_id,
            'isoCode' => $this->iso_code,
            'callPrefix' => $this->call_prefix,
            'containsStates' => $this->contains_states,
            'needIdentificationNumber' => $this->need_identification_number,
            'needZipCode' => $this->need_zip_code,
            'zipCodeFormat' => $this->zip_code_format,
            'displayTaxLabel' => $this->display_tax_label,
        ];
    }
}

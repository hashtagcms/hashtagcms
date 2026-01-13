<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FestivalResource extends JsonResource
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
            'image' => $this->image,
            'bodyCss' => $this->body_css,
            'lottie' => $this->lottie,
            'lottieAttributes' => [
                'background' => $this->background,
                'speed' => $this->speed,
                'mode' => $this->play_mode,
                'autoplay' => ($this->autoplay) === 1,
                'loop' => $this->loop === 1,
                'controls' => $this->controls === 1,
                'hover' => ($this->hover) === 1,
                'direction' => $this->direction,

            ],
            'lottieStyles' => [
                'width' => $this->width,
                'height' => $this->height,
                'position' => $this->position_css,
                'top' => $this->top,
                'left' => $this->left,
                'zIndex' => $this->z_index,
            ],
            'lottieHideOnComplete' => $this->hide_on_complete === 1,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'position' => $this->position,
            'extra' => $this->extra,
        ];
    }
}

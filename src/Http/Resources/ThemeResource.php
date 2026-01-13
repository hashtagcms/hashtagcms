<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use HashtagCms\Core\Traits\LayoutHandler;

class ThemeResource extends JsonResource
{
    use LayoutHandler;

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
            'siteId' => $this->site_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'directory' => $this->directory,
            'bodyClass' => $this->body_class,
            'imgPreview' => $this->img_preview,
            'skeleton' => $this->parseStringForPath($this->skeleton, $this->directory),
            'headerContent' => $this->parseStringForPath($this->header_content, $this->directory),
            'footerContent' => $this->parseStringForPath($this->footer_content, $this->directory),
            'hooks' => isset($this->hooks) ? $this->hooks : [],
            'modules' => isset($this->modules) ? $this->modules : [],
        ];
    }
}

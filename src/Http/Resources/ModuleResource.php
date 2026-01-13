<?php

namespace HashtagCms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $isLocal = env('APP_ENV') === 'local';
        $isServerLater = strtolower($this->data_type) === 'servicelater';
        $data = [
            'id' => $this->id,
            'siteId' => $this->site_id,
            'name' => $this->name,
            'alias' => $this->alias,
            'placeholder' => "%{cms.module.{$this->alias}}%",
            'linkedModule' => $this->linked_module,
            'viewName' => $this->view_name,
            'dataType' => $this->data_type,
            'description' => $this->description,
            'isMandatory' => $this->is_mandatory,
            'headers' => $this->headers,
            'individualCache' => $this->individual_cache,
            'cacheGroup' => $this->cache_group,
            'isSeoModule' => $this->is_seo_module,
            'liveEdit' => $this->live_edit,
            'shared' => $this->shared,
        ];

        if ($isLocal) {
            $data['queryStatement'] = $this->query_statement;
            $data['queryAs'] = $this->query_as;
        }
        if ($isServerLater || $isLocal) {
            $data['dataHandler'] = $this->data_handler;
            $data['dataKeyMap'] = $this->data_key_map;
            $data['methodType'] = $this->method_type;
            $data['serviceParams'] = $this->service_params;
        }

        if (isset($this->queryData)) {
            $data['queryData'] = $this->queryData;
        }
        $data['data'] = $this->data;
        $data['moduleProps'] = $this->moduleProps;

        return $data;
    }
}

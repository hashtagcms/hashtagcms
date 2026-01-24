<?php

namespace HashtagCms\Core\Main;

class UrlServiceModuleLoader extends Results implements ModuleLoaderServiceImp
{
    protected array $result;

    public function __construct(?string $service_url = null, ?string $method_type = null, array $withData = [], ?string $data_key_map = null, array $headers = [])
    {
        parent::__construct();
        if ($service_url != null) {
            $this->process($service_url, $method_type, $withData, $data_key_map, $headers);
        }
    }

    public function process(?string $service_url = null, ?string $method_type = null, array $withData = [], ?string $data_key_map = null, array $headers = []): void
    {

        if ($service_url == '' || $service_url == null) {
            info('Service url is missing');
            $this->setResult([]);
        }
        $urls = explode('?', $service_url);

        $arguments = [];

        //make sure if we are not passing data
        if (count($urls) > 1) {
            //we have arguments too
            parse_str($urls[1], $arguments);
        }

        $url = $urls[0];
        $withData = array_merge($arguments, $withData);

        $arguments = $this->makeQueryParams($data_key_map);
        $arguments = array_merge($withData, $arguments);

        if (isset($arguments['resultType'])) {
            $headers['Content-Type'] = $arguments['resultType'];
        }
        //dd("url ".$url, $arguments);
        try {
            $ml = new ServiceModuleLoader($url, $method_type, $arguments, $headers);

            $this->setResult($ml->getResult());
        } catch (\Exception $e) {
            info('UrlServiceModuleLoader: '.$e->getMessage());
            $this->setResult([]);
        }

    }

    /**
     * @return array
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * @param  array  $data
     */
    public function setResult(mixed $data): void
    {
        $this->result = ! empty($data) ? collect($data)->all() : [];
    }

    /**
     * @param  string  $url
     */
    private function makeQueryParams(string $data_key_map): array
    {
        $dataKeyMap = explode(',', $data_key_map);
        $arr = [];
        foreach ($dataKeyMap as $key => $val) {
            $arr[$val] = request()->input($val);
        }

        return $arr;

    }
}

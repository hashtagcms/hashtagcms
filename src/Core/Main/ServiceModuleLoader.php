<?php

namespace HashtagCms\Core\Main;

use Illuminate\Support\Facades\Http;

class ServiceModuleLoader extends Results implements ModuleLoaderServiceImp
{
    protected array $result = [];

    protected bool $foundError = false;

    protected string $errorMessage = '';

    public function __construct(?string $service_url = null, ?string $method_type = null, array $withData = [], array $headers = [])
    {
        parent::__construct();
        if ($service_url != null) {
            $this->process($service_url, $method_type, $withData, $headers);
        }
    }

    public function process(?string $service_url = null, ?string $method_type = null, array $withData = [], array $headers = []): void
    {
        $data = [];
        $this->foundError = false;
        $this->errorMessage = '';

        if ($service_url == '' || $service_url == null) {
            $this->setResult([]);

            return;
        }

        $urls = explode('?', $service_url);

        $arguments = [];

        //make sure if we are not passing data
        if (count($urls) > 1) {
            //we have arguments too
            parse_str($urls[1], $arguments);
        }

        $url = $urls[0];
        $arguments = array_merge($arguments, $withData);
        $contentTypes = ['text/css', 'text/csv', 'text/html', 'text/plain', 'text/xml'];

        $contentType = 'json';

        //if in params
        if (isset($arguments['resultType']) && in_array($arguments['resultType'], $contentTypes)) {
            $headers['Content-Type'] = $arguments['resultType'];
            $contentType = 'text';
        }
        //if in headers - it can merge with above one. but don't want to.
        if (isset($headers['Content-Type']) && in_array($headers['Content-Type'], $contentTypes)) {
            $contentType = 'text';
        }

        try {

            $timeout = config('hashtagcms.external_service_timeout', 60);

            switch (strtolower($method_type)) {
                case 'get':
                    if ($contentType === 'text') {
                        $data = Http::withHeaders($headers)->timeout($timeout)->get($url, $arguments)->body();
                    } else {
                        $data = Http::withHeaders($headers)->timeout($timeout)->get($url, $arguments)->json();
                    }
                    break;
                case 'post':
                    if ($contentType === 'text') {
                        $data = Http::withHeaders($headers)->timeout($timeout)->post($url, $arguments)->body();
                    } else {
                        $data = Http::withHeaders($headers)->timeout($timeout)->post($url, $arguments)->json();
                    }

                    break;
            }

        } catch (\Exception $exception) {
            $err = 'Error: getServiceModule ' . $exception->getMessage();
            info('Error: getServiceModule ' . $exception->getMessage());
            $data = [];
            $this->foundError = true;
            $this->errorMessage = $exception->getMessage();
            //$this->setResult([]);
        }

        $this->setResult($data);
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
        $this->result = collect($data)->all();
    }

    /**
     * Has Error
     */
    public function hasError(): bool
    {
        return $this->foundError;
    }

    /**
     * Get error message
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}

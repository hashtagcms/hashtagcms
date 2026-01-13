<?php

namespace HashtagCms\Core\Main;

class QueryModuleLoader extends Results implements ModuleLoaderImp
{
    protected array $result;

    public function __construct(?string $query = null, ?string $database = null)
    {
        parent::__construct();
        if ($query != null) {
            $this->process($query, $database);
        }
    }

    public function process(string $query, ?string $database = null): void
    {

        $data = $this->dbSelect($query, [], $database);
        $this->setResult($data);
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $data): void
    {
        $this->result = collect($data)->all();
    }
}

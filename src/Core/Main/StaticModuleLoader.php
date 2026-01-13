<?php

namespace HashtagCms\Core\Main;

class StaticModuleLoader extends Results implements ModuleLoaderImp
{
    protected array $result;

    public function __construct(?string $alias = null)
    {
        parent::__construct();
        if ($alias != null) {
            $this->process($alias);
        }
    }

    public function process(string $alias): void
    {
        $query = "select smcl.title, smcl.content from static_module_contents smc  
                  left join static_module_content_langs smcl on (smc.id = smcl.static_module_content_id)
                  where smc.alias='$alias' and lang_id=:lang_id and site_id=:site_id";

        $data = $this->dbSelectOne($query);

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

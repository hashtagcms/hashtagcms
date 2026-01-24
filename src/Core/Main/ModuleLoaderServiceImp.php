<?php

namespace HashtagCms\Core\Main;

interface ModuleLoaderServiceImp
{
    public function getResult(): mixed;

    public function setResult(mixed $data): void;
}

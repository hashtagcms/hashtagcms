<?php

$packageJson = json_decode(file_get_contents(__DIR__.'/../package.json'), true);
return [
    'version' => $packageJson['version'] ?? '2.x.x',
];

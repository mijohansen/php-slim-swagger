<?php

require __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PSX\Model\Swagger\ExternalDocs;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\Swagger;
use SlimSwagger\Api;
use SlimSwagger\Util;

$lines = [];
$lines = Util::getMethodDocBlock(Api::class, Info::class, $lines);
$lines = Util::getMethodDocBlock(Api::class, Swagger::class, $lines);
$lines = Util::getMethodDocBlock(Api::class, ExternalDocs::class, $lines);

echo implode(PHP_EOL, $lines) . PHP_EOL;


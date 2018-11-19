<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18/11/2018
 * Time: 17:50
 */

use PSX\Model\Swagger\Operation;
use SlimSwagger\Route;
use SlimSwagger\Util;

require __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$lines = [];
$lines = Util::getMethodDocBlock(Route::class, Operation::class, $lines);

echo implode(PHP_EOL, $lines) . PHP_EOL;


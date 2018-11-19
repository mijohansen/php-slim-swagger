<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18/11/2018
 * Time: 17:50
 */

use PSX\Model\Swagger\ExternalDocs;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\Swagger;
use SlimSwagger\Api;

require __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$lines = [];
//$lines = print_methods(Operation::class);
$lines = print_methods(Info::class, $lines);
$lines = print_methods(Swagger::class, $lines);
$lines = print_methods(ExternalDocs::class, $lines);

$lines = array_unique($lines);
sort($lines);
echo implode(PHP_EOL, $lines) . PHP_EOL;

function print_methods($from, $lines = []) {
    $reflection = new ReflectionClass($from);
    $existing = new ReflectionClass(Api::class);
    $routeMethodNames = [];
    foreach ($existing->getMethods() as $method) {
        $routeMethodNames[] = $method->name;
    }
    foreach ($reflection->getMethods() as $method) {
        $params = [];
        foreach ($method->getParameters() as $param) {
            $params[] = "$" . $param->getName();
        }
        $params_string = implode(", ", $params);
        if (!in_array($method->name, $routeMethodNames) && !$method->isConstructor()) {
            $lines[] = "* @method Route {$method->name}($params_string)";
        }
    }
    return $lines;
}
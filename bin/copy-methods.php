<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18/11/2018
 * Time: 17:50
 */

use PSX\Model\Swagger\Info;

require __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

//$from = Operation::class;
$from = Info::class;
//$from = Swagger::class;

$reflection = new ReflectionClass($from);

foreach ($reflection->getMethods() as $method) {
    $params = [];
    foreach ($method->getParameters() as $param) {
        $params[] = "$" . $param->getName();
    }

    $params_string = implode(",", $params);
    echo "* @method Route {$method->name}($params_string)" . PHP_EOL;
}

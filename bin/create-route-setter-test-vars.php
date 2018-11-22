<?php

use PSX\Model\Swagger\Operation;

require __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$reflecting_methods = [
    Operation::class
];
$pairs = [];
foreach ($reflecting_methods as $reflecting_method) {
    $existing = new \ReflectionClass($reflecting_method);
    foreach ($existing->getMethods() as $method) {
        $reqParams = $method->getNumberOfRequiredParameters();
        $prefix = substr($method->getName(), 0, 3);
        $methodStem = substr($method->getName(), 3);
        if ($prefix === "set" && strlen($methodStem) > 1 && $reqParams > 0) {
            $defVal = $method->getParameters()[0]->getType();
            if (is_null($defVal)) {
                $pairs[$methodStem] = ["set" . $methodStem, "get" . $methodStem, "dummy_str"];
            } else {
                if ($defVal->getName() === "array") {
                    $pairs[$methodStem] = ["set" . $methodStem, "get" . $methodStem, ["dummy_str", "dummy"]];
                }
            }

        }
    }
}
$pairs = array_values($pairs);
foreach ($pairs as $pair) {
    echo json_encode($pair) . "," . PHP_EOL;
}
<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 04/08/2018
 * Time: 10:37
 */

namespace SlimSwagger;

use FastRoute\RouteParser\Std;
use PSX\Model\Swagger\Parameter;
use PSX\Model\Swagger\Path;
use PSX\Model\Swagger\Responses;
use PSX\Model\Swagger\Swagger;
use PSX\Schema\Parser\Popo\Dumper;
use Slim\Interfaces\RouteInterface;

if (!function_exists('is_iterable')) {
    function is_iterable($obj) {
        return is_array($obj) || (is_object($obj) && ($obj instanceof \Traversable));
    }
}

class Util {

    /**
     * Utility class to solve
     *
     * @param $mixed
     * @return mixed
     */
    static public function dump($mixed) {
        static $dumper;
        if (is_null($dumper)) {
            $dumper = new Dumper();
        }
        $dumped = $dumper->dump($mixed);

        if (is_iterable($dumped)) {
            foreach ($dumped as $prop => $value) {
                if (is_a($value, \ArrayObject::class)) {
                    /** @var \ArrayObject $value */
                    if ($value->getFlags() === 0) {
                        $new_array = [];
                        foreach ($value->getArrayCopy() as $valuex => $element) {
                            $new_array[$valuex] = self::dump($element);
                        }
                        $dumped[$prop] = $new_array;
                    }
                } else {
                    $dumped[$prop] = self::dump($value);
                }
            }
        }
        return $dumped;
    }

    static public function isSwaggerRoute(RouteInterface $route) {
        /** @var Route $route */
        return (is_a($route, Route::class) && $route->isSwaggerPath());
    }

    /**
     * @param Swagger $swagger
     * @param Route $route
     * @return Swagger
     */
    static public function addRouteToSwagger(Swagger $swagger, Route $route) {
        static $parser;
        if (is_null($parser)) {
            $parser = new Std();
        }
        $pattern = $route->getPattern();
        $route_segments = $parser->parse($pattern)[0];
        $parameters = [];
        foreach ($route_segments as $segment) {
            if (is_array($segment)) {
                $param = new Parameter();
                $param->setName($segment[0]);
                $param->setIn("path");
                $param->setType("string");
                $parameters[] = $param;
            }
        }
        $path = new Path();
        if (count($parameters)) {
            $path->setParameters($parameters);
        }
        foreach ($swagger->getPaths() as $existing_pattern => $existing_path) {
            if ($pattern === $existing_pattern) {
                $path = $existing_path;
            }
        }
        $path = self::swaggerRouteToOperation($route, $path);
        $swagger->addPath($pattern, $path);
        return $swagger;
    }

    /**
     * @param Route $route
     * @param Path $path
     * @return Path
     */
    static public function swaggerRouteToOperation(Route $route, Path $path) {
        $operation = $route->getOperation();
        $operation->setOperationId($route->getIdentifier());
        $responses = new Responses();
        $response = new \PSX\Model\Swagger\Response();
        $response->setDescription('No response buddy');
        $responses[200] = $response;
        $operation->setResponses($responses);
        foreach ($route->getMethods() as $method) {
            switch ($method) {
                case "GET":
                    $path->setGet($operation);
                    break;
                case "POST":
                    $path->setPost($operation);
                    break;
            }
        }
        return $path;
    }

    static public function getMethodDocBlock($to, $from, $lines = []) {
        $reflection = new \ReflectionClass($from);
        $existing = new \ReflectionClass($to);
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
                $lines[] = "* @method {$existing->getShortName()} {$method->name}($params_string)";
            }
        }
        $lines = array_unique($lines);
        sort($lines);
        return $lines;
    }
}

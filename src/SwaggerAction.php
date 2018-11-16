<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16/11/2018
 * Time: 16:42
 */

namespace SlimSwagger;

use Psr\Container\ContainerInterface;
use PSX\Model\Swagger\Swagger;
use Slim\Http\Request;
use Slim\Http\Response;

class SwaggerAction {

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \ReflectionException
     */
    public function __invoke(Request $request, Response $response) {
        $routes = $this->container->get("router")->getRoutes();
        $definitions = $this->container->get("router")->getDefinitions();
        $host = $request->getUri()->getHost();
        $port = $request->getUri()->getPort();

        $swagger = $this->container->get("swagger");
        /** @var Swagger $swagger */
        if (!in_array($request->getUri()->getPort(), [80, 443])) {
            $host = $host . ":" . $port;
        }
        $swagger->setHost($host);
        $swagger->setSchemes([$request->getUri()->getScheme()]);
        foreach ($routes as $route) {
            if (Util::isSwaggerRoute($route)) {
                $swagger = Util::addRouteToSwagger($swagger, $route);
            }
        }
        foreach ($definitions as $definitionClassName) {
            $name = array_pop(explode("\\", $definitionClassName));
            $reflectionClass = new $definitionClassName();
            $reflect = new \ReflectionClass($reflectionClass);
            $definition = new \stdClass();
            $definition->properties = [];
            foreach ($reflect->getProperties() as $property) {
                $definition->properties[$property->getName()] = [
                    "type" => "string"
                ];
            }
            $definition->type = "object";
            $definition->required = [];
            $swagger->addDefinition($name, $definition);
        }
        return $response->withJson(Util::dump($swagger));
    }
}

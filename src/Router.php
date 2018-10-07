<?php

namespace SlimSwagger;

use PSX\Model\Swagger\Swagger;
use Slim\App;

class Router extends \Slim\Router {

    private $definitions = [];

    /**
     * Create a new Route object
     *
     * @param  string[] $methods Array of HTTP methods
     * @param  string $pattern The route pattern
     * @param  callable $callable The route callable
     *
     * @return \Slim\Interfaces\RouteInterface
     */
    protected function createRoute($methods, $pattern, $callable) {
        $route = new Route($methods, $pattern, $callable, $this->routeGroups, $this->routeCounter);
        if (!empty($this->container)) {
            $route->setContainer($this->container);
        }
        return $route;
    }

    static public function attach(App $app, $path = "/swagger.json") {
        $container = $app->getContainer();
        /**
         * Override the default router.
         *
         * @param $container
         * @return Router
         */
        $container['router'] = function ($container) {
            $routerCacheFile = false;
            if (isset($container->get('settings')['routerCacheFile'])) {
                $routerCacheFile = $container->get('settings')['routerCacheFile'];
            }

            $router = (new Router())->setCacheFile($routerCacheFile);
            if (method_exists($router, 'setContainer')) {
                $router->setContainer($container);
            }
            return $router;
        };

        /**
         * @param $container
         * @return Swagger
         */
        $container['swagger'] = function ($container) {
            $swagger = new Swagger();
            $swagger->setConsumes(["application/json"]);
            $swagger->setProduces(["application/json"]);
            return $swagger;
        };
    }

    /**
     * @param $definition
     * @return Router
     */
    public function addDefinition($definition) {
        $this->definitions[] = $definition;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefinitions() {
        return $this->definitions;
    }
}
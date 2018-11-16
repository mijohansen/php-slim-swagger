<?php

namespace SlimSwagger;

use Slim\App;

class Router extends \Slim\Router {

    private $definitions = [];

    /**
     * @param App $app
     */
    static public function attach(App $app) {
        $container = $app->getContainer();
        SlimSwagger::init($container);
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
}
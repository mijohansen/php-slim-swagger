<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16/11/2018
 * Time: 17:03
 */

namespace SlimSwagger;

use Psr\Container\ContainerInterface;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\Swagger;
use Slim\Container;
use Slim\Interfaces\RouterInterface;

class SlimSwagger {



    /**
     * @param Container|null $container
     * @return Container
     */
    static public function init(Container $container = null) {
        if (is_null($container)) {
            $container = new Container([
                'settings' => [],
            ]);
        }
        /**
         * Override the default router.
         *
         * @param $container
         * @return RouterInterface
         */
        $container['router'] = function (ContainerInterface $container) {
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
         * @return Swagger
         */
        $container['swagger'] = function () {
            $swagger = new Swagger();
            $swagger->setConsumes(["application/json"]);
            $swagger->setProduces(["application/json"]);
            $swagger->setInfo(new Info());
            return $swagger;
        };
        return $container;
    }
}
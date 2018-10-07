<?php

namespace SlimSwagger;

use Composer\Spdx\SpdxLicenses;
use Psr\Container\ContainerInterface;
use PSX\Model\Swagger\Contact;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\License;
use PSX\Model\Swagger\Swagger;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 03/08/2018
 * Time: 17:28
 */
class Api {

    /**
     * @var App
     */
    private $app;

    private $tags;



    /**
     * Api constructor.
     * @param App $app
     * @param array $tags
     */
    public function __construct(App $app, $tags = []) {
        $this->app = $app;
        $this->tags = $tags;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer() {
        return $this->app->getContainer();
    }

    /**
     * @param string $pattern
     * @param callable|string $callable
     * @return Route
     */
    public function get($pattern, $callable) {
        $route = $this->app->get($pattern, $callable);
        $route->getOperation()->setTags($this->tags);
        $route->setApiObject($this);
        return $route;
    }

    /**
     * @param string $pattern
     * @param callable|string $callable
     * @return Route
     */
    public function post($pattern, $callable) {
        $route = $this->app->post($pattern, $callable);
        $route->getOperation()->setTags($this->tags);
        $route->setApiObject($this);
        return $route;
    }

    /**
     * @param string $pattern
     * @param callable|string $callable
     * @return Route
     */
    public function put($pattern, $callable) {
        $route = $this->app->put($pattern, $callable);
        $route->getOperation()->setTags($this->tags);
        $route->setApiObject($this);
        return $route;
    }

    /**
     * @param string $pattern
     * @param callable|string $callable
     * @return Route
     */
    public function patch($pattern, $callable) {
        $route = $this->app->patch($pattern, $callable);
        $route->getOperation()->setTags($this->tags);
        $route->setApiObject($this);
        return $route;
    }

    /**
     * @param string $pattern
     * @param callable|string $callable
     * @return Route
     */
    public function delete($pattern, $callable) {
        $route = $this->app->delete($pattern, $callable);
        $route->getOperation()->setTags($this->tags);
        $route->setApiObject($this);
        return $route;
    }

    /**
     * @param string $pattern
     * @param callable|string $callable
     * @return Route
     */
    public function options($pattern, $callable) {
        $route = $this->app->options($pattern, $callable);
        $route->getOperation()->setTags($this->tags);
        $route->setApiObject($this);
        return $route;
    }

    /**
     * @return Router;
     */
    public function getRouter() {
        return $this->app->getContainer()->get("router");
    }

    /**
     * @return Swagger;
     */
    public function getSwagger() {
        return $this->app->getContainer()->get("swagger");
    }

    /**
     * @return Info
     */
    public function getApiInfo() {
        $info = $this->getSwagger()->getInfo();
        if (is_null($info)) {
            $info = new Info();
        }
        return $info;
    }

    /**
     * @param $identifier
     * @return $this
     */
    public function setApiLicense($licenseIdentifier) {
        $SpdxLicenses = new SpdxLicenses();
        $license = $SpdxLicenses->getLicenseByIdentifier($licenseIdentifier);
        $info = $this->getApiInfo();
        $swaggerLicenseField = new License();
        $swaggerLicenseField->setName($license[0]);
        $swaggerLicenseField->setUrl($license[2]);
        $info->setLicense($swaggerLicenseField);
        $this->getSwagger()->setInfo($info);
        return $this;
    }

    /**
     * @return $this
     */
    public function setApiContact() {
        $info = $this->getApiInfo();
        $contact = new Contact();
        foreach (func_get_args() as $arg) {
            if (filter_var($arg, FILTER_VALIDATE_EMAIL)) {
                $contact->setEmail($arg);
            } elseif (filter_var($arg, FILTER_VALIDATE_URL)) {
                $contact->setUrl($arg);
            } else {
                $contact->setName($arg);
            }
        }
        $info->setContact($contact);
        return $this;
    }

    /**
     * @param $version
     * @return $this
     */
    public function setVersion($version) {
        $this->getApiInfo()->setVersion($version);
        return $this;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title) {
        $this->getApiInfo()->setTitle($title);
        return $this;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description) {
        $this->getApiInfo()->setDescription($description);
        return $this;
    }

    /**
     * @param $termOfService
     * @return $this
     */
    public function setTermsOfService($termOfService) {
        $this->getApiInfo()->setTermsOfService($termOfService);
        return $this;
    }

    public function setTags(array $tags) {
        $this->tags = $tags;
    }

 

    /**
     * @param string $path
     * @return Swagger;
     */
    public function addSwaggerRoute($path = "/swagger.json") {
        Router::attach($this->app);
        $this->app->get($path, function (Request $req, Response $res)  {
            $routes = $this->get("router")->getRoutes();
            $definitions = $this->get("router")->getDefinitions();
            $swagger = $this->get("swagger");
            $host = $req->getUri()->getHost();
            $port = $req->getUri()->getPort();
            /** @var Swagger $swagger */
            if (!in_array($req->getUri()->getPort(), [80, 443])) {
                $host = $host . ":" . $port;
            }
            $swagger->setHost($host);
            $swagger->setSchemes([$req->getUri()->getScheme()]);
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
            return $res->withJson(Util::dump($swagger));
        });
    }
}
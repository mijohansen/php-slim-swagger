<?php

namespace SlimSwagger;

use Composer\Spdx\SpdxLicenses;
use Psr\Container\ContainerInterface;
use PSX\Model\Swagger\Contact;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\License;
use PSX\Model\Swagger\Swagger;
use Slim\App;

/**
 * @method Route addDefinition($name, $schema)
 * @method Route addPath($path, $value)
 * @method Route getBasePath()
 * @method Route getConsumes()
 * @method Route getContact()
 * @method Route getDefinitions()
 * @method Route getDescription()
 * @method Route getExternalDocs()
 * @method Route getHost()
 * @method Route getLicense()
 * @method Route getParameters()
 * @method Route getPaths()
 * @method Route getProduces()
 * @method Route getResponses()
 * @method Route getSchemes()
 * @method Route getSecurity()
 * @method Route getSecurityDefinitions()
 * @method Route getSwagger()
 * @method Route getTags()
 * @method Route getTermsOfService()
 * @method Route getTitle()
 * @method Route getVersion()
 * @method Route setBasePath($basePath)
 * @method Route setConsumes($consumes)
 * @method Route setContact($contact)
 * @method Route setDefinitions($definitions)
 * @method Route setDescription($description)
 * @method Route setExternalDocs($externalDocs)
 * @method Route setHost($host)
 * @method Route setInfo($info)
 * @method Route setParameters($parameters)
 * @method Route setPaths($paths)
 * @method Route setProduces($produces)
 * @method Route setResponses($responses)
 * @method Route setSchemes($schemes)
 * @method Route setSecurity($security)
 * @method Route setSecurityDefinitions($securityDefinitions)
 * @method Route setTermsOfService($termsOfService)
 * @method Route setTitle($title)
 * @method Route setVersion($version)
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
        /** @var Route $route */
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
        /** @var Route $route */
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
        /** @var Route $route */
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
        /** @var Route $route */
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
        /** @var Route $route */
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
        /** @var Route $route */
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
     * @param $licenseIdentifier |License
     * @return $this
     */
    public function setLicense($licenseIdentifier) {
        $info = $this->getInfo();
        if (is_string($licenseIdentifier)) {
            $SpdxLicenses = new SpdxLicenses();
            $license = $SpdxLicenses->getLicenseByIdentifier($licenseIdentifier);
            $swaggerLicenseField = new License();
            $swaggerLicenseField->setName($license[0]);
            $swaggerLicenseField->setUrl($license[2]);
            $info->setLicense($swaggerLicenseField);
        } else {
            $info->setLicense($licenseIdentifier);
        }
        $this->getSwaggerModel()->setInfo($info);
        return $this;
    }

    /**
     * @return Info
     */
    public function getInfo() {
        $info = $this->getSwaggerModel()->getInfo();
        if (is_null($info)) {
            $info = new Info();
        }
        return $info;
    }

    /**
     * @return Swagger;
     */
    public function getSwaggerModel() {
        return $this->app->getContainer()->get("swagger");
    }

    /**
     * We just forward the method calls to Operation.
     *
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments) {
        if (method_exists($this->getSwaggerModel(), $name)) {
            call_user_func_array([$this->getSwaggerModel(), $name], $arguments);
        }
        if (method_exists($this->getInfo(), $name)) {
            call_user_func_array([$this->getInfo(), $name], $arguments);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setApiContact() {
        $info = $this->getInfo();
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





    public function setTags(array $tags) {
        $this->tags = $tags;
    }

    /**
     * @param string $path
     */
    public function addSwaggerRoute($path = "/swagger.json") {
        Router::attach($this->app);
        $this->app->get($path, SwaggerAction::class);
    }
}


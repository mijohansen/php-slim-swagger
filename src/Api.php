<?php

namespace SlimSwagger;

use Composer\Spdx\SpdxLicenses;
use Psr\Container\ContainerInterface;
use PSX\Model\Swagger\Contact;
use PSX\Model\Swagger\ExternalDocs;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\License;
use PSX\Model\Swagger\Swagger;
use Slim\App;

/**
 * @method Api addDefinition($name, $schema)
 * @method Api addPath($path, $value)
 * @method Api getBasePath()
 * @method Api getConsumes()
 * @method Api getContact()
 * @method Api getDefinitions()
 * @method Api getDescription()
 * @method Api getHost()
 * @method Api getLicense()
 * @method Api getParameters()
 * @method Api getPaths()
 * @method Api getProduces()
 * @method Api getResponses()
 * @method Api getSchemes()
 * @method Api getSecurity()
 * @method Api getSecurityDefinitions()
 * @method Api getSwagger()
 * @method Api getTags()
 * @method Api getTermsOfService()
 * @method Api getTitle()
 * @method Api getUrl()
 * @method Api getVersion()
 * @method Api setBasePath($basePath)
 * @method Api setConsumes($consumes)
 * @method Api setDefinitions($definitions)
 * @method Api setDescription($description)
 * @method Api setExternalDocs($externalDocs)
 * @method Api setHost($host)
 * @method Api setInfo($info)
 * @method Api setParameters($parameters)
 * @method Api setPaths($paths)
 * @method Api setProduces($produces)
 * @method Api setResponses($responses)
 * @method Api setSchemes($schemes)
 * @method Api setSecurity($security)
 * @method Api setSecurityDefinitions($securityDefinitions)
 * @method Api setTermsOfService($termsOfService)
 * @method Api setTitle($title)
 * @method Api setUrl($url)
 * @method Api setVersion($version)
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
            $this->getSwaggerModel()->setInfo($info);
        }
        return $info;
    }

    /**
     * @return ExternalDocs;
     */
    public function getExternalDocs() {
        $externalDocs = $this->getSwaggerModel()->getExternalDocs();
        if (is_null($externalDocs)) {
            $externalDocs = new ExternalDocs();
            $this->getSwaggerModel()->setExternalDocs($externalDocs);
        }
        return $externalDocs;
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
        if (method_exists($this->getExternalDocs(), $name)) {
            call_user_func_array([$this->getExternalDocs(), $name], $arguments);
        }
        return $this;
    }

    /**
     * @param $mixed | Contact
     * @return $this
     */
    public function setContact($mixed) {
        $info = $this->getInfo();
        if (is_a($mixed, Contact::class)) {
            $info->setContact($mixed);
        } else {
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
        }
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


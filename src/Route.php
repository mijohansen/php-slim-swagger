<?php

namespace SlimSwagger;

use PSX\Model\Swagger\Operation;
use PSX\Model\Swagger\Parameter;

/**
 * @method Route setTags($tags)
 * @method Route getTags()
 * @method Route setSummary($summary)
 * @method Route getSummary()
 * @method Route setDescription($description)
 * @method Route getDescription()
 * @method Route setExternalDocs($externalDocs)
 * @method Route getExternalDocs()
 * @method Route setOperationId($operationId)
 * @method Route getOperationId()
 * @method Route setProduces($produces)
 * @method Route getProduces()
 * @method Route setConsumes($consumes)
 * @method Route getConsumes()
 * @method Route setParameters($parameters)
 * @method Route getParameters()
 * @method Route setResponses($responses)
 * @method Route getResponses()
 * @method Route setSchemes($schemes)
 * @method Route getSchemes()
 * @method Route setDeprecated($deprecated)
 * @method Route getDeprecated()
 * @method Route setSecurity($security)
 * @method Route getSecurity()
 */
class Route extends \Slim\Route {

    /**
     * @var Operation
     */
    protected $operation;

    /**
     * @var Api
     */
    protected $apiObject;

    public function isSwaggerPath() {
        return !is_null($this->operation);
    }

    /**
     * @return Operation
     */
    public function getOperation() {
        if (is_null($this->operation)) {
            $this->operation = new Operation();
        }
        return $this->operation;
    }

    /**
     * We just forward the method calls to Operation.
     *
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments) {
        if (method_exists($this->getOperation(), $name)) {
            call_user_func_array([$this->getOperation(), $name], $arguments);
        }
        return $this;
    }

    /**
     * @param $requestClass
     * @return $this
     */
    public function setRequestClass($requestClass) {
        $name = array_pop(explode("\\", $requestClass));
        $parameters = $this->getOperation()->getParameters();
        $this->getApiObject()->getRouter()->addDefinition($requestClass);
        $param = new Parameter();
        $param->setIn("body");
        $param->setRequired(true);
        $param->setName($name);
        $schema = new \stdClass();
        $schema->ref = "#/definitions/" . $name;
        $param->setSchema($schema);
        $param->setType("object");
        $parameters[] = $param;
        $this->getOperation()->setParameters($parameters);
        return $this;
    }

    /**
     * @return Api
     */
    public function getApiObject() {
        return $this->apiObject;
    }

    /**
     * @param $apiObject
     * @return $this
     */
    public function setApiObject(Api $apiObject) {
        $this->apiObject = $apiObject;
        return $this;
    }
}

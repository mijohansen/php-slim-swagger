<?php

namespace SlimSwagger;

use PSX\Model\Swagger\Operation;
use PSX\Model\Swagger\Parameter;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 03/08/2018
 * Time: 17:16
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
     * @param $description
     * @return $this
     */
    public function desc($description) {
        $this->getOperation()->setDescription($description);
        return $this;
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
     * @param $summary
     * @return $this
     */
    public function summary($summary) {
        $this->getOperation()->setSummary($summary);
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
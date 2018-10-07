<?php

namespace SlimSwagger;

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
     * @return \PSX\Model\Swagger\Operation
     */
    public function getOperation() {
        if (is_null($this->operation)) {
            $this->operation = new \PSX\Model\Swagger\Operation();
        }
        return $this->operation;
    }

    /**
     * @return $this
     */
    public function desc($description) {
        $this->getOperation()->setDescription($description);
        return $this;
    }

    /**
     * @return Route
     */
    public function summary($summary) {
        $this->getOperation()->setSummary($summary);
        return $this;
    }

    /**
     * @param $apiObject
     * @return Route
     */
    public function setApiObject(Api $apiObject) {
        $this->apiObject = $apiObject;
        return $this;
    }

    /**
     * @return Api
     */
    public function getApiObject() {
        return $this->apiObject;
    }
    /**
     *
     */
    /**
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
        $param->setSchema(['$ref' => "#/definitions/" . $name]);
        $param->setType("object");
        $parameters[] = $param;
        $this->getOperation()->setParameters($parameters);
        return $this;
    }
}
<?php

namespace SlimSwagger;

use PSX\Model\Swagger\Operation;

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


    public function isSwaggerPath() {
        return !is_null($this->operation);
    }

    /**
     * @return Operation
     */
    public function getOperation(){
        if(is_null($this->operation)){
            $this->operation =  new Operation();
        }
        return $this->operation;
    }

    /**
     * @return $this
     */
    public function desc($description){
        $this->getOperation()->setDescription($description);
        return $this;
    }

    /**
     * @return $this
     */
    public function summary($summary){
        $this->getOperation()->setSummary($summary);
        return $this;
    }

}
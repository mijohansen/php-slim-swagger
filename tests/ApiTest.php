<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16/11/2018
 * Time: 18:27
 */

use PHPUnit\Framework\TestCase;
use PSX\Model\Swagger\Swagger;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use SlimSwagger\Api;
use SlimSwagger\Route;
use SlimSwagger\SlimSwagger;
use SlimSwagger\SwaggerAction;

final class ApiTest extends TestCase {

    public function testApiCouldBeMounted() {
        $container = SlimSwagger::init();
        $app = new App($container);
        $api = new Api($app);
        $swagger = $api->getSwagger();
        $this->assertInstanceOf(Swagger::class, $swagger);
        //ob_start();
        $content = $app->run(true);
        $this->assertEquals(404, $content->getStatusCode());
        //$this->assertStringStartsWith("<html", ob_get_clean());

    }

    public function testShouldMountRoute() {
        $app = new App(SlimSwagger::init());
        $api = new Api($app);
        $route = $api->get("/", function () { });
        $this->assertInstanceOf(Route::class, $route);
    }

    public function testShouldMountSwaggerRoute() {
        $app = new App(SlimSwagger::init());
        $app->get("/swagger.json", SwaggerAction::class);

        /**
         * Create a fake environment
         */
        $env = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/swagger.json',
        ]);
        $req = Request::createFromEnvironment($env);
        $app->getContainer()['request'] = $req;
        $response = $app->run(true);
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode((string)$response->getBody());
        $this->assertAttributeEquals("2.0", "swagger", $content);
    }
}

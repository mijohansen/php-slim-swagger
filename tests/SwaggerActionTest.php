<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16/11/2018
 * Time: 18:27
 */

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use SlimSwagger\SlimSwagger;
use SlimSwagger\SwaggerAction;

final class SwaggerTest extends TestCase {

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

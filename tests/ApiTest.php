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
use SlimSwagger\Api;
use SlimSwagger\Route;
use SlimSwagger\Router;
use SlimSwagger\SlimSwagger;
use SlimSwagger\Util;

final class ApiTest extends TestCase {

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testApiCouldBeMounted() {
        $container = SlimSwagger::init();
        $app = new App($container);
        $api = new Api($app);
        $swagger = $api->getSwaggerModel();
        $this->assertInstanceOf(Swagger::class, $swagger);

        $content = $app->run(true);
        $this->assertEquals(404, $content->getStatusCode());
        //$this->assertStringStartsWith("<html", ob_get_clean());

    }

    /**
     *
     */
    public function testShouldMountRoute() {
        $app = new App(SlimSwagger::init());
        $api = new Api($app);
        $route = $api->get("/", function () { });
        $this->assertInstanceOf(Route::class, $route);
        $this->assertInstanceOf(Router::class, $app->getContainer()->get("router"));
        $this->assertInstanceOf(Swagger::class, $app->getContainer()->get("swagger"));
    }

    /**
     *
     */
    public function testContactSetter() {
        $app = new App(SlimSwagger::init());
        $api = new Api($app);
        $api->setContact("Michael Johansen");
        $model = $api->getSwaggerModel();
        $info = Util::dump($model);
        $swagger = json_decode(json_encode($info));
        $this->assertEquals("Michael Johansen", $swagger->info->contact->name);
    }

    /**
     *
     */
    public function testExternalDocsSetter() {
        $app = new App(SlimSwagger::init());
        $api = new Api($app);
        $test_value = "http://example.com";
        $api->setExternalDocs($test_value);
        $model = $api->getSwaggerModel();
        $info = Util::dump($model);
        $swagger = json_decode(json_encode($info));
        $this->assertEquals($test_value, $swagger->externalDocs->url);
    }

    /**
     * @dataProvider provideSimpleSettersAndGetters
     * @param $setter
     * @param $getter
     * @param $dummy_value
     */
    public function testSimpleSettersAndGetters($setter, $getter, $dummy_value) {
        $app = new App(SlimSwagger::init());
        $api = new Api($app);
        $setter_return = call_user_func_array([$api, $setter], [$dummy_value]);
        $this->assertSame($api, $setter_return);
        $getter_return = call_user_func_array([$api, $getter], [$dummy_value]);
        $this->assertEquals($dummy_value, $getter_return);
    }

    /**
     * @return array
     */
    public function provideSimpleSettersAndGetters() {
        return array(
            ["setTitle", "getTitle", "dummy_str"],
            ["setVersion", "getVersion", "dummy_str"],
            ["setDescription", "getDescription", "dummy_str"],
            ["setTermsOfService", "getTermsOfService", "dummy_str"],
            ["setHost", "getHost", "dummy_str"],
            ["setBasePath", "getBasePath", "dummy_str"],
            ["setSchemes", "getSchemes", ["dummy_str", "dummy"]],
            ["setConsumes", "getConsumes", ["dummy_str", "dummy"]],
            ["setProduces", "getProduces", ["dummy_str", "dummy"]],
            ["setParameters", "getParameters", "dummy_str"],
            ["setResponses", "getResponses", "dummy_str"],
            ["setSecurity", "getSecurity", ["dummy_str", "dummy"]],
            ["setTags", "getTags", ["dummy_str", "dummy"]],
            ["setUrl", "getUrl", "dummy_str"],
        );
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16/11/2018
 * Time: 18:27
 */

use PHPUnit\Framework\TestCase;
use Slim\App;
use SlimSwagger\Api;
use SlimSwagger\SlimSwagger;
use SlimSwagger\Util;

final class RouteTest extends TestCase {

    /**
     * @dataProvider provideSimpleSettersAndGetters
     * @param $setter
     * @param $getter
     * @param $dummy_value
     */
    public function testSimpleSettersAndGetters($setter, $getter, $dummy_value) {
        $app = new App(SlimSwagger::init());
        $api = new Api($app);
        $route = $api->get("/", function () { });
        $setter_return = call_user_func_array([$route, $setter], [$dummy_value]);
        $this->assertSame($route, $setter_return);
        $getter_return = call_user_func_array([$route, $getter], [$dummy_value]);
        $this->assertEquals($dummy_value, $getter_return);
    }

    /**
     * @return array
     */
    public function provideSimpleSettersAndGetters() {
        return array(
            ["setTags", "getTags", ["dummy_str", "dummy"]],
            ["setSummary", "getSummary", "dummy_str"],
            ["setDescription", "getDescription", "dummy_str"],
            ["setOperationId", "getOperationId", "dummy_str"],
            ["setProduces", "getProduces", ["dummy_str", "dummy"]],
            ["setConsumes", "getConsumes", ["dummy_str", "dummy"]],
            ["setParameters", "getParameters", ["dummy_str", "dummy"]],
            ["setResponses", "getResponses", "dummy_str"],
            ["setSchemes", "getSchemes", ["dummy_str", "dummy"]],
            ["setDeprecated", "getDeprecated", "dummy_str"],
            ["setSecurity", "getSecurity", ["dummy_str", "dummy"]],
        );
    }

    /**
     *
     */
    public function testExternalDocsSetter() {
        $app = new App(SlimSwagger::init());
        $api = new Api($app);
        $route = $api->get("/", function () { });
        $test_value = "http://example.com";
        $route->setExternalDocs($test_value);
        $model = $route->getOperation();
        $info = Util::dump($model);
        $swagger = json_decode(json_encode($info));
        $this->assertEquals($test_value, $swagger->externalDocs->url);
    }
}

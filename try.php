<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 03/08/2018
 * Time: 18:42
 */

require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$parser = new FastRoute\RouteParser\Std();

$parsed = $parser->parse("/my/route/{my-var}/{something-else}");

print_r($parsed);
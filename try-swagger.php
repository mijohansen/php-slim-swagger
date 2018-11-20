<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 04/08/2018
 * Time: 03:37
 */
require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PSX\Model\Swagger\Contact;
use PSX\Model\Swagger\ExternalDocs;
use PSX\Model\Swagger\Header;
use PSX\Model\Swagger\Headers;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\License;
use PSX\Model\Swagger\Operation;
use PSX\Model\Swagger\Parameter;
use PSX\Model\Swagger\Path;
use PSX\Model\Swagger\Response;
use PSX\Model\Swagger\Responses;
use PSX\Model\Swagger\Swagger;
use PSX\Model\Swagger\Tag;
use PSX\Schema\Parser\Popo\Dumper;

$contact = new Contact();
$contact->setName('Swagger API Team');
$contact->setEmail('apiteam@swagger.io');
$contact->setUrl('http://swagger.io');
$license = new License();
$license->setName('MIT');
$license->setUrl('http://github.com/gruntjs/grunt/blob/master/LICENSE-MIT');

$info = new Info();
$info->setVersion('1.0.0');
$info->setTitle('Swagger Petstore');
$info->setDescription('A sample API that uses a petstore as an example to demonstrate features in the swagger-2.0 specification');
$info->setTermsOfService('http://swagger.io/terms/');
$info->setContact($contact);
$info->setLicense($license);
$petSchema = json_decode('{ "required": [ "id", "name" ], "properties": { "id": { "type": "integer", "format": "int64" }, "name": { "type": "string" }, "tag": { "type": "string" } } }');
$petsSchema = json_decode('{ "type": "array", "items": { "$ref": "#/definitions/Pet" } }');
$errorSchema = json_decode('{ "required": [ "code", "message" ], "properties": { "code": { "type": "integer", "format": "int32" }, "message": { "type": "string" } } }');
$externalDocs = new ExternalDocs();
$externalDocs->setDescription('find more info here');
$externalDocs->setUrl('https://swagger.io/about');
$tags = [];
$tag = new Tag();
$tag->setName('pets');
$tag->setDescription('Pets operations');
$tags[] = $tag;
$tag = new Tag();
$tag->setName('bar');
$tag->setDescription('Boo tag');
$tags[] = $tag;
$swagger = new Swagger();
$swagger->setInfo($info);
$swagger->setExternalDocs($externalDocs);
$swagger->setHost('petstore.swagger.io');
$swagger->setBasePath('/v1');
$swagger->setSchemes(['http']);
$swagger->setConsumes(['application/json']);
$swagger->setProduces(['application/json']);
$swagger->addDefinition('Pet', $petSchema);
$swagger->addDefinition('Pets', $petsSchema);
$swagger->addDefinition('Error', $errorSchema);
$swagger->setTags($tags);
$parameters = [];

$parameter = new Parameter();
$parameter->setName('limit');
$parameter->setIn('query');
$parameter->setRequired(false);
$parameter->setDescription('How many items to return at one time (max 100)');
$parameter->setType('integer');
$parameter->setFormat('int32');
$parameters[] = $parameter;
$header = new Header();
$header->setType('string');
$header->setDescription('A link to the next page of responses');

$headers = new Headers();
$headers['x-next'] = $header;
$responses = new Responses();

$response = new Response();
$response->setDescription('An paged array of pets');
$response->setHeaders($headers);
$response->setSchema((object)['$ref' => '#/definitions/Pets']);
$responses['200'] = $response;
$response = new Response();
$response->setDescription('unexpected error');
$response->setSchema((object)['$ref' => '#/definitions/Error']);
$responses['default'] = $response;
$operation = new Operation();
$operation->setSummary('List all pets');
$operation->setOperationId('listPets');
$operation->setTags(['pets']);
$operation->setParameters($parameters);
$operation->setResponses($responses);

$path = new Path();
$path->setGet($operation);
$responses = new Responses();
$response = new Response();
$response->setDescription('Null response');
$responses['201'] = $response;
$response = new Response();
$response->setDescription('unexpected error');
$response->setSchema((object)['$ref' => '#/definitions/Error']);
$responses['default'] = $response;

$operation = new Operation();
$operation->setSummary('Create a pet');
$operation->setOperationId('createPets');
$operation->setTags(['pets']);
$operation->setResponses($responses);
$path->setPost($operation);
$swagger->addPath('/pets', $path);
$parameters = [];

$parameter = new Parameter();
$parameter->setName('petId');
$parameter->setIn('path');
$parameter->setRequired(true);
$parameter->setDescription('The id of the pet to retrieve');
$parameter->setType('string');
$parameters[] = $parameter;
$responses = new Responses();
$response = new Response();
$response->setDescription('Expected response to a valid request');
$response->setSchema((object)['$ref' => '#/definitions/Pets']);

$responses['200'] = $response;
$response = new Response();
$response->setDescription('unexpected error');
$response->setSchema((object)['$ref' => '#/definitions/Error']);
$responses['default'] = $response;
$operation = new Operation();
$operation->setSummary('Info for a specific pet');
$operation->setOperationId('showPetById');
$operation->setTags(['pets']);
$operation->setParameters($parameters);
$operation->setResponses($responses);

$path = new Path();
$path->setGet($operation);
$swagger->addPath('/pets/{petId}', $path);
$dumper = new Dumper();
$actual = json_encode($dumper->dump($swagger), JSON_PRETTY_PRINT);

echo $actual;
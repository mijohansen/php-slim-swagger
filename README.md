# php-slim-swagger
Slim needs a simpler way to define an Swagger API. No docblock magic, just code.

This project is at an early stage. Functionality will break.

## Usage

### Adding the dependencies to the container
You need to override the router and add the swagger liberary
to Slim. A clean way of doing this is BEFORE the $app object is
initiated.

Minimal example:
```php
use Slim\App;
use SlimSwagger\SlimSwagger;

$container = SlimSwagger::init();
$app = new App($container);
```

### Installing the swagger route:

```php
use SlimSwagger\SwaggerAction;

$app->get('/swagger.json', SwaggerAction::class);
```


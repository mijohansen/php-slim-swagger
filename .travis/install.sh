#!/bin/bash -e
composer global require hirak/prestissimo --no-interaction --no-suggest
composer global require phpunit/phpunit
composer install --prefer-source --no-interaction --dev --ignore-platform-reqs --no-suggest
phpenv config-rm xdebug.ini
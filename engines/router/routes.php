<?PHP
// engines/router/routes.php

require_once __DIR__ . '/Router.php';

use router\Router;

/** @var Router $router */
$router = new Router();

// Глобальные маршруты (если есть)
// ...

return $router;
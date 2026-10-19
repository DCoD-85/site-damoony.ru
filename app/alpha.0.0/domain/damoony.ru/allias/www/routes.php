<?PHP
// app/alpha.0.0/domain/damoony.ru/allias/www/routes.php

use alogic\Router;

$router = new Router(); // <--- ДОБАВИТЬ ЭТО

/** @var Router $router */
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/index', 'algoritm_lang_index');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}', 'algoritm_lang');

return $router; // <--- ДОБАВИТЬ
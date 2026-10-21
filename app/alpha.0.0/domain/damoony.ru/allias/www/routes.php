<?PHP
// app/alpha.0.0/domain/damoony.ru/allias/www/routes.php

use router\Router;

$router = new Router();

/** @var Router $router */

// Главная страница
$router->add('/{lang:[a-z]{2}-[a-z]{2}}', 'algoritm_lang');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/index', 'algoritm_lang_index');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/lang', 'algoritm_lang_index');

// Тестовая страница (работает и для /test, и для /test/list)
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/test', 'algoritm_lang_test');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/test/list', 'algoritm_lang_test_list');

// Секретная страница (только для авторизованных)
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/test/auth', 'algoritm_lang_test_auth');

return $router;

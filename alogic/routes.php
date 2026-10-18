<?PHP

require_once __DIR__ . '/Router.php';

use alogic\Router;

/** @var Router $router */
$router = new Router();

// Основные страницы (из algoritm_lang.data.json)
$router->add('/{lang:[a-z]{2}-[a-z]{2}}', 'algoritm_lang');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/{page:index|docs|news|downloads|download|login|logout|registration}', 'algoritm_lang_index');

// Профиль пользователя (динамика)
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/profile/{login:[A-Za-z0-9_-]+}', 'algoritm_profile_user');

return $router;
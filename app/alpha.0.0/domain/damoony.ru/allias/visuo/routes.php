<?PHP
use router\Router;

$router = new Router();
/** @var Router $router */

$router->add('/{lang:[a-z]{2}-[a-z]{2}}', 'algoritm_lang_index');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/index', 'algoritm_lang_index');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/lang', 'algoritm_lang_index');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/login', 'algoritm_lang_login');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/registration', 'algoritm_lang_registration');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/logout', 'algoritm_lang_logout');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/profile', 'algoritm_lang_profile');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/docs', 'algoritm_lang_docs');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/news', 'algoritm_lang_news');

return $router;

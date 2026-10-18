<?PHP
// app/alpha.0.0/domain/damoony.ru/allias/visuo/routes.php

use alogic\Router;

$router = new Router(); // <--- ДОБАВИТЬ ЭТО

/** @var Router $router */
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/index', 'algoritm_lang_index');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/profile/{login:[A-Za-z0-9_-]+}', 'algoritm_lang_profile');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/docs', 'algoritm_lang_docs');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/docs/{page:[a-zA-Z0-9_-]+}', 'algoritm_lang_docs_page');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/news', 'algoritm_lang_news');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/news/{page:[a-zA-Z0-9_-]+}', 'algoritm_lang_news_page');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/downloads', 'algoritm_lang_downloads');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/download/{page:[a-zA-Z0-9_-]+}', 'algoritm_lang_download');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}/{page:login|logout|registration|help}', 'algoritm_lang_index');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}', 'algoritm_lang');

return $router; // <--- ДОБАВИТЬ
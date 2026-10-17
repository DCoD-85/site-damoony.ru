<?PHP

require_once __DIR__ . '/Router.php';

use alogic\Router;

/** @var Router $router */
$router = new Router();

$router->add('/{lang:[a-z]{2}-[a-z]{2}}/index', 'algoritm_lang_index');
$router->add('/{lang:[a-z]{2}-[a-z]{2}}', 'algoritm_lang');

// Пример из вашего ТЗ:
// /users/ — список всех пользователей
$router->add('/users', 'algoritm_users_list');

// /users/{SameUser} — сводная информация по профилю
// Регулярка из вашего примера: [A-Z0-9]{5,30}
$router->add('/users/{login:[A-Z0-9]{5,30}}', 'algoritm_users_profile');

// /users/{SameUser}/{tab} — конкретные страницы
// Ограничиваем tab тремя значениями
$router->add('/users/{login:[A-Z0-9]{5,30}}/{tab:name|info|history|messages}', 'algoritm_users_tab');

return $router;
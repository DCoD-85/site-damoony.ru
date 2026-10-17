<?PHP

require_once __DIR__ . '/Router.php';

$router = require_once __DIR__ . '/routes.php';

$testUris = [
    '/ru-ru/index',
    '/en-en/index',
    '/ru-ru',
    '/en-en',
    '/fr-fr/index', // должно быть 404 (нет в регулярке, если ограничить)
];

foreach ($testUris as $uri) {
    $result = $router->match($uri);
    echo str_pad($uri, 30) . " -> ";
    if ($result) {
        echo "Класс: {$result['handler']} | Параметры: " . json_encode($result['params']);
    } else {
        echo "404 Not Found";
    }
    echo PHP_EOL;
}

// Добавляем фейковые классы-алгоритмы для проверки
class algoritm_users_list { public function __construct($main, $args) { echo " -> [Вызван algoritm_users_list]\n"; } }
class algoritm_users_profile { public function __construct($main, $args) { echo " -> [Вызван algoritm_users_profile с логином: {$args['login']}]\n"; } }
class algoritm_users_tab { public function __construct($main, $args) { echo " -> [Вызван algoritm_users_tab: логин {$args['login']}, таб {$args['tab']}]\n"; } }

echo "\n--- Тест инстанцирования ---\n";
foreach ($testUris as $uri) {
    $result = $router->match($uri);
    if ($result) {
        $class = $result['handler'];
        if (class_exists($class)) {
            new $class(null, $result['params']);
        } else {
            echo "Класс $class не найден!\n";
        }
    }
}
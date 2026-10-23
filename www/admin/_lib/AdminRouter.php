<?PHP

class AdminRouter {
    
    // Карта перенесенных маршрутов
    private static array $map = [
        'dashboard' => 'DashboardController',
        'domain'    => 'DomainController',
		'allias'    => 'AlliasController',
		'filler'    => 'FillerController',
        'block'     => 'BlockController',
        'blocks'    => 'BlockController',
		'page'      => 'PageController',
        'pages'     => 'PageController',
		'media'    => 'MediaController',
        'settings' => 'SettingsController', 
        'db'       => 'SettingsController',
		'schemes'  => 'SchemeController',
        'scheme'   => 'SchemeController',
        'stubs'    => 'StubController',
        'stub'     => 'StubController',
		'security' => 'SecurityController',
        // Сюда будем добавлять остальные по мере переноса
    ];

    public static function dispatch(string $p): bool {
        $parts = explode(':', $p);
        $resource = $parts[0] ?? 'dashboard';
        $action = $parts[1] ?? 'index';

        // Если роут еще не перенесен
        if (!isset(self::$map[$resource])) {
            return false;
        }

        $controllerName = self::$map[$resource];
        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
        
        $baseControllerFile = __DIR__ . '/../controllers/BaseController.php';
        if (is_file($baseControllerFile)) {
            require_once $baseControllerFile;
        }

        if (!is_file($controllerFile)) {
            return false;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            return false;
        }

        $controller = new $controllerName($p, $action);

        if (!method_exists($controller, $action)) {
            return false;
        }

        $controller->$action();
        return true;
    }
}
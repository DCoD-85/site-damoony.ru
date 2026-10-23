<?PHP

class AdminRouter {
    
    // Карта перенесенных маршрутов
    private static array $map = [
        'dashboard' => 'DashboardController',
        'domain'    => 'DomainController',
        // Сюда будем добавлять остальные по мере переноса
    ];

    public static function dispatch(string $p): void {
        $parts = explode(':', $p);
        $resource = $parts[0] ?? 'dashboard';
        $action = $parts[1] ?? 'index';

        // Если роут еще не перенесен — отдаем управление старому switch
        if (!isset(self::$map[$resource])) {
            return;
        }

        $controllerName = self::$map[$resource];
        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

        if (!is_file($controllerFile)) {
            http_response_code(500);
            exit("Controller file not found: {$controllerName}");
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            http_response_code(500);
            exit("Controller class not found: {$controllerName}");
        }

        $controller = new $controllerName($p, $action);

        if (!method_exists($controller, $action)) {
            http_response_code(404);
            exit("Action not found: {$action}");
        }

        $controller->$action();
        exit; // Контроллер сам сделает render или redirect, после этого завершаем скрипт
    }
}
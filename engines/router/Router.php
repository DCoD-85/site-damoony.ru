<?PHP

namespace router;

class Router
{
    private array $routes = [];

    /**
     * Добавить маршрут
     * 
     * @param string $pattern  Шаблон URL (например, '/users/{login:[A-Z0-9]{5,30}}')
     * @param string $handler  Имя класса-алгоритма (например, 'algoritm_users_profile')
     */
    public function add(string $pattern, string $handler): void {
        $segments = explode('/', trim($pattern, '/'));
        $regex = '';
        
        foreach ($segments as $segment) {
            $regex .= '/';
            // Если сегмент вида {name} или {name:regex}
            if (preg_match('/^\{([a-zA-Z0-9_]+)(?::(.+))?\}$/', $segment, $matches)) {
                $name = $matches[1];
                $patt = $matches[2] ?? '[^/]+';
                $regex .= "(?P<{$name}>{$patt})";
            } else {
                // Обычный текст (экранируем спецсимволы)
                $regex .= preg_quote($segment, '#');
            }
        }

        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'pattern' => $pattern,
            'regex'   => $regex,
            'handler' => $handler
        ];
    }

    /**
     * Найти обработчик по URL
     * 
     * @param string $uri
     * @return array|null ['handler' => string, 'params' => array]
     */
    public function match(string $uri): ?array
    {
        // Очищаем URI от GET-параметров и ведущих слэшей
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                return [
                    'handler' => $route['handler'],
                    'params'  => $params
                ];
            }
        }

        return null; // 404
    }
}
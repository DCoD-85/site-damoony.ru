<?PHP

abstract class BaseController {
    protected string $p;
    protected string $action;

    public function __construct(string $p, string $action) {
        $this->p = $p;
        $this->action = $action;
    }

    protected function render(string $view, array $data = []): void {
        View::render($view, $data);
    }

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    protected function post(string $key, mixed $default = ''): mixed {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = ''): mixed {
        return $_GET[$key] ?? $default;
    }

    protected function requirePost(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method not allowed');
        }
    }
}
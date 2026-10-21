<?PHP

use PHPUnit\Framework\TestCase;
use router\Router;

class RouterTest extends TestCase {
    private Router $router;

    protected function setUp(): void {
        // Подключаем автозагрузчик
        require_once dirname(__DIR__, 2) . '/engine/autoload.php';
        
        $this->router = new Router();
        $this->router->add('/users', 'algoritm_users_list');
        $this->router->add('/users/{id:[0-9]+}', 'algoritm_users_view');
        $this->router->add('/profile/{login:[A-Z]+}', 'algoritm_profile');
    }

    public function testStaticRoute() {
        $match = $this->router->match('/users');
        $this->assertNotNull($match);
        $this->assertEquals('algoritm_users_list', $match['handler']);
        $this->assertEmpty($match['params']);
    }

    public function testDynamicRouteNumeric() {
        $match = $this->router->match('/users/123');
        $this->assertNotNull($match);
        $this->assertEquals('algoritm_users_view', $match['handler']);
        $this->assertEquals('123', $match['params']['id']);
    }

    public function testDynamicRouteAlpha() {
        $match = $this->router->match('/profile/ABC');
        $this->assertNotNull($match);
        $this->assertEquals('algoritm_profile', $match['handler']);
        $this->assertEquals('ABC', $match['params']['login']);
    }

    public function testNotFound() {
        $match = $this->router->match('/profile/123'); // Ожидается [A-Z]
        $this->assertNull($match);
    }

    public function testRootMatch() {
        $router = new Router();
        $router->add('/', 'algoritm_index');
        $m = $router->match('/');
        $this->assertNotNull($m);
        $this->assertEquals('algoritm_index', $m['handler']);
        $this->assertNotNull($router->match(''));
    }
}
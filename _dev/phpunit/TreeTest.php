<?PHP

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../engine/tree.php';

class TreeTest extends TestCase {

    private function mockMain() : object {
        // В _dev/bootstrap.php загружен Stubs.php, где объявлен класс \main
        return new \main();
    }

    private function callMatch( string $segment, array $available ) : array {
        $m = new ReflectionMethod( Tree::class, 'match' );
        $m->setAccessible( true );
        return $m->invoke( new Tree( $this->mockMain() ), $segment, $available );
    }

    public function test_empty_pattern_matches_empty_segment() : void {
        [ $target, $arg ] = $this->callMatch( '', [ '' ] );
        $this->assertSame( '', $target );
        $this->assertNull( $arg );
    }

    public function test_pipe_with_empty_regex() : void {
        [ $target, $arg ] = $this->callMatch( '', [ '|list' ] );
        $this->assertSame( 'list', $target );
    }

    public function test_regex_with_param() : void {
        [ $target, $arg ] = $this->callMatch( 'dsadas', [ '[a-z0-9-]+|slug' ] );
        $this->assertSame( 'slug', $target );
        $this->assertSame( 'slug', $arg );
    }

    public function test_no_match_returns_null() : void {
        [ $target ] = $this->callMatch( 'index', [ 'about', 'contact' ] );
        $this->assertNull( $target );
    }
}
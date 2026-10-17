<?PHP

declare( strict_types = 1 );

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CasesTest extends TestCase {

    #[DataProvider( 'casesProvider' )]
    public function testCase ( string $suite , string $name , Closure $fn ) : void {
        $fn( );
        $this->assertTrue( true );
    }

    public static function casesProvider ( ) : iterable {
        $suites = Runner::discover( TEST_ROOT . '/cases' );

        foreach ( $suites as $suiteName => $suite ) {
            if ( $suite->setup !== null ) {
                ( $suite->setup )( );
            }

            foreach ( $suite->tests as $testName => $fn ) {
                yield "{$suiteName} :: {$testName}" => [
                    $suiteName ,
                    $testName ,
                    $fn ,
                ];
            }
        }
    }
}
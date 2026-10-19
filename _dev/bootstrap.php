<?PHP

declare( strict_types = 1 );

error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );

if ( ! defined( 'DS' )  ) define( 'DS'  , DIRECTORY_SEPARATOR );
if ( ! defined( 'DSA' ) ) define( 'DSA' , '/' );

$root      = dirname( __DIR__ );
$www       = $root . '/www';
$visuo_src = $root . '/visuo/sources';
$sources   = $root . '/engine';

if ( ! defined( 'VISUO_SRC' ) ) define( 'VISUO_SRC' , $visuo_src );
if ( ! defined( 'SOURCES'   ) ) define( 'SOURCES'   , $sources   );
if ( ! defined( 'TEST_ROOT' ) ) define( 'TEST_ROOT' , $www . '/tests' );

if ( ! function_exists( 'error' ) ) {
    function error ( $t , $m , $f = '' , $l = '' ) {
        throw new RuntimeException( "{$t}: {$m} ({$f}:{$l})" );
    }
}
if ( ! function_exists( 'error_file' ) ) {
    function error_file ( $p , $f , $l ) { error( 'File not found' , $p , $f , $l ); }
}

# тесты ожидают стабильные URL (без cache-buster)
if ( ! defined( 'VISUOLINK_RAND_DEFAULT' ) ) define( 'VISUOLINK_RAND_DEFAULT' , false );

# Test helpers + Stubs — ДО autoload:
# untyped class main из Stubs.php должен быть объявлен раньше,
# иначе autoloader подтянет engine/main.php с typed ?visuorelease,
# и $m->visuorelease = new fake_visuorelease() даст TypeError.
require_once $www . '/tests/lib/AssertException.php';
require_once $www . '/tests/lib/Assert.php';
require_once $www . '/tests/lib/CaseResult.php';
require_once $www . '/tests/lib/Suite.php';
require_once $www . '/tests/lib/Runner.php';
require_once $www . '/tests/lib/Stubs.php';

# Composer autoload — только для классов, которых нет в Stubs
$autoload = $root . '/_dev/vendor/autoload.php';
if ( is_file( $autoload ) ) require_once $autoload;

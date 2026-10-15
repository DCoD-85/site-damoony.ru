<?PHP
# Language tests — isolated, no main bootstrap
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );
define( 'GL_LOG' , false );
define( 'GL_DEBUG' , true );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root = dirname( __DIR__ , 2 );

# fake main with main_settings
class fake_main_settings {
	public function get ( string $key ) {
		return $key === 'language' ? 'ru-ru' : null;
	}
}

class main {
	public $main_settings;
	public function __construct ( ) {
		$this->main_settings = new fake_main_settings( );
	}
	public function __get ( $n ) { return null; }
}

require $root . '/sources/language.php';

$main = new main( );
$lang = new language( $main );

echo '<h1>Language tests</h1>';

# 1. load
echo '<h2>1. Load</h2>';
$lang->load( );
echo 'language: <b>' . htmlspecialchars( $lang->language ) . '</b><br>';
echo 'strings loaded: <b>' . count( $lang->strings ) . '</b><br>';

# 2. simple get
echo '<h2>2. Simple get (from file)</h2>';
echo 'TOP_LOGIN_LOGIN: <b>' . htmlspecialchars( $lang->get( 'TOP_LOGIN_LOGIN' , [ ] , 'Авторизация' ) ) . '</b><br>';
echo 'TOP_LOGIN_LOGOUT: <b>' . htmlspecialchars( $lang->get( 'TOP_LOGIN_LOGOUT' , [ ] , 'Выход' ) ) . '</b><br>';

# 3. get with params
echo '<h2>3. get with params (%s)</h2>';
echo 'HELLO_TEXT: <b>' . htmlspecialchars( $lang->get( 'HELLO_TEXT' , [ 'Вася' ] , 'Привет %s' ) ) . '</b><br>';
echo 'TOP_LOGIN_PROFILE: <b>' . htmlspecialchars( $lang->get( 'TOP_LOGIN_PROFILE' , [ 'admin' ] , '%s' ) ) . '</b><br>';

# 4. missing key — default
echo '<h2>4. Missing key (uses default)</h2>';
$result = $lang->get( 'NONEXISTENT_KEY_XYZ' , [ ] , 'Default value here' );
echo 'result: <b>' . htmlspecialchars( $result ) . '</b><br>';

# 5. queue
echo '<h2>5. strings_tosave (queue of missing keys)</h2>';
echo '<pre>' . htmlspecialchars( print_r( $lang->strings_tosave , true ) ) . '</pre>';

# 6. savedefaults
echo '<h2>6. savedefaults()</h2>';
$lang->savedefaults( );
echo 'saved<br>';
echo 'file exists: ' . ( is_file( $root . '/www/language/save.txt.inc' ) ? 'yes' : 'no' ) . '<br>';

# 7. show save.txt.inc
echo '<h2>7. save.txt.inc (first 30 lines)</h2>';
$save = $root . '/www/language/save.txt.inc';
if ( is_file( $save ) ) {
	$lines = file( $save );
	echo '<pre>' . htmlspecialchars( implode( '' , array_slice( $lines , 0 , 30 ) ) ) . '</pre>';
}

echo '<hr>done';
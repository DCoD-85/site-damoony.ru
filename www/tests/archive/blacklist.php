<?PHP
# Blacklist tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root = dirname( __DIR__ , 2 );

# --- fake main / main_settings ---
class fake_main_settings {
	public string $domain_real_name = 'damoony.ru';
	public string $allias_real_name = 'login';
}
class main {
	public fake_main_settings $main_settings;
	public function __construct ( ) { $this->main_settings = new fake_main_settings( ); }
	public function __get ( $n ) { return null; }
}

require $root . '/sources/blacklist.php';

# --- test subclass: intercept allow/deny ---
class test_blacklist extends blacklist {
	public array $denied  = [ ];
	public int   $allowed = 0;
	public function deny  ( string $message , int $http_code = 0 ) : void {
		$this->denied[ ] = [ 'message' => $message , 'code' => $http_code ];
	}
	public function allow ( ) : void { $this->allowed++; }
}

echo '<h1>Blacklist tests</h1>';

# --- fixtures ---
$tmp = sys_get_temp_dir( ) . DS . 'damoony2_bl_' . getmypid( ) . DS;
if ( ! is_dir( $tmp ) ) mkdir( $tmp , 0777 , true );

function fixture_write ( string $path , string $content ) : void { file_put_contents( $path , $content ); }
function fixture_cleanup ( string $dir ) : void {
	if ( ! is_dir( $dir ) ) return;
	foreach ( glob( $dir . '*' ) as $f ) if ( is_file( $f ) ) unlink( $f );
	rmdir( $dir );
}

# 1. construct
echo '<h2>1. Construct</h2>';
$b = new test_blacklist( );
echo 'main is null: <b>' . ( $b->main === null ? 'true' : 'false' ) . '</b><br>';
echo 'mode default true: <b>' . ( $b->mode === true ? 'true' : 'false' ) . '</b><br>';

# 2. blacklist — file missing → no block
echo '<h2>2. Blacklist, missing file</h2>';
$b = new test_blacklist( null , $tmp );
$_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
$b->check_blacklist( );
echo 'denied: <b>' . count( $b->denied ) . '</b> (ожидаем 0)<br>';

# 3. blacklist — IP in list → deny
echo '<h2>3. Blacklist, IP matched</h2>';
fixture_write( $tmp . 'blacklist.txt' , "10.0.0.1\n10.0.0.2\n" );
$b = new test_blacklist( null , $tmp );
$b->check_blacklist( );
echo 'denied: <b>' . count( $b->denied ) . '</b> (ожидаем 1)<br>';
echo 'message: <b>' . ( $b->denied[ 0 ][ 'message' ] ?? '-' ) . '</b><br>';
echo 'http_code: <b>' . ( $b->denied[ 0 ][ 'code' ] ?? '-' ) . '</b> (ожидаем 0, blacklist без 404)<br>';

# 4. blacklist — comments skipped
echo '<h2>4. Blacklist, comments skipped</h2>';
fixture_write( $tmp . 'blacklist.txt' , "# 10.0.0.1\n10.0.0.3\n" );
$_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
$b = new test_blacklist( null , $tmp );
$b->check_blacklist( );
echo 'denied: <b>' . count( $b->denied ) . '</b> (ожидаем 0, IP закомментирован)<br>';

# 5. whitelist — пустой аллиас → whitelist.txt, IP разрешён
echo '<h2>5. Whitelist, base file, IP allowed</h2>';
$main = new main( );
$main->main_settings->allias_real_name = '';
fixture_write( $tmp . 'whitelist.txt' , "10.0.0.1\n" );
$_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
$b = new test_blacklist( $main , $tmp );
$b->check_whitelist( );
echo 'allowed: <b>' . $b->allowed . '</b>, denied: <b>' . count( $b->denied ) . '</b> (ожидаем 1 и 0)<br>';

# 6. whitelist — с аллиасом → whitelist_<domain>_<allias>.txt, IP разрешён
echo '<h2>6. Whitelist, allias file, IP allowed</h2>';
$main = new main( );
$main->main_settings->allias_real_name = 'login';
fixture_write( $tmp . 'whitelist_damoony.ru_login.txt' , "10.0.0.1\n" );
$_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
$b = new test_blacklist( $main , $tmp );
$b->check_whitelist( );
echo 'allowed: <b>' . $b->allowed . '</b>, denied: <b>' . count( $b->denied ) . '</b> (ожидаем 1 и 0)<br>';

# 7. whitelist — IP не в списке → deny 404
echo '<h2>7. Whitelist, IP not listed</h2>';
fixture_write( $tmp . 'whitelist_damoony.ru_login.txt' , "10.0.0.1\n" );
$_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.99';
$b = new test_blacklist( $main , $tmp );
$b->check_whitelist( );
echo 'allowed: <b>' . $b->allowed . '</b>, denied: <b>' . count( $b->denied ) . '</b> (ожидаем 0 и 1)<br>';
echo 'http_code: <b>' . ( $b->denied[ 0 ][ 'code' ] ?? '-' ) . '</b> (ожидаем 404)<br>';

# 8. whitelist — файл отсутствует → не блокируем (старое поведение)
echo '<h2>8. Whitelist, missing file</h2>';
fixture_cleanup( $tmp );
mkdir( $tmp , 0777 , true );
$_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
$b = new test_blacklist( $main , $tmp );
$b->check_whitelist( );
echo 'allowed: <b>' . $b->allowed . '</b>, denied: <b>' . count( $b->denied ) . '</b> (ожидаем 0 и 0)<br>';

# 9. init → blacklist при mode = true
echo '<h2>9. init → blacklist</h2>';
fixture_write( $tmp . 'blacklist.txt' , "10.0.0.1\n" );
$_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
$b = new test_blacklist( null , $tmp );
$b->mode = true;
$b->init( );
echo 'denied: <b>' . count( $b->denied ) . '</b> (ожидаем 1)<br>';

# 10. init → whitelist при mode = false
echo '<h2>10. init → whitelist</h2>';
$main = new main( );
$main->main_settings->allias_real_name = '';
fixture_write( $tmp . 'whitelist.txt' , "10.0.0.1\n" );
$_SERVER[ 'REMOTE_ADDR' ] = '10.0.0.1';
$b = new test_blacklist( $main , $tmp );
$b->mode = false;
$b->init( );
echo 'allowed: <b>' . $b->allowed . '</b> (ожидаем 1)<br>';

# 11. нет REMOTE_ADDR → no crash
echo '<h2>11. No REMOTE_ADDR</h2>';
unset( $_SERVER[ 'REMOTE_ADDR' ] );
$b = new test_blacklist( $main , $tmp );
$b->check_blacklist( );
$b->check_whitelist( );
echo 'no crash: <b>true</b><br>';

fixture_cleanup( $tmp );

echo '<hr>done';
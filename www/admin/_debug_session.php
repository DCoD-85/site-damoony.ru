<?PHP
# временный файл — удалить после отладки

session_start( );

define( 'ADMIN_ROOT'   , __DIR__ );
define( 'PROJECT_ROOT' , dirname( dirname( ADMIN_ROOT ) ) );

header( 'Content-Type: text/plain; charset=utf-8' );

echo "session_id: " . session_id( ) . "\n";
echo "session_status: " . session_status( ) . " (2 = active)\n";
echo "session_name: " . session_name( ) . "\n";
echo "save_path: " . ini_get( 'session.save_path' ) . "\n";
echo "save_path writable: " . ( is_writable( ini_get( 'session.save_path' ) ) ? 'yes' : 'NO' ) . "\n\n";

$action = $_GET[ 'action' ] ?? 'show';

if ( $action === 'login' ) {
	$_SESSION[ 'admin_ok' ]    = true;
	$_SESSION[ 'admin_login' ] = 'admin';
	$_SESSION[ 'test_time' ]   = time( );
	echo "LOGIN: set admin_ok=true\n";
	echo "session_id: " . session_id( ) . "\n";
	echo "COOKIES after this request should contain " . session_name( ) . "=<id>\n";
}

if ( $action === 'check' ) {
	echo "SESSION content:\n";
	var_export( $_SESSION );
	echo "\n";
	echo "admin_ok = " . var_export( $_SESSION[ 'admin_ok' ] ?? null , true ) . "\n";
}

if ( $action === 'destroy' ) {
	session_destroy( );
	echo "destroyed\n";
}

echo "\n--- links ---\n";
echo "login:   " . $_SERVER[ 'REQUEST_SCHEME' ] . '://' . $_SERVER[ 'HTTP_HOST' ] . '/admin/_debug_session.php?action=login' . "\n";
echo "check:   ...?action=check\n";
echo "destroy: ...?action=destroy\n";
<?PHP
# временный файл — удалить после отладки

define( 'ADMIN_ROOT'   , __DIR__ );
define( 'PROJECT_ROOT' , dirname( dirname( ADMIN_ROOT ) ) );

header( 'Content-Type: text/plain; charset=utf-8' );

$path = PROJECT_ROOT . '/config/site.config';
echo "PROJECT_ROOT: " . PROJECT_ROOT . "\n";
echo "site.config:  " . $path . "\n";
echo "is_file:      " . ( is_file( $path ) ? 'yes' : 'NO' ) . "\n\n";

$json = @file_get_contents( $path );
if ( $json === false ) { die( "Cannot read\n" ); }

$data = json_decode( $json , true );
if ( ! is_array( $data ) ) { die( "Bad JSON: " . json_last_error_msg() . "\n" ); }

echo "admin.login:  " . var_export( $data['admin']['login'] ?? null , true ) . "\n";
echo "admin.hash:   " . var_export( substr( $data['admin']['password_hash'] ?? '' , 0 , 20 ) . '...' , true ) . "\n\n";

# проверить пароль
$pass = $_GET['pass'] ?? '';
if ( $pass !== '' ) {
	$h = $data['admin']['password_hash'] ?? '';
	echo "password_verify('{$pass}', hash): " . ( password_verify( $pass , $h ) ? 'TRUE' : 'false' ) . "\n";
	echo "hash_equals('admin', 'admin'):     " . ( hash_equals('admin', 'admin') ? 'TRUE' : 'false' ) . "\n";
}

echo "\nsession.save_path: " . ini_get('session.save_path') . "\n";
echo "session.name:      " . ini_get('session.name') . "\n";
<?PHP
# Schema tests — keep table, show columns
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );
define( 'GL_LOG' , false );
define( 'GL_DEBUG' , true );
define( 'DB_LIMIT_ONE' , 1 );
define( 'DB_LIMIT_PAGE' , 10 );
define( 'DB_LIMIT_MAX' , 1000 );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root = dirname( __DIR__ , 2 );

foreach ( [ 'driver' , 'query' , 'exception' , 'config' , 'schema' ] as $f ) {
	require $root . '/sources/db/' . $f . '.php';
}
foreach ( [ 'driver' , 'query' ] as $f ) {
	require $root . '/sources/db/mysqli/' . $f . '.php';
}
require $root . '/sources/db.php';

if ( ! class_exists( 'main' ) ) { class main { public function __get( $n ) { return null; } } }
$main = new main( );

echo '<h1>Schema tests</h1>';

$db = new db( $main );
$db->open( 'login_damoony' );

# 1. CREATE (без drop — сохраняем таблицу)
echo '<h2>1. CREATE</h2>';
$db->schema( )->create(
	table: 'schema_test' ,
	columns: [
		col( name: 'id'      , type: 'int'       , unsigned: true , auto: true , nullable: false ) ,
		col( name: 'name'    , type: 'string'    , length: 64 , nullable: false ) ,
		col( name: 'status'  , type: 'enum'      , values: [ 'active' , 'blocked' ] , default: 'active' , nullable: false ) ,
		col( name: 'created' , type: 'timestamp' , default: 'CURRENT_TIMESTAMP' ) ,
	] ,
	primary: 'id' ,
	indexes: [ [ 'name'=>'idx_name' , 'columns'=>[ 'name' ] , 'unique'=>true ] ] ,
	engine:  'InnoDB' ,
	charset: 'utf8mb4' ,
)->run( );
echo 'ok (если уже есть — не упало, IF NOT EXISTS)<br>';

# 2. EXISTS
echo '<h2>2. EXISTS</h2>';
echo 'schema_test: ' . ( $db->schema( )->exists( 'schema_test' ) ? 'yes' : 'no' ) . '<br>';
echo 'toolsusers:  ' . ( $db->schema( )->exists( 'toolsusers' )  ? 'yes' : 'no' ) . '<br>';

# 3. SHOW COLUMNS для двух таблиц
echo '<h2>3. COLUMNS</h2>';
echo '<h3>schema_test</h3>';
echo '<pre>' . htmlspecialchars( print_r( $db->schema( )->columns_of( 'schema_test' ) , true ) ) . '</pre>';
echo '<h3>toolsusers (существующая)</h3>';
echo '<pre>' . htmlspecialchars( print_r( $db->schema( )->columns_of( 'toolsusers' ) , true ) ) . '</pre>';

# 4. SHOW TABLES
echo '<h2>4. TABLES</h2>';
$tables = $db->schema( )->tables( );
echo 'Всего: ' . count( $tables ) . '<br>';
echo '<pre>' . htmlspecialchars( print_r( $tables , true ) ) . '</pre>';

echo '<hr>done. Таблица schema_test сохранена.';
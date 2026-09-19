<?PHP
# DB test — изолированный, не грузит main
# URL: https://login.damoony2.ru/tests/db.php

error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

# базовые константы
define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );
define( 'GL_LOG'       , false );
define( 'GL_DEBUG'     , true );
define( 'GL_DEBUGHARD' , false );
define( 'DB_LIMIT_ONE'  , 1 );
define( 'DB_LIMIT_PAGE' , 10 );
define( 'DB_LIMIT_MAX'  , 1000 );

# минимальные заглушки
function error ( $title , $message , $file = '' , $line = '' ) {
    echo '<div style="color:red"><b>ERROR:</b> ' . $title . ' | ' . $message . ' | ' . $file . ':' . $line . '</div>';
}
function error_file ( $path , $file , $line ) {
    error( 'File not found' , $path , $file , $line );
}

$root = dirname( __DIR__ , 2 );   # корень проекта

echo '<h1>DB tests</h1>';
echo '<p>Project root: ' . htmlspecialchars( $root ) . '</p>';

# подключаем классы
$files = [
    'sources/db/driver.php'       ,
    'sources/db/query.php'        ,
    'sources/db/exception.php'    ,
    'sources/db/config.php'       ,
    'sources/db/mysqli/driver.php',
    'sources/db/mysqli/query.php' ,
    'sources/db.php'              ,
];

foreach ( $files as $f ) {
    $path = $root . DS . str_replace( '/' , DS , $f );
    echo '<div>load: ' . htmlspecialchars( $f ) . ' ... ';
    if ( ! is_file( $path ) ) { echo '<b style="color:red">NOT FOUND</b></div>'; continue; }
    require_once $path;
    echo '<b style="color:green">OK</b></div>';
}

# minimal main stub — just enough for db to work
if ( ! class_exists( 'main' ) ) {
    class main {
        public function __get ( $name ) { return null; }
    }
}

$main = new main( );

# Тест 1: создать driver
echo '<h2>1. Create db</h2>';
try {
    $db = new db( $main );
    echo 'driver: <b>' . get_class( $db->driver ) . '</b><br>';
    echo 'db_type: <b>' . $db->db_type . '</b><br>';
    echo 'config file: <b>' . $db->config->path . '</b><br>';
} catch ( Throwable $e ) {
    echo '<b style="color:red">FAIL:</b> ' . $e->getMessage( );
    echo '<pre>' . $e->getTraceAsString( ) . '</pre>';
    exit;
}

# Тест 2: список БД
echo '<h2>2. Available databases</h2>';
foreach ( $db->config->all( ) as $name => $row ) {
    echo '- ' . $name . ' → ' . $row->dbname . '@' . $row->dbhost . '<br>';
}

# Тест 3: открыть
echo '<h2>3. Open connection</h2>';
try {
    $db->open( 'login_damoony' );
    echo 'status: <b>' . $db->driver->status( ) . '</b><br>';
} catch ( Throwable $e ) {
    echo '<b style="color:red">FAIL:</b> ' . $e->getMessage( );
    echo '<pre>' . $e->getTraceAsString( ) . '</pre>';
    exit;
}

# Тест 4: простой SELECT
echo '<h2>4. SELECT 1+1</h2>';
try {
    $rows = $db->select( 'SELECT 1 AS a , 2 AS b' )->result( );
    echo '<pre>' . htmlspecialchars( print_r( $rows , true ) ) . '</pre>';
} catch ( Throwable $e ) {
    echo '<b style="color:red">FAIL:</b> ' . $e->getMessage( );
    echo '<pre>' . $e->getTraceAsString( ) . '</pre>';
}

# Тест 5: плейсхолдеры
echo '<h2>5. SELECT with placeholders</h2>';
try {
    $rows = $db->select( 'SELECT %d AS num , %s AS txt' , [ 42 , 'hello' ] )->result( );
    echo '<pre>' . htmlspecialchars( print_r( $rows , true ) ) . '</pre>';
} catch ( Throwable $e ) {
    echo '<b style="color:red">FAIL:</b> ' . $e->getMessage( );
}

# Тест 6: несовпадение типов (dev — exception)
echo '<h2>6. Type mismatch (dev: exception)</h2>';
try {
    $rows = $db->select( 'SELECT %d AS n' , [ 'not_a_number' ] )->result( );
    echo '<b style="color:orange">NO EXCEPTION</b> result: <pre>' . htmlspecialchars( print_r( $rows , true ) ) . '</pre>';
} catch ( db_exception $e ) {
    echo '<b style="color:green">EXPECTED exception:</b> ' . htmlspecialchars( $e->getMessage( ) );
} catch ( Throwable $e ) {
    echo '<b style="color:red">OTHER:</b> ' . $e->getMessage( );
}

echo '<hr><p>done</p>';
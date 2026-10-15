<?PHP
# Render tests — isolated
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

# --- stub visuorelease: фиксируем вызовы ---
class fake_visuorelease {
	public array $calls = [ ];
	public function final      ( $log = false , $debug = false ) { $this->calls[ ] = [ 'final'      , $log , $debug ]; }
	public function final_ajax ( $log = false , $debug = false ) { $this->calls[ ] = [ 'final_ajax' , $log , $debug ]; }
}

# --- stub main ---
class main {
	public null|fake_visuorelease $visuorelease = null;
	public function __construct ( ) { $this->visuorelease = new fake_visuorelease( ); }
	public function __get ( $n ) { return null; }
}

require $root . '/sources/render.php';

echo '<h1>Render tests</h1>';

# 1. конструктор без аргументов
echo '<h2>1. Construct without args</h2>';
$r = new render( );
echo 'main is null: <b>' . ( $r->main === null ? 'true' : 'false' ) . '</b><br>';
echo 'data is render__data: <b>' . ( $r->data( ) instanceof render__data ? 'true' : 'false' ) . '</b><br>';

# 2. конструктор с main
echo '<h2>2. Construct with main</h2>';
$main = new main( );
$r    = new render( $main );
echo 'main ok: <b>' . ( $r->main === $main ? 'true' : 'false' ) . '</b><br>';

# 3. set/get min — реальное использование visuorelease
echo '<h2>3. set/get min (actual usage)</h2>';
$r->setdata( 'min' , true );
echo 'min: <b>' . var_export( $r->getdata( 'min' ) , true ) . '</b> (ожидаем true)<br>';
$r->setdata( 'min' , false );
echo 'min: <b>' . var_export( $r->getdata( 'min' ) , true ) . '</b> (ожидаем false, НЕ null)<br>';

# 4. falsy values не превращаются в null (regression fix)
echo '<h2>4. Falsy values (regression)</h2>';
$r->setdata( 'render_ajax' , false );
echo 'render_ajax: <b>' . var_export( $r->getdata( 'render_ajax' ) , true ) . '</b> (ожидаем false, не null)<br>';

# 5. неизвестный ключ
echo '<h2>5. Unknown key</h2>';
echo 'get("nope"): <b>' . var_export( $r->getdata( 'nope' ) , true ) . '</b> (ожидаем null, без warning)<br>';
$r->setdata( 'nope' , 123 ); # не должно создать динамическое свойство
echo 'get("nope") after set: <b>' . var_export( $r->getdata( 'nope' ) , true ) . '</b> (ожидаем null)<br>';

# 6. init(true) — включает render_ajax
echo '<h2>6. init() sets render_ajax</h2>';
$r->data( )->init( true );
echo 'render_ajax: <b>' . var_export( $r->getdata( 'render_ajax' ) , true ) . '</b> (ожидаем true)<br>';

# 7. release — не-AJAX путь
echo '<h2>7. release (no-ajax)</h2>';
$r->data( )->init( false );
$r->release( true , true );
$calls = $main->visuorelease->calls;
$last  = end( $calls );
echo 'last call: <b>' . $last[ 0 ] . '</b> (ожидаем final)<br>';
echo 'log=' . var_export( $last[ 1 ] , true ) . ', debug=' . var_export( $last[ 2 ] , true ) . '<br>';

# 8. release — AJAX путь
echo '<h2>8. release (ajax)</h2>';
$r->data( )->init( true );
$r->release( );
$calls = $main->visuorelease->calls;
$last  = end( $calls );
echo 'last call: <b>' . $last[ 0 ] . '</b> (ожидаем final_ajax)<br>';

# 9. release без main — не падает
echo '<h2>9. release with null main</h2>';
$r2 = new render( );
$r2->release( );
echo 'no crash: <b>true</b><br>';

# 10. release при main без visuorelease — не падает
echo '<h2>10. release with main without visuorelease</h2>';
$main->visuorelease = null;
$r->release( );
echo 'no crash: <b>true</b><br>';

echo '<hr>done';
<?PHP
# Visuostruct tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

# --- stubs ---
if ( ! class_exists( 'main' ) ) {
	class main {
		public function __get ( $n ) { return null; }
	}
}

# --- visuotag stack (needed for visuostruct_ajax releases) ---
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';

require $root_dir . '/sources/visuo/sources/visuostruct.php';

echo '<h1>Visuostruct tests</h1>';

# 1. construct without args
echo '<h2>1. Construct without args</h2>';
$s = new visuostruct( );
echo 'main null: <b>' . ( $s->main === null ? 'true' : 'false' ) . '</b><br>';
echo 'min null: <b>' . ( $s->min === null ? 'true' : 'false' ) . '</b><br>';
echo 'ajax null: <b>' . ( $s->ajax === null ? 'true' : 'false' ) . '</b><br>';

# 2. construct with main
echo '<h2>2. Construct with main</h2>';
$m = new main( );
$s = new visuostruct( $m );
echo 'main set: <b>' . ( $s->main === $m ? 'true' : 'false' ) . '</b><br>';

# 3. min() returns visuostruct_min
echo '<h2>3. min()</h2>';
$s = new visuostruct( );
$min = $s->min( );
echo 'returns visuostruct_min: <b>' . ( $min instanceof visuostruct_min ? 'true' : 'false' ) . '</b><br>';
echo 'property set: <b>' . ( $s->min === $min ? 'true' : 'false' ) . '</b><br>';

# 4. min() singleton (bugfix)
echo '<h2>4. min() singleton (bugfix)</h2>';
$m1 = $s->min( );
$m2 = $s->min( );
echo 'same instance: <b>' . ( $m1 === $m2 ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 5. ajax() returns visuostruct_ajax
echo '<h2>5. ajax()</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
echo 'returns visuostruct_ajax: <b>' . ( $ajax instanceof visuostruct_ajax ? 'true' : 'false' ) . '</b><br>';
echo 'property set: <b>' . ( $s->ajax === $ajax ? 'true' : 'false' ) . '</b><br>';

# 6. ajax() singleton (bugfix)
echo '<h2>6. ajax() singleton (bugfix)</h2>';
$a1 = $s->ajax( );
$a2 = $s->ajax( );
echo 'same instance: <b>' . ( $a1 === $a2 ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 7. min and ajax independent
echo '<h2>7. min / ajax independent</h2>';
$s = new visuostruct( );
$min = $s->min( );
$ajax = $s->ajax( );
echo 'min is not ajax: <b>' . ( $min !== $ajax ? 'true' : 'false' ) . '</b><br>';
echo 'both set: <b>' . ( $s->min !== null && $s->ajax !== null ? 'true' : 'false' ) . '</b><br>';

# 8. min backref
echo '<h2>8. min backref</h2>';
$s = new visuostruct( );
$min = $s->min( );
echo 'min->visuostruct is $s: <b>' . ( $min->visuostruct === $s ? 'true' : 'false' ) . '</b><br>';

# 9. ajax backref
echo '<h2>9. ajax backref</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
echo 'ajax->visuostruct is $s: <b>' . ( $ajax->visuostruct === $s ? 'true' : 'false' ) . '</b><br>';

# 10. min sub-elements accessible
echo '<h2>10. min sub-elements</h2>';
$s = new visuostruct( );
$min = $s->min( );
echo 'error() returns visuostruct_min_error: <b>' . ( $min->error( ) instanceof visuostruct_min_error ? 'true' : 'false' ) . '</b><br>';
echo 'messages() returns visuostruct_min_messages: <b>' . ( $min->messages( ) instanceof visuostruct_min_messages ? 'true' : 'false' ) . '</b><br>';
echo 'release() returns visuostruct_min_release: <b>' . ( $min->release( ) instanceof visuostruct_min_release ? 'true' : 'false' ) . '</b><br>';
echo 'form() returns visuostruct_min_form: <b>' . ( $min->form( ) instanceof visuostruct_min_form ? 'true' : 'false' ) . '</b><br>';
echo 'referer() returns visuostruct_min_referer: <b>' . ( $min->referer( ) instanceof visuostruct_min_referer ? 'true' : 'false' ) . '</b><br>';

# 11. min sub-singleton
echo '<h2>11. min sub-singleton</h2>';
$s = new visuostruct( );
$min = $s->min( );
$e1 = $min->error( );
$e2 = $min->error( );
echo 'same error instance: <b>' . ( $e1 === $e2 ? 'true' : 'false' ) . '</b><br>';

# 12. error set / release
echo '<h2>12. error set / release</h2>';
$s = new visuostruct( );
$min = $s->min( );
$min->error( )->set( true );
echo 'error release: <b>' . var_export( $min->error( )->release( ) , true ) . '</b> (ожидаем true)<br>';

# 13. form set / release
echo '<h2>13. form set / release</h2>';
$s = new visuostruct( );
$min = $s->min( );
$min->form( )->set( reset: true , redirect: '/next' , redirect_time: 5 );
$form = $min->form( )->release( );
echo 'reset: <b>' . var_export( $form[ 'reset' ] , true ) . '</b><br>';
echo 'redirect: <b>' . htmlspecialchars( (string) $form[ 'redirect' ] ) . '</b><br>';
echo 'redirect_time: <b>' . var_export( $form[ 'redirect_time' ] , true ) . '</b><br>';

# 14. referer default (HTTP_REFERER)
echo '<h2>14. referer default</h2>';
$_SERVER[ 'HTTP_REFERER' ] = 'https://example.com/prev';
$s = new visuostruct( );
$min = $s->min( );
echo 'referer: <b>' . htmlspecialchars( (string) $min->referer( )->release( ) ) . '</b> (ожидаем https://example.com/prev)<br>';

# 15. referer set
echo '<h2>15. referer set</h2>';
$s = new visuostruct( );
$min = $s->min( );
$min->referer( )->set( '/custom' );
echo 'referer: <b>' . htmlspecialchars( (string) $min->referer( )->release( ) ) . '</b> (ожидаем /custom)<br>';

# 16. ajax as_json empty
echo '<h2>16. ajax as_json empty</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
$json = $ajax->as_json( );
$data = json_decode( $json , true );
echo 'valid json: <b>' . ( $data !== null ? 'true' : 'false' ) . '</b><br>';
echo 'has error key: <b>' . ( isset( $data[ 'error' ] ) ? 'true' : 'false' ) . '</b><br>';
echo 'has layers: <b>' . ( isset( $data[ 'layers' ] ) ? 'true' : 'false' ) . '</b><br>';
echo 'error value: <b>' . var_export( $data[ 'error' ] , true ) . '</b> (ожидаем false)<br>';

# 17. ajax error set
echo '<h2>17. ajax error set</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
$ajax->error = true;
$data = json_decode( $ajax->as_json( ) , true );
echo 'error value: <b>' . var_export( $data[ 'error' ] , true ) . '</b> (ожидаем true)<br>';

# 18. ajax address
echo '<h2>18. ajax address</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
$ajax->address( )->set( 'https://example.com/page' );
$data = json_decode( $ajax->as_json( ) , true );
echo 'address: <b>' . htmlspecialchars( (string) $data[ 'address' ] ) . '</b> (ожидаем https://example.com/page)<br>';

# 19. ajax top layer
echo '<h2>19. ajax top layer</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
$vt = new visuotag( );
$vt->tag( 'div' )->content( 'top content' );
$ajax->top( )->set( $vt );
$data = json_decode( $ajax->as_json( ) , true );
echo 'top layer id: <b>' . htmlspecialchars( (string) $data[ 'layers' ][ 'top' ][ 'layer' ] ) . '</b> (ожидаем dt_top)<br>';
echo 'top html contains content: <b>' . ( strpos( (string) $data[ 'layers' ][ 'top' ][ 'html' ] , 'top content' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 20. ajax header release
echo '<h2>20. ajax header release</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
$vt = new visuotag( );
$vt->tag( 'title' )->content( 'Header Title' );
$ajax->header( )->set( $vt );
$data = json_decode( $ajax->as_json( ) , true );
echo 'header html contains title: <b>' . ( strpos( (string) $data[ 'layers' ][ 'header' ] , 'Header Title' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 21. ajax all layers null — нет ошибок
echo '<h2>21. ajax all layers null</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
$data = json_decode( $ajax->as_json( ) , true );
echo 'header null: <b>' . ( $data[ 'layers' ][ 'header' ] === null ? 'true' : 'false' ) . '</b><br>';
echo 'top null: <b>' . ( $data[ 'layers' ][ 'top' ] === null ? 'true' : 'false' ) . '</b><br>';
echo 'bottom null: <b>' . ( $data[ 'layers' ][ 'bottom' ] === null ? 'true' : 'false' ) . '</b><br>';

# 22. ajax singleton для слоёв
echo '<h2>22. ajax layer singleton</h2>';
$s = new visuostruct( );
$ajax = $s->ajax( );
$t1 = $ajax->top( );
$t2 = $ajax->top( );
echo 'same top instance: <b>' . ( $t1 === $t2 ? 'true' : 'false' ) . '</b><br>';

# 23. min + ajax coexist
echo '<h2>23. min + ajax coexist</h2>';
$s = new visuostruct( );
$min = $s->min( );
$ajax = $s->ajax( );
echo 'min not null: <b>' . ( $min !== null ? 'true' : 'false' ) . '</b><br>';
echo 'ajax not null: <b>' . ( $ajax !== null ? 'true' : 'false' ) . '</b><br>';
echo 'same struct backref: <b>' . ( $min->visuostruct === $ajax->visuostruct ? 'true' : 'false' ) . '</b><br>';

echo '<hr>done';
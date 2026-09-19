<?PHP
# Visuostruct_min tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

# --- stubs ---
if ( ! class_exists( 'visuostruct' ) ) {
	class visuostruct {
		public null|main $main = null;
		public function __construct ( $main = null ) { $this->main = $main; }
	}
}
if ( ! class_exists( 'main' ) ) {
	class main {
		public function __get ( $n ) { return null; }
	}
}

require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';
require $root_dir . '/sources/visuo/sources/visuostruct/visuostruct_min.php';

echo '<h1>Visuostruct_min tests</h1>';

# 1. construct
echo '<h2>1. Construct</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
echo 'visuostruct set: <b>' . ( $min->visuostruct === $vs ? 'true' : 'false' ) . '</b><br>';
echo 'error null: <b>' . ( $min->error === null ? 'true' : 'false' ) . '</b><br>';

# 2. error factory
echo '<h2>2. error()</h2>';
$e = $min->error( );
echo 'returns visuostruct_min_error: <b>' . ( $e instanceof visuostruct_min_error ? 'true' : 'false' ) . '</b><br>';
echo 'property set: <b>' . ( $min->error === $e ? 'true' : 'false' ) . '</b><br>';
echo 'backref visuostruct: <b>' . ( $e->visuostruct === $vs ? 'true' : 'false' ) . '</b><br>';
echo 'backref visuostruct_min: <b>' . ( $e->visuostruct_min === $min ? 'true' : 'false' ) . '</b><br>';

# 3. error singleton
echo '<h2>3. error singleton</h2>';
$e2 = $min->error( );
echo 'same instance: <b>' . ( $e === $e2 ? 'true' : 'false' ) . '</b><br>';

# 4. error set/release
echo '<h2>4. error set / release</h2>';
$e->set( true );
echo 'release true: <b>' . var_export( $e->release( ) , true ) . '</b><br>';
$e->set( false );
echo 'release false: <b>' . var_export( $e->release( ) , true ) . '</b><br>';

# 5. messages factory + release empty (bugfix)
echo '<h2>5. messages empty (bugfix)</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$msg = $min->messages( );
echo 'returns visuostruct_min_messages: <b>' . ( $msg instanceof visuostruct_min_messages ? 'true' : 'false' ) . '</b><br>';
echo 'release empty (no fatal): <b>' . htmlspecialchars( $msg->release( ) ) . '</b> (ожидаем пусто)<br>';

# 6. messages set + release
echo '<h2>6. messages set + release</h2>';
$vt = new visuotag( );
$vt->tag( 'div' )->content( 'hello' );
$msg->set( $vt );
$out = $msg->release( );
echo 'release: <b>' . htmlspecialchars( $out ) . '</b><br>';
echo 'contains hello: <b>' . ( strpos( $out , 'hello' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 7. release factory + empty (bugfix)
echo '<h2>7. release empty (bugfix)</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$rel = $min->release( );
echo 'returns visuostruct_min_release: <b>' . ( $rel instanceof visuostruct_min_release ? 'true' : 'false' ) . '</b><br>';
$data = $rel->release( );
echo 'html empty: <b>' . htmlspecialchars( $data[ 'html' ] ) . '</b> (ожидаем пусто, не fatal)<br>';
echo 'layer empty: <b>' . htmlspecialchars( $data[ 'layer' ] ) . '</b><br>';
echo 'popup false: <b>' . var_export( $data[ 'popup' ] , true ) . '</b><br>';

# 8. release set + release
echo '<h2>8. release set</h2>';
$vt = new visuotag( );
$vt->tag( 'div' )->content( 'content' );
$rel->set( $vt , 'dt_center' , true , true , false );
$data = $rel->release( );
echo 'html contains content: <b>' . ( strpos( $data[ 'html' ] , 'content' ) !== false ? 'true' : 'false' ) . '</b><br>';
echo 'layer: <b>' . htmlspecialchars( $data[ 'layer' ] ) . '</b> (ожидаем dt_center)<br>';
echo 'popup: <b>' . var_export( $data[ 'popup' ] , true ) . '</b><br>';
echo 'popup_back: <b>' . var_export( $data[ 'popup_back' ] , true ) . '</b><br>';
echo 'popup_navigate: <b>' . var_export( $data[ 'popup_navigate' ] , true ) . '</b><br>';

# 9. form factory + empty
echo '<h2>9. form empty</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$form = $min->form( );
echo 'returns visuostruct_min_form: <b>' . ( $form instanceof visuostruct_min_form ? 'true' : 'false' ) . '</b><br>';
$data = $form->release( );
echo 'reset default: <b>' . var_export( $data[ 'reset' ] , true ) . '</b> (ожидаем false)<br>';
echo 'redirect default: <b>' . var_export( $data[ 'redirect' ] , true ) . '</b> (ожидаем null)<br>';

# 10. form set
echo '<h2>10. form set</h2>';
$form->set( reset: true , redirect: '/next' , redirect_time: 5 );
$data = $form->release( );
echo 'reset: <b>' . var_export( $data[ 'reset' ] , true ) . '</b><br>';
echo 'redirect: <b>' . htmlspecialchars( (string) $data[ 'redirect' ] ) . '</b><br>';
echo 'redirect_time: <b>' . var_export( $data[ 'redirect_time' ] , true ) . '</b><br>';

# 11. referer factory
echo '<h2>11. referer empty</h2>';
unset( $_SERVER[ 'HTTP_REFERER' ] );
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$ref = $min->referer( );
echo 'returns visuostruct_min_referer: <b>' . ( $ref instanceof visuostruct_min_referer ? 'true' : 'false' ) . '</b><br>';
echo 'release: <b>' . var_export( $ref->release( ) , true ) . '</b> (ожидаем null)<br>';

# 12. referer from HTTP_REFERER
echo '<h2>12. referer from server</h2>';
$_SERVER[ 'HTTP_REFERER' ] = 'https://example.com/prev';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
echo 'release: <b>' . htmlspecialchars( (string) $min->referer( )->release( ) ) . '</b> (ожидаем https://example.com/prev)<br>';

# 13. referer set
echo '<h2>13. referer set</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$min->referer( )->set( '/custom' );
echo 'release: <b>' . htmlspecialchars( (string) $min->referer( )->release( ) ) . '</b> (ожидаем /custom)<br>';

# 14. referer falsy regression ('0')
echo '<h2>14. referer falsy (regression)</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$min->referer( )->set( '0' );
echo 'release: <b>' . var_export( $min->referer( )->release( ) , true ) . '</b> (ожидаем "0", не null)<br>';

# 15. referer set null falls back to server
echo '<h2>15. referer set null</h2>';
$_SERVER[ 'HTTP_REFERER' ] = 'https://from-server.example';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$min->referer( )->set( null );
echo 'release: <b>' . htmlspecialchars( (string) $min->referer( )->release( ) ) . '</b> (ожидаем https://from-server.example)<br>';

# 16. all factories keep state
echo '<h2>16. all factories keep state</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$e1 = $min->error( );
$m1 = $min->messages( );
$r1 = $min->release( );
$f1 = $min->form( );
$ref1 = $min->referer( );
echo 'error identity: <b>' . ( $min->error( ) === $e1 ? 'true' : 'false' ) . '</b><br>';
echo 'messages identity: <b>' . ( $min->messages( ) === $m1 ? 'true' : 'false' ) . '</b><br>';
echo 'release identity: <b>' . ( $min->release( ) === $r1 ? 'true' : 'false' ) . '</b><br>';
echo 'form identity: <b>' . ( $min->form( ) === $f1 ? 'true' : 'false' ) . '</b><br>';
echo 'referer identity: <b>' . ( $min->referer( ) === $ref1 ? 'true' : 'false' ) . '</b><br>';

# 17. all elements inherit base
echo '<h2>17. inheritance</h2>';
echo 'error instanceof base: <b>' . ( $e1 instanceof visuostruct_min_element ? 'true' : 'false' ) . '</b><br>';
echo 'messages instanceof base: <b>' . ( $m1 instanceof visuostruct_min_element ? 'true' : 'false' ) . '</b><br>';
echo 'release instanceof base: <b>' . ( $r1 instanceof visuostruct_min_element ? 'true' : 'false' ) . '</b><br>';
echo 'form instanceof base: <b>' . ( $f1 instanceof visuostruct_min_element ? 'true' : 'false' ) . '</b><br>';
echo 'referer instanceof base: <b>' . ( $ref1 instanceof visuostruct_min_element ? 'true' : 'false' ) . '</b><br>';

# 18. constructor without visuostruct
echo '<h2>18. construct without visuostruct</h2>';
$min = new visuostruct_min( );
echo 'no crash: <b>true</b><br>';
echo 'visuostruct null: <b>' . ( $min->visuostruct === null ? 'true' : 'false' ) . '</b><br>';
echo 'error backref null: <b>' . ( $min->error( )->visuostruct === null ? 'true' : 'false' ) . '</b><br>';

# 19. messages release idempotent
echo '<h2>19. messages release idempotent</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$vt = new visuotag( );
$vt->tag( 'p' )->content( 'x' );
$min->messages( )->set( $vt );
$h1 = $min->messages( )->release( );
$h2 = $min->messages( )->release( );
echo 'identical: <b>' . ( $h1 === $h2 ? 'true' : 'false' ) . '</b><br>';

# 20. release release idempotent
echo '<h2>20. release idempotent</h2>';
$vs = new visuostruct( );
$min = new visuostruct_min( $vs );
$vt = new visuotag( );
$vt->tag( 'p' )->content( 'x' );
$min->release( )->set( $vt , 'dt_test' );
$r1 = $min->release( )->release( );
$r2 = $min->release( )->release( );
echo 'identical html: <b>' . ( $r1[ 'html' ] === $r2[ 'html' ] ? 'true' : 'false' ) . '</b><br>';

echo '<hr>done';
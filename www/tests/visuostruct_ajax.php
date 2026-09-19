<?PHP
# Visuostruct_ajax tests — isolated
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

require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';
require $root_dir . '/sources/visuo/sources/visuostruct/visuostruct_ajax.php';

echo '<h1>Visuostruct_ajax tests</h1>';

# 1. construct
echo '<h2>1. Construct</h2>';
$vs = new visuostruct( );
$ajax = new visuostruct_ajax( $vs );
echo 'visuostruct set: <b>' . ( $ajax->visuostruct === $vs ? 'true' : 'false' ) . '</b><br>';
echo 'error default false: <b>' . var_export( $ajax->error , true ) . '</b><br>';
echo 'all layers null: <b>' . ( $ajax->address === null && $ajax->header === null && $ajax->top === null ? 'true' : 'false' ) . '</b><br>';

# 2. address factory
echo '<h2>2. address()</h2>';
$addr = $ajax->address( );
echo 'returns visuostruct_ajax_address: <b>' . ( $addr instanceof visuostruct_ajax_address ? 'true' : 'false' ) . '</b><br>';
echo 'property set: <b>' . ( $ajax->address === $addr ? 'true' : 'false' ) . '</b><br>';
echo 'backref visuostruct_ajax: <b>' . ( $addr->visuostruct_ajax === $ajax ? 'true' : 'false' ) . '</b><br>';
echo 'backref visuostruct: <b>' . ( $addr->visuostruct === $vs ? 'true' : 'false' ) . '</b><br>';

# 3. address set/release
echo '<h2>3. address set / release</h2>';
$addr->set( 'https://example.com/page' );
echo 'release: <b>' . htmlspecialchars( (string) $addr->release( ) ) . '</b><br>';
echo 'astag: <b>' . htmlspecialchars( (string) $addr->astag( ) ) . '</b><br>';

# 4. header factory + set + release
echo '<h2>4. header</h2>';
$ajax = new visuostruct_ajax( );
$vt = new visuotag( );
$vt->tag( 'title' )->content( 'Header Title' );
$ajax->header( )->set( $vt );
$data = json_decode( $ajax->as_json( ) , true );
echo 'header html contains title: <b>' . ( strpos( (string) $data[ 'layers' ][ 'header' ] , 'Header Title' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 5. top layer (dt_top)
echo '<h2>5. top layer</h2>';
$ajax = new visuostruct_ajax( );
$vt = new visuotag( );
$vt->tag( 'div' )->content( 'top' );
$ajax->top( )->set( $vt );
$data = json_decode( $ajax->as_json( ) , true );
echo 'layer: <b>' . htmlspecialchars( (string) $data[ 'layers' ][ 'top' ][ 'layer' ] ) . '</b> (ожидаем dt_top)<br>';
echo 'html contains top: <b>' . ( strpos( (string) $data[ 'layers' ][ 'top' ][ 'html' ] , 'top' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 6. left layer — NEW (было fatal)
echo '<h2>6. left layer — NEW (bugfix)</h2>';
$ajax = new visuostruct_ajax( );
$vt = new visuotag( );
$vt->tag( 'div' )->content( 'left content' );
$left = $ajax->left( );
echo 'returns visuostruct_ajax_left: <b>' . ( $left instanceof visuostruct_ajax_left ? 'true' : 'false' ) . '</b><br>';
$left->set( $vt );
$data = json_decode( $ajax->as_json( ) , true );
echo 'layer: <b>' . htmlspecialchars( (string) $data[ 'layers' ][ 'left' ][ 'layer' ] ) . '</b> (ожидаем dt_left)<br>';
echo 'html contains left content: <b>' . ( strpos( (string) $data[ 'layers' ][ 'left' ][ 'html' ] , 'left content' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 7. right layer — NEW (было fatal)
echo '<h2>7. right layer — NEW (bugfix)</h2>';
$ajax = new visuostruct_ajax( );
$right = $ajax->right( );
echo 'returns visuostruct_ajax_right: <b>' . ( $right instanceof visuostruct_ajax_right ? 'true' : 'false' ) . '</b><br>';
$vt = new visuotag( );
$vt->tag( 'div' )->content( 'right content' );
$right->set( $vt );
$data = json_decode( $ajax->as_json( ) , true );
echo 'layer: <b>' . htmlspecialchars( (string) $data[ 'layers' ][ 'right' ][ 'layer' ] ) . '</b> (ожидаем dt_right)<br>';

# 8. center layer
echo '<h2>8. center layer</h2>';
$ajax = new visuostruct_ajax( );
$ajax->center( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'center' ) );
$data = json_decode( $ajax->as_json( ) , true );
echo 'layer: <b>' . htmlspecialchars( (string) $data[ 'layers' ][ 'center' ][ 'layer' ] ) . '</b> (ожидаем dt_center)<br>';

# 9. bottom layer
echo '<h2>9. bottom layer</h2>';
$ajax = new visuostruct_ajax( );
$ajax->bottom( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'bottom' ) );
$data = json_decode( $ajax->as_json( ) , true );
echo 'layer: <b>' . htmlspecialchars( (string) $data[ 'layers' ][ 'bottom' ][ 'layer' ] ) . '</b> (ожидаем dt_bottom)<br>';

# 10. all layers at once
echo '<h2>10. all layers</h2>';
$ajax = new visuostruct_ajax( );
$ajax->header( )->set( ( new visuotag( ) )->tag( 'title' )->content( 'H' ) );
$ajax->top( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'T' ) );
$ajax->left( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'L' ) );
$ajax->center( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'C' ) );
$ajax->right( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'R' ) );
$ajax->bottom( )->set( ( new visuotag( ) )->tag( 'div' )->content( 'B' ) );
$json = $ajax->as_json( );
$data = json_decode( $json , true );
echo 'valid json: <b>' . ( $data !== null ? 'true' : 'false' ) . '</b><br>';
echo 'header not null: <b>' . ( $data[ 'layers' ][ 'header' ] !== null ? 'true' : 'false' ) . '</b><br>';
echo 'left not null: <b>' . ( $data[ 'layers' ][ 'left' ] !== null ? 'true' : 'false' ) . '</b><br>';
echo 'right not null: <b>' . ( $data[ 'layers' ][ 'right' ] !== null ? 'true' : 'false' ) . '</b><br>';

# 11. error set
echo '<h2>11. error set</h2>';
$ajax = new visuostruct_ajax( );
$ajax->error = true;
$data = json_decode( $ajax->as_json( ) , true );
echo 'error: <b>' . var_export( $data[ 'error' ] , true ) . '</b> (ожидаем true)<br>';

# 12. layer without set — release null
echo '<h2>12. layer without set</h2>';
$ajax = new visuostruct_ajax( );
$ajax->top( ); # создаём, но не set
$data = json_decode( $ajax->as_json( ) , true );
echo 'top null: <b>' . ( $data[ 'layers' ][ 'top' ] === null ? 'true' : 'false' ) . '</b><br>';

# 13. layer release guards (bugfix)
echo '<h2>13. layer release guards</h2>';
$ajax = new visuostruct_ajax( );
$top = $ajax->top( );
echo 'release без set: <b>' . var_export( $top->release( ) , true ) . '</b> (ожидаем null, не fatal)<br>';

# 14. ajax layer singleton
echo '<h2>14. layer singleton</h2>';
$ajax = new visuostruct_ajax( );
$t1 = $ajax->top( );
$t2 = $ajax->top( );
echo 'same instance: <b>' . ( $t1 === $t2 ? 'true' : 'false' ) . '</b><br>';

# 15. all layers are separate objects
echo '<h2>15. all layers separate</h2>';
$ajax = new visuostruct_ajax( );
$all = [ $ajax->top( ) , $ajax->left( ) , $ajax->center( ) , $ajax->right( ) , $ajax->bottom( ) ];
$ids = array_map( 'spl_object_id' , $all );
echo 'all unique: <b>' . ( count( array_unique( $ids ) ) === count( $all ) ? 'true' : 'false' ) . '</b><br>';

# 16. layer instanceof base
echo '<h2>16. layer instanceof base</h2>';
$ajax = new visuostruct_ajax( );
echo 'top instanceof layer: <b>' . ( $ajax->top( ) instanceof visuostruct_ajax_layer ? 'true' : 'false' ) . '</b><br>';
echo 'left instanceof layer: <b>' . ( $ajax->left( ) instanceof visuostruct_ajax_layer ? 'true' : 'false' ) . '</b><br>';
echo 'right instanceof layer: <b>' . ( $ajax->right( ) instanceof visuostruct_ajax_layer ? 'true' : 'false' ) . '</b><br>';
echo 'top instanceof element: <b>' . ( $ajax->top( ) instanceof visuostruct_ajax_element ? 'true' : 'false' ) . '</b><br>';

# 17. address instanceof base
echo '<h2>17. address instanceof base</h2>';
$ajax = new visuostruct_ajax( );
echo 'address instanceof element: <b>' . ( $ajax->address( ) instanceof visuostruct_ajax_element ? 'true' : 'false' ) . '</b><br>';
echo 'header instanceof element: <b>' . ( $ajax->header( ) instanceof visuostruct_ajax_element ? 'true' : 'false' ) . '</b><br>';

# 18. json structure
echo '<h2>18. json structure</h2>';
$ajax = new visuostruct_ajax( );
$data = json_decode( $ajax->as_json( ) , true );
echo 'has error: <b>' . ( array_key_exists( 'error' , $data ) ? 'true' : 'false' ) . '</b><br>';
echo 'has address: <b>' . ( array_key_exists( 'address' , $data ) ? 'true' : 'false' ) . '</b><br>';
echo 'has layers: <b>' . ( array_key_exists( 'layers' , $data ) ? 'true' : 'false' ) . '</b><br>';
echo 'layers has 6 keys: <b>' . count( $data[ 'layers' ] ) . '</b> (ожидаем 6)<br>';
echo 'layer keys: <b>' . implode( ',' , array_keys( $data[ 'layers' ] ) ) . '</b><br>';

# 19. JSON_UNESCAPED_UNICODE
echo '<h2>19. JSON_UNESCAPED_UNICODE</h2>';
$ajax = new visuostruct_ajax( );
$vt = new visuotag( );
$vt->tag( 'div' )->content( 'Привет' );
$ajax->center( )->set( $vt );
$json = $ajax->as_json( );
echo 'contains Привет as text: <b>' . ( strpos( $json , 'Привет' ) !== false ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 20. json empty (no error)
echo '<h2>20. json empty</h2>';
$ajax = new visuostruct_ajax( );
$json = $ajax->as_json( );
echo 'valid json: <b>' . ( json_decode( $json ) !== null ? 'true' : 'false' ) . '</b><br>';
echo 'not "{}": <b>' . ( $json !== '{}' ? 'true' : 'false' ) . '</b><br>';

# 21. address backref chain
echo '<h2>21. address backref chain</h2>';
$vs = new visuostruct( );
$ajax = new visuostruct_ajax( $vs );
$addr = $ajax->address( );
echo 'addr->visuostruct === vs: <b>' . ( $addr->visuostruct === $vs ? 'true' : 'false' ) . '</b><br>';
echo 'addr->visuostruct_ajax === ajax: <b>' . ( $addr->visuostruct_ajax === $ajax ? 'true' : 'false' ) . '</b><br>';

# 22. constructor without visuostruct
echo '<h2>22. construct without visuostruct</h2>';
$ajax = new visuostruct_ajax( );
echo 'no crash: <b>true</b><br>';
echo 'visuostruct null: <b>' . ( $ajax->visuostruct === null ? 'true' : 'false' ) . '</b><br>';
echo 'address backref null: <b>' . ( $ajax->address( )->visuostruct === null ? 'true' : 'false' ) . '</b><br>';

echo '<hr>done';
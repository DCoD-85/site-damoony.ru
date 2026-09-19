<?PHP
# Visuoattrsdata tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root = dirname( __DIR__ , 2 );

# --- stub visuoattrs (real one not needed for data test) ---
if ( ! class_exists( 'visuoattrs' ) ) {
	class visuoattrs {
		public function __construct ( ...$args ) {}
	}
}

require $root . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';

echo '<h1>Visuoattrsdata tests</h1>';

# 1. empty
echo '<h2>1. Empty</h2>';
$d = new visuoattrsdata( );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (ожидаем пусто)<br>';

# 2. add string (no json)
echo '<h2>2. Add string (no json)</h2>';
$d = new visuoattrsdata( );
$d->add( 'role' , 'admin' , false );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (ожидаем data-role="admin")<br>';

# 3. add string (json)
echo '<h2>3. Add string (json)</h2>';
$d = new visuoattrsdata( );
$d->add( 'role' , 'admin' , true );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (кавычки JSON экранированы)<br>';

# 4. add int (json)
echo '<h2>4. Add int (json)</h2>';
$d = new visuoattrsdata( );
$d->add( 'count' , 5 );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (ожидаем data-count="5")<br>';

# 5. add array (json)
echo '<h2>5. Add array (json)</h2>';
$d = new visuoattrsdata( );
$d->add( 'info' , [ 'a' => 1 , 'b' => 'x' ] );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b><br>';

# 6. escaping (XSS)
echo '<h2>6. Escaping</h2>';
$d = new visuoattrsdata( );
$d->add( 'xss' , '"><script>alert(1)</script>' , false );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b><br>';
echo 'должно быть экранировано без сырых <b>&lt;script&gt;</b><br>';

# 7. get
echo '<h2>7. Get</h2>';
$d = new visuoattrsdata( );
$d->add( 'role' , 'admin' , false );
$d->add( 'info' , [ 'a' => 1 ] , true );
echo 'get role: <b>' . var_export( $d->get( 'role' ) , true ) . '</b> (ожидаем admin)<br>';
echo 'get info: <b>' . htmlspecialchars( var_export( $d->get( 'info' ) , true ) ) . '</b> (ожидаем [a=1])<br>';
echo 'get nope: <b>' . var_export( $d->get( 'nope' ) , true ) . '</b> (ожидаем null)<br>';

# 8. has
echo '<h2>8. Has</h2>';
echo 'has role: <b>' . ( $d->has( 'role' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'has nope: <b>' . ( $d->has( 'nope' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 9. remove
echo '<h2>9. Remove</h2>';
$d->remove( 'role' );
echo 'after remove: <b>' . htmlspecialchars( $d->release( ) ) . '</b><br>';
echo 'has role: <b>' . ( $d->has( 'role' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 10. clear
echo '<h2>10. Clear</h2>';
$d->clear( );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (ожидаем пусто)<br>';

# 11. overwrite
echo '<h2>11. Overwrite</h2>';
$d = new visuoattrsdata( );
$d->add( 'x' , 'a' , false )->add( 'x' , 'b' , false );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (ожидаем b)<br>';

# 12. Cyrillic
echo '<h2>12. Cyrillic</h2>';
$d = new visuoattrsdata( );
$d->add( 'msg' , [ 'text' => 'Привет' ] );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (ожидаем читаемое Привет)<br>';

# 13. owner
echo '<h2>13. Owner</h2>';
$va = new visuoattrs( );
$d = new visuoattrsdata( $va );
echo 'owner is visuoattrs: <b>' . ( $d->owner( ) === $va ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 14. null value
echo '<h2>14. Null value</h2>';
$d = new visuoattrsdata( );
$d->add( 'x' , null , true );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (ожидаем data-x="null")<br>';

# 15. false / zero
echo '<h2>15. False / zero</h2>';
$d = new visuoattrsdata( );
$d->add( 'a' , false , true );
$d->add( 'b' , 0 , true );
echo 'release: <b>' . htmlspecialchars( $d->release( ) ) . '</b> (ожидаем false и 0)<br>';

# 16. encode/decode round-trip
echo '<h2>16. encode/decode</h2>';
$arr = [ 'a' => 1 , 'b' => 'Привет' ];
$enc = visuoattrsdata::encode( $arr );
$dec = visuoattrsdata::decode( $enc );
echo 'encoded: <b>' . htmlspecialchars( $enc ) . '</b><br>';
echo 'decoded equals original: <b>' . ( $dec === $arr ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

echo '<hr>done';
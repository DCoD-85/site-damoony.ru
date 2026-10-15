<?PHP
# Visuoattrsactions tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root = dirname( __DIR__ , 2 );

# --- stubs ---
if ( ! class_exists( 'visuotag' ) ) {
	class visuotag {
		public string $tagname = 'div';
		public function __construct ( ...$args ) {}
	}
}
if ( ! class_exists( 'visuoattrs' ) ) {
	class visuoattrs {
		private null|visuotag $visuotag;
		public function __construct ( null|visuotag $visuotag = null ) { $this->visuotag = $visuotag; }
		public function visuotag ( ) { return $this->visuotag; }
	}
}

require $root . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';

echo '<h1>Visuoattrsactions tests</h1>';

$vt = new visuotag( );
$va = new visuoattrs( $vt );

# 1. empty
echo '<h2>1. Empty</h2>';
$a = new visuoattrsactions( $va );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем пусто)<br>';

# 2. add one
echo '<h2>2. Add one</h2>';
$a->add( 'onclick' , 'alert(1)' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем onclick="alert(1)")<br>';

# 3. add many
echo '<h2>3. Add many</h2>';
$a->add( 'onmouseover' , 'hi()' );
$a->add( 'onsubmit' , 'return false' );
echo 'all: <b>' . htmlspecialchars( json_encode( $a->all( ) ) ) . '</b><br>';

# 4. overwrite
echo '<h2>4. Overwrite</h2>';
$a->add( 'onclick' , 'newcode()' );
echo 'get onclick: <b>' . htmlspecialchars( $a->get( 'onclick' ) ) . '</b> (ожидаем newcode())<br>';

# 5. has
echo '<h2>5. Has</h2>';
echo 'has onclick: <b>' . ( $a->has( 'onclick' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'has nope: <b>' . ( $a->has( 'onnope' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 6. get
echo '<h2>6. Get</h2>';
echo 'get onmouseover: <b>' . htmlspecialchars( $a->get( 'onmouseover' ) ) . '</b> (ожидаем hi())<br>';
echo 'get nope: <b>' . htmlspecialchars( $a->get( 'onnope' ) ) . '</b> (ожидаем пусто)<br>';

# 7. invalid event ignored
echo '<h2>7. Invalid event ignored</h2>';
$a->add( 'foo' , 'bar' );
echo 'has foo: <b>' . ( $a->has( 'foo' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';
echo 'has onclick: <b>' . ( $a->has( 'onclick' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 8. empty value ignored
echo '<h2>8. Empty value ignored</h2>';
$a->add( 'onfocus' , '' );
echo 'has onfocus: <b>' . ( $a->has( 'onfocus' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 9. shorthand onclick
echo '<h2>9. Shorthand</h2>';
$a = new visuoattrsactions( $va );
$a->onclick( 'x()' )->onmouseover( 'y()' );
echo 'all: <b>' . htmlspecialchars( json_encode( $a->all( ) ) ) . '</b> (ожидаем onclick,onmouseover)<br>';

# 10. remove
echo '<h2>10. Remove</h2>';
$a->remove( 'onclick' );
echo 'has onclick: <b>' . ( $a->has( 'onclick' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';
echo 'has onmouseover: <b>' . ( $a->has( 'onmouseover' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 11. clear
echo '<h2>11. Clear</h2>';
$a->clear( );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем пусто)<br>';

# 12. case-insensitive
echo '<h2>12. Case-insensitive</h2>';
$a = new visuoattrsactions( $va );
$a->add( 'ONCLICK' , 'x()' );
echo 'has onclick: <b>' . ( $a->has( 'onclick' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'has ONCLICK: <b>' . ( $a->has( 'ONCLICK' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 13. release full vs raw
echo '<h2>13. Release variants</h2>';
$a = new visuoattrsactions( $va );
$a->add( 'onclick' , 'x()' );
echo 'release(true): <b>' . htmlspecialchars( $a->release( ) ) . '</b> (с ведущим пробелом)<br>';
echo 'release(false): <b>' . htmlspecialchars( $a->release( false ) ) . '</b> (без пробела)<br>';

# 14. all HTML5 events accepted
echo '<h2>14. HTML5 events</h2>';
$a = new visuoattrsactions( $va );
foreach ( [ 'onclick' , 'onsubmit' , 'onkeydown' , 'ontouchstart' , 'onanimationend' , 'ontransitionend' , 'onwheel' , 'onhashchange' , 'ondragstart' ] as $ev ) {
	$a->add( $ev , 'x()' );
}
echo 'count: <b>' . count( $a->all( ) ) . '</b> (ожидаем 9)<br>';

# 15. escaping
echo '<h2>15. Escaping</h2>';
$a = new visuoattrsactions( $va );
$a->add( 'onclick' , 'alert("hi"); <script>bad()</script>' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем экранировано)<br>';

# 16. chainable
echo '<h2>16. Chainable</h2>';
$a = new visuoattrsactions( $va );
$a->add( 'onclick' , 'a()' )->add( 'onfocus' , 'b()' )->remove( 'onclick' );
echo 'all: <b>' . htmlspecialchars( json_encode( $a->all( ) ) ) . '</b> (ожидаем только onfocus)<br>';

# 17. parent
echo '<h2>17. Parent</h2>';
echo 'parent is visuotag: <b>' . ( $a->parent( ) === $vt ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'tag is visuotag: <b>' . ( $a->tag( ) === $vt ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

echo '<hr>done';

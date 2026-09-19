<?PHP
# Visuoattrsstyle tests — isolated
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

require $root . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';

echo '<h1>Visuoattrsstyle tests</h1>';

$vt = new visuotag( );
$va = new visuoattrs( $vt );

# 1. empty
echo '<h2>1. Empty</h2>';
$s = new visuoattrsstyle( $va );
echo 'release(true): <b>' . htmlspecialchars( $s->release( ) ) . '</b> (ожидаем пусто)<br>';
echo 'release(false): <b>' . htmlspecialchars( $s->release( false ) ) . '</b> (ожидаем пусто)<br>';

# 2. add one
echo '<h2>2. Add one</h2>';
$s->add( 'color:red' );
echo 'release(true): <b>' . htmlspecialchars( $s->release( ) ) . '</b> (ожидаем style="color:red;")<br>';

# 3. add many
echo '<h2>3. Add many</h2>';
$s->add( 'font-size:14px; padding: 5px ' );
echo 'all: <b>' . htmlspecialchars( json_encode( $s->all( ) , JSON_UNESCAPED_UNICODE ) ) . '</b><br>';
echo 'ожидаем color, font-size, padding<br>';

# 4. overwrite on add
echo '<h2>4. Overwrite</h2>';
$s->add( 'color:blue' );
echo 'get color: <b>' . htmlspecialchars( $s->get( 'color' ) ) . '</b> (ожидаем blue)<br>';

# 5. has
echo '<h2>5. Has</h2>';
echo 'has padding: <b>' . ( $s->has( 'padding' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'has nope: <b>' . ( $s->has( 'nope' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 6. get
echo '<h2>6. Get</h2>';
echo 'get padding: <b>' . htmlspecialchars( $s->get( 'padding' ) ) . '</b> (ожидаем 5px)<br>';
echo 'get nope: <b>' . htmlspecialchars( $s->get( 'nope' ) ) . '</b> (ожидаем пусто)<br>';

# 7. remove
echo '<h2>7. Remove</h2>';
$s->remove( 'color padding' );
echo 'has color: <b>' . ( $s->has( 'color' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';
echo 'has padding: <b>' . ( $s->has( 'padding' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 8. clear
echo '<h2>8. Clear</h2>';
$s->clear( );
echo 'release: <b>' . htmlspecialchars( $s->release( ) ) . '</b> (ожидаем пусто)<br>';

# 9. url with colon in value
echo '<h2>9. URL in value (bugfix)</h2>';
$s = new visuoattrsstyle( $va );
$s->add( 'background:url(http://example.com/img.png)' );
echo 'get background: <b>' . htmlspecialchars( $s->get( 'background' ) ) . '</b><br>';
echo 'ожидаем url(http://example.com/img.png) целиком<br>';

# 10. custom separators
echo '<h2>10. Custom separators</h2>';
$s = new visuoattrsstyle( $va );
$s->add( 'color=red|font-size=14px' , '|' , '=' );
echo 'all: <b>' . htmlspecialchars( json_encode( $s->all( ) ) ) . '</b> (ожидаем color=red, font-size=14px)<br>';

# 11. skip empty parts
echo '<h2>11. Skip empty</h2>';
$s = new visuoattrsstyle( $va );
$s->add( ';;color:red;;; font-size:14px ;;' );
echo 'all count: <b>' . count( $s->all( ) ) . '</b> (ожидаем 2)<br>';

# 12. whitespace trim
echo '<h2>12. Whitespace trim</h2>';
$s = new visuoattrsstyle( $va );
$s->add( '  color  :   red  ;  padding:5px  ' );
echo 'get color: <b>' . htmlspecialchars( $s->get( 'color' ) ) . '</b> (ожидаем red)<br>';
echo 'get padding: <b>' . htmlspecialchars( $s->get( 'padding' ) ) . '</b> (ожидаем 5px)<br>';

# 13. chainable
echo '<h2>13. Chainable</h2>';
$s = new visuoattrsstyle( $va );
$s->add( 'a:1' )->add( 'b:2' )->remove( 'a' );
echo 'all: <b>' . htmlspecialchars( json_encode( $s->all( ) ) ) . '</b> (ожидаем {b:2})<br>';

# 14. parent
echo '<h2>14. Parent</h2>';
echo 'parent is visuotag: <b>' . ( $s->parent( ) === $vt ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 15. escaping
echo '<h2>15. Escaping</h2>';
$s = new visuoattrsstyle( $va );
$s->add( 'x:"><script>alert(1)</script>' );
echo 'release: <b>' . htmlspecialchars( $s->release( ) ) . '</b> (ожидаем экранировано)<br>';

echo '<hr>done';
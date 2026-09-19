<?PHP
# Visuoattrsclass tests — isolated
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

require $root . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';

echo '<h1>Visuoattrsclass tests</h1>';

$vt = new visuotag( );
$va = new visuoattrs( $vt );

# 1. empty
echo '<h2>1. Empty</h2>';
$c = new visuoattrsclass( $va );
echo 'release(true): <b>' . htmlspecialchars( $c->release( ) ) . '</b> (ожидаем пусто)<br>';
echo 'release(false): <b>' . htmlspecialchars( $c->release( false ) ) . '</b> (ожидаем пусто)<br>';

# 2. add one
echo '<h2>2. Add one</h2>';
$c->add( 'container' );
echo 'release(true): <b>' . htmlspecialchars( $c->release( ) ) . '</b> (ожидаем class="container")<br>';
echo 'release(false): <b>' . htmlspecialchars( $c->release( false ) ) . '</b> (ожидаем container)<br>';

# 3. add many with spaces
echo '<h2>3. Add many</h2>';
$c->add( 'dark  rounded' );
echo 'all: <b>' . implode( ',' , $c->all( ) ) . '</b> (ожидаем container,dark,rounded)<br>';
echo 'release: <b>' . htmlspecialchars( $c->release( ) ) . '</b><br>';

# 4. dedup
echo '<h2>4. Dedup</h2>';
$c->add( 'container dark container' );
echo 'all: <b>' . implode( ',' , $c->all( ) ) . '</b> (ожидаем container,dark,rounded без повторов)<br>';

# 5. has
echo '<h2>5. Has</h2>';
echo 'has dark: <b>' . ( $c->has( 'dark' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'has nope: <b>' . ( $c->has( 'nope' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 6. get
echo '<h2>6. Get</h2>';
echo 'get dark: <b>' . htmlspecialchars( $c->get( 'dark' ) ) . '</b> (ожидаем dark)<br>';
echo 'get nope: <b>' . htmlspecialchars( $c->get( 'nope' ) ) . '</b> (ожидаем пусто)<br>';

# 7. remove
echo '<h2>7. Remove</h2>';
$c->remove( 'dark rounded' );
echo 'all: <b>' . implode( ',' , $c->all( ) ) . '</b> (ожидаем container)<br>';

# 8. clear
echo '<h2>8. Clear</h2>';
$c->clear( );
echo 'release: <b>' . htmlspecialchars( $c->release( ) ) . '</b> (ожидаем пусто)<br>';

# 9. whitespace trim
echo '<h2>9. Whitespace trim</h2>';
$c = new visuoattrsclass( $va );
$c->add( '   spaced   classes   ' );
echo 'all: <b>' . implode( ',' , $c->all( ) ) . '</b> (ожидаем spaced,classes)<br>';

# 10. chainable
echo '<h2>10. Chainable</h2>';
$c = new visuoattrsclass( $va );
$c->add( 'a' )->add( 'b' )->remove( 'a' );
echo 'all: <b>' . implode( ',' , $c->all( ) ) . '</b> (ожидаем b)<br>';

# 11. parent
echo '<h2>11. Parent</h2>';
echo 'parent is visuotag: <b>' . ( $c->parent( ) === $vt ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 12. order preserved
echo '<h2>12. Order preserved</h2>';
$c = new visuoattrsclass( $va );
$c->add( 'z' )->add( 'a' )->add( 'm' );
echo 'all: <b>' . implode( ',' , $c->all( ) ) . '</b> (ожидаем z,a,m в порядке добавления)<br>';

# 13. escaping
echo '<h2>13. Escaping</h2>';
$c = new visuoattrsclass( $va );
$c->add( 'evil"><script>alert(1)</script>' );
echo 'release: <b>' . htmlspecialchars( $c->release( ) ) . '</b> (ожидаем экранировано)<br>';

echo '<hr>done';
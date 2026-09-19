<?PHP
# Visuoattrs tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root = dirname( __DIR__ , 2 );

# --- stub visuotag (real one not needed for attrs test) ---
if ( ! class_exists( 'visuotag' ) ) {
	class visuotag {
		public string $tagname = 'div';
		public function __construct ( ...$args ) {}
	}
}

# --- load subclasses + parent ---
require $root . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root . '/sources/visuo/sources/visuoattrs.php';

echo '<h1>Visuoattrs tests</h1>';

$vt = new visuotag( );

# 1. empty
echo '<h2>1. Empty</h2>';
$a = new visuoattrs( $vt );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем пусто)<br>';
echo 'visuotag: <b>' . ( $a->visuotag( ) === $vt ? 'visuotag' : 'NOT' ) . '</b><br>';

# 2. generic attributes
echo '<h2>2. Generic</h2>';
$a->add( 'id' , 'main' );
$a->add( 'href' , '/index.php' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем id и href)<br>';
echo 'get id: <b>' . htmlspecialchars( $a->get( 'id' ) ) . '</b> (ожидаем main)<br>';
echo 'has href: <b>' . ( $a->has( 'href' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 3. add_empty flag
echo '<h2>3. add_empty flag</h2>';
$a = new visuoattrs( $vt );
$a->add( 'data-x' , '' );                    # default add_empty = false → не добавится
$a->add( 'data-y' , '' , true );             # add_empty = true → добавится
echo 'has data-x: <b>' . ( $a->has( 'data-x' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';
echo 'has data-y: <b>' . ( $a->has( 'data-y' ) ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 4. get falsy value (regression)
echo '<h2>4. Falsy value (regression)</h2>';
$a = new visuoattrs( $vt );
$a->add( 'value' , '0' );
echo 'get value: <b>' . var_export( $a->get( 'value' ) , true ) . '</b> (ожидаем "0", не null)<br>';
echo 'get nope: <b>' . var_export( $a->get( 'nope' ) , true ) . '</b> (ожидаем null)<br>';

# 5. remove
echo '<h2>5. Remove</h2>';
$a->remove( 'value' );
echo 'has value: <b>' . ( $a->has( 'value' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 6. class
echo '<h2>6. Class</h2>';
$a = new visuoattrs( $vt );
$a->class_add( 'container dark' );
$a->class_add( 'rounded' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем class="container dark rounded")<br>';
echo 'class has dark: <b>' . ( $a->class( )->has( 'dark' ) ? 'true' : 'false' ) . '</b><br>';

# 7. class_remove
echo '<h2>7. class_remove</h2>';
$a->class_remove( 'dark' );
echo 'class all: <b>' . implode( ',' , $a->class( )->all( ) ) . '</b> (ожидаем container,rounded)<br>';

# 8. style
echo '<h2>8. Style</h2>';
$a = new visuoattrs( $vt );
$a->style_add( 'color:red; font-size:14px' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем style="...")<br>';
echo 'style get color: <b>' . htmlspecialchars( $a->style( )->get( 'color' ) ) . '</b> (ожидаем red)<br>';

# 9. actions
echo '<h2>9. Actions</h2>';
$a = new visuoattrs( $vt );
$a->actions_add( 'onclick' , 'alert(1)' );
$a->actions_add( 'onmouseover' , 'hi()' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b><br>';
echo 'actions has onclick: <b>' . ( $a->actions( )->has( 'onclick' ) ? 'true' : 'false' ) . '</b><br>';

# 10. full release order (class → style → actions → generic)
echo '<h2>10. Full release order</h2>';
$a = new visuoattrs( $vt );
$a->add( 'id' , 'x' );
$a->class_add( 'cls' );
$a->style_add( 'color:red' );
$a->actions_add( 'onclick' , 'y()' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b><br>';
echo 'ожидаем: class → style → actions → id<br>';

# 11. attributes_release (без class/style/actions)
echo '<h2>11. attributes_release</h2>';
echo 'attributes_release: <b>' . htmlspecialchars( $a->attributes_release( ) ) . '</b> (ожидаем только id="x")<br>';

# 12. escaping generic
echo '<h2>12. Escaping generic</h2>';
$a = new visuoattrs( $vt );
$a->add( 'xss' , '"><script>alert(1)</script>' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b><br>';

# 13. clear
echo '<h2>13. Clear</h2>';
$a = new visuoattrs( $vt );
$a->add( 'id' , 'x' );
$a->class_add( 'y' );
$a->clear( );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем пусто)<br>';

# 14. sub-classes are singletons per instance
echo '<h2>14. Sub-class identity</h2>';
$a = new visuoattrs( $vt );
$c1 = $a->class( );
$c2 = $a->class( );
echo 'class same object: <b>' . ( $c1 === $c2 ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'class parent is visuotag: <b>' . ( $c1->parent( ) === $vt ? 'true' : 'false' ) . '</b><br>';

# 15. chainable
echo '<h2>15. Chainable</h2>';
$a = new visuoattrs( $vt );
$a->add( 'id' , 'x' )->class_add( 'y' )->style_add( 'z:1' )->actions_add( 'onclick' , 'q()' );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b><br>';

# 16. data через attrs
echo '<h2>16. data через attrs</h2>';
$a = new visuoattrs( $vt );
$a->data_add( 'role' , 'admin' , false );
$a->data_add( 'info' , [ 'x' => 1 ] );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b><br>';
echo 'ожидаем data-role="admin" data-info="{...}" в конце<br>';
echo 'data get role: <b>' . htmlspecialchars( $a->data( )->get( 'role' ) ) . '</b> (ожидаем admin)<br>';

# 17. data очищается clear()
echo '<h2>17. data cleared by clear()</h2>';
$a = new visuoattrs( $vt );
$a->data_add( 'role' , 'admin' , false );
$a->class_add( 'y' );
$a->clear( );
echo 'release: <b>' . htmlspecialchars( $a->release( ) ) . '</b> (ожидаем пусто)<br>';
echo 'data has role: <b>' . ( $a->data( )->has( 'role' ) ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

echo '<hr>done';
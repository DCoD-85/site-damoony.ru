<?PHP
# Visuometa tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

# --- stubs ---
if ( ! class_exists( 'visuolog' ) ) {
	class visuolog {
		public array $rows = [ ];
		public function __construct ( bool $online = false ) {}
		public function add ( string $m , string $method , string $file , string $line ) : void {
			$this->rows[ ] = $m;
		}
	}
}
if ( ! class_exists( 'visuorelease' ) ) {
	class visuorelease {
		public visuolog $visuolog;
		public function __construct ( ) { $this->visuolog = new visuolog( ); }
	}
}

require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';
require $root_dir . '/sources/visuo/sources/visuolink.php';
require $root_dir . '/sources/visuo/sources/visuometa.php';

echo '<h1>Visuometa tests</h1>';

# 1. construct without args
echo '<h2>1. Construct without args</h2>';
$m = new visuometa( );
echo 'visuorelease null: <b>' . ( $m->visuorelease( ) === null ? 'true' : 'false' ) . '</b><br>';
echo 'elements empty: <b>' . ( empty( $m->elements ) ? 'true' : 'false' ) . '</b><br>';

# 2. construct with visuorelease
echo '<h2>2. Construct with visuorelease</h2>';
$vl = new visuorelease( );
$m  = new visuometa( $vl );
echo 'visuorelease ok: <b>' . ( $m->visuorelease( ) === $vl ? 'true' : 'false' ) . '</b><br>';
echo 'log has construct entry: <b>' . ( count( $vl->visuolog->rows ) > 0 ? 'true' : 'false' ) . '</b> (ожидаем true — bugfix)<br>';

# 3. title factory
echo '<h2>3. title()</h2>';
$m = new visuometa( );
$t = $m->title( );
echo 'returns visuometa_title: <b>' . ( $t instanceof visuometa_title ? 'true' : 'false' ) . '</b><br>';
echo 'elements count: <b>' . count( $m->elements ) . '</b> (ожидаем 1)<br>';
echo 'elements[0] is t: <b>' . ( $m->elements[0] === $t ? 'true' : 'false' ) . '</b><br>';

# 4. title data + as_tag
echo '<h2>4. title data + as_tag</h2>';
$t->data( 'My Page' );
$vt = $t->as_tag( );
echo 'tag name: <b>' . $vt->tagname . '</b> (ожидаем title)<br>';
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 5. title back
echo '<h2>5. title back()</h2>';
echo 'back() is $m: <b>' . ( $t->back( ) === $m ? 'true' : 'false' ) . '</b><br>';

# 6. link factory
echo '<h2>6. link()</h2>';
$m = new visuometa( );
$l = $m->link( );
echo 'returns visuolink: <b>' . ( $l instanceof visuolink ? 'true' : 'false' ) . '</b><br>';
echo 'elements count: <b>' . count( $m->elements ) . '</b> (ожидаем 1)<br>';

# 7. link css
echo '<h2>7. link()->css()</h2>';
$m = new visuometa( );
$m->link( )->css( '/main.css' );
echo 'elements count: <b>' . count( $m->elements ) . '</b> (ожидаем 1)<br>';
$vt = $m->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 8. link js
echo '<h2>8. link()->js()</h2>';
$m = new visuometa( );
$m->link( )->js( '/main.js' );
$vt = $m->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 9. link favicon
echo '<h2>9. link()->favicon()</h2>';
$m = new visuometa( );
$m->link( )->favicon( '/favicon.ico#v1' );
$vt = $m->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 10. multiple links
echo '<h2>10. multiple links</h2>';
$m = new visuometa( );
$m->link( )->css( '/a.css' );
$m->link( )->css( '/b.css' );
$m->link( )->js( '/a.js' );
echo 'elements count: <b>' . count( $m->elements ) . '</b> (ожидаем 3)<br>';
$vt = $m->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 11. title + links together
echo '<h2>11. title + links</h2>';
$m = new visuometa( );
$m->title( )->data( 'Title' );
$m->link( )->css( '/main.css' );
$vt = $m->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 12. clear
echo '<h2>12. clear()</h2>';
$m = new visuometa( );
$m->title( )->data( 'X' );
$m->link( )->css( '/a.css' );
$m->clear( );
echo 'elements count: <b>' . count( $m->elements ) . '</b> (ожидаем 0)<br>';
$vt = $m->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b> (ожидаем пусто)<br>';

# 13. clear chainable
echo '<h2>13. clear chainable</h2>';
$m = new visuometa( );
echo 'returns $this: <b>' . ( $m->clear( ) === $m ? 'true' : 'false' ) . '</b><br>';

# 14. as_tag returns new visuotag each call
echo '<h2>14. as_tag idempotent</h2>';
$m = new visuometa( );
$m->title( )->data( 'X' );
$vt1 = $m->as_tag( );
$vt2 = $m->as_tag( );
echo 'two different visuotags: <b>' . ( $vt1 !== $vt2 ? 'true' : 'false' ) . '</b><br>';
echo 'first release: <b>' . htmlspecialchars( $vt1->release( ) ) . '</b><br>';
echo 'second release: <b>' . htmlspecialchars( $vt2->release( ) ) . '</b> (не должен дублировать)<br>';

# 15. as_tag empty
echo '<h2>15. as_tag empty</h2>';
$m = new visuometa( );
$vt = $m->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b> (ожидаем пусто)<br>';

# 16. integration with visuotag meta()
echo '<h2>16. integration with visuotag::meta()</h2>';
$root = new visuotag( );
$head = $root->tag( 'head' );
$m = new visuometa( );
$m->title( )->data( 'Integrated' );
$m->link( )->css( '/x.css' );
$head->meta( $m );
echo 'release: <b>' . htmlspecialchars( $root->release( ) ) . '</b><br>';

# 17. log entries on title/link
echo '<h2>17. log entries</h2>';
$vl = new visuorelease( );
$m  = new visuometa( $vl );
$m->title( );
$m->link( );
echo 'log count: <b>' . count( $vl->visuolog->rows ) . '</b> (ожидаем >= 3)<br>';

# 18. visuotag accessor still works
echo '<h2>18. visuotag accessor</h2>';
$m = new visuometa( );
echo 'visuotag() is visuotag: <b>' . ( $m->visuotag( ) instanceof visuotag ? 'true' : 'false' ) . '</b><br>';

# 19. visuotag_set
echo '<h2>19. visuotag_set</h2>';
$m = new visuometa( );
$vt = new visuotag( );
$m->visuotag_set( $vt );
echo 'returns $this: <b>' . ( $m->visuotag_set( $vt ) === $m ? 'true' : 'false' ) . '</b><br>';
echo 'visuotag set: <b>' . ( $m->visuotag( ) === $vt ? 'true' : 'false' ) . '</b><br>';

# 20. title empty content
echo '<h2>20. title empty content</h2>';
$m = new visuometa( );
$t = $m->title( );
$vt = $t->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b> (ожидаем &lt;title&gt;&lt;/title&gt;)<br>';

echo '<hr>done';
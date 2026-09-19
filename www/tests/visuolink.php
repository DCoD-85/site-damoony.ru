<?PHP
# Visuolink tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

# --- stubs ---
if ( ! class_exists( 'visuometa' ) ) {
	class visuometa {
		public function __construct ( $visuorelease = null ) {}
	}
}

require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';
require $root_dir . '/sources/visuo/sources/visuolink.php';

echo '<h1>Visuolink tests</h1>';

$meta = new visuometa( );

# 1. construct
echo '<h2>1. Construct</h2>';
$l = new visuolink( $meta );
echo 'visuometa ok: <b>' . ( $l->visuometa === $meta ? 'true' : 'false' ) . '</b><br>';
echo 'type empty: <b>' . ( $l->type === '' ? 'true' : 'false' ) . '</b><br>';

# 2. favicon factory
echo '<h2>2. favicon()</h2>';
$l = new visuolink( $meta );
$f = $l->favicon( '/favicon.ico' );
echo 'returns visuolink_favicon: <b>' . ( $f instanceof visuolink_favicon ? 'true' : 'false' ) . '</b><br>';
echo 'type set: <b>' . $l->type . '</b> (ожидаем favicon)<br>';
echo 'property set: <b>' . ( $l->favicon === $f ? 'true' : 'false' ) . '</b><br>';

# 3. favicon as_tag
echo '<h2>3. favicon as_tag</h2>';
$l = new visuolink( $meta );
$l->favicon( '/favicon.ico' );
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 4. favicon custom href
echo '<h2>4. favicon custom href</h2>';
$l = new visuolink( $meta );
$l->favicon( '/favicon.ico#v1' );
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 5. css factory
echo '<h2>5. css()</h2>';
$l = new visuolink( $meta );
$c = $l->css( '/main.css' );
echo 'returns visuolink_css: <b>' . ( $c instanceof visuolink_css ? 'true' : 'false' ) . '</b><br>';
echo 'type set: <b>' . $l->type . '</b> (ожидаем css)<br>';

# 6. css as_tag
echo '<h2>6. css as_tag</h2>';
$l = new visuolink( $meta );
$l->css( '/main.css' );
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 7. css без cache-buster (по умолчанию)
echo '<h2>7. css без cache-buster по умолчанию</h2>';
$l = new visuolink( $meta );
$l->css( 'https://cdn.plyr.io/3.8.4/plyr.css' );
$vt = $l->as_tag( );
$html = $vt->release( );
echo 'release: <b>' . htmlspecialchars( $html ) . '</b><br>';
echo 'содержит ?: <b>' . ( strpos( $html , '?' ) !== false ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 8. js factory
echo '<h2>8. js()</h2>';
$l = new visuolink( $meta );
$j = $l->js( '/main.js' );
echo 'returns visuolink_js: <b>' . ( $j instanceof visuolink_js ? 'true' : 'false' ) . '</b><br>';
echo 'type set: <b>' . $l->type . '</b> (ожидаем js)<br>';

# 9. js as_tag
echo '<h2>9. js as_tag</h2>';
$l = new visuolink( $meta );
$l->js( '/main.js' );
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 10. js CDN без rand
echo '<h2>10. js CDN без cache-buster</h2>';
$l = new visuolink( $meta );
$l->js( 'https://cdn.plyr.io/3.8.4/plyr.js' );
$vt = $l->as_tag( );
$html = $vt->release( );
echo 'release: <b>' . htmlspecialchars( $html ) . '</b><br>';
echo 'содержит ?: <b>' . ( strpos( $html , '?' ) !== false ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';

# 11. as_tag без выбора
echo '<h2>11. as_tag без выбора</h2>';
$l = new visuolink( $meta );
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b> (ожидаем пусто, не падает)<br>';

# 12. type = nonexistent (guard через whitelist)
echo '<h2>12. type nonexistent — guard</h2>';
$l = new visuolink( $meta );
$l->type = 'nonexistent';
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b> (ожидаем пусто)<br>';
echo 'без Warning: да (тест пройден, если здесь нет оранжевых сообщений выше)<br>';

# 13. favicon defaults
echo '<h2>13. favicon defaults</h2>';
$l = new visuolink( $meta );
$f = $l->favicon( '/x.ico' );
echo 'd_rel: <b>' . $f->d_rel . '</b> (ожидаем icon)<br>';
echo 'd_type: <b>' . $f->d_type . '</b> (ожидаем image/x-icon)<br>';
echo 'd_href: <b>' . $f->d_href . '</b> (ожидаем /x.ico)<br>';

# 14. favicon custom rel/type
echo '<h2>14. favicon custom rel/type</h2>';
$l = new visuolink( $meta );
$f = $l->favicon( '/fav.svg' );
$f->d_rel = 'shortcut icon';
$f->d_type = 'image/svg+xml';
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 15. css idempotent
echo '<h2>15. css idempotent</h2>';
$l = new visuolink( $meta );
$l->css( '/a.css' );
$l1 = $l->as_tag( )->release( );
$l2 = $l->as_tag( )->release( );
echo 'identical: <b>' . ( $l1 === $l2 ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 16. favicon idempotent
echo '<h2>16. favicon idempotent</h2>';
$l = new visuolink( $meta );
$l->favicon( '/favicon.ico' );
$h1 = $l->as_tag( )->release( );
$h2 = $l->as_tag( )->release( );
echo 'identical: <b>' . ( $h1 === $h2 ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 17. разные подклассы
echo '<h2>17. разные подклассы</h2>';
$l = new visuolink( $meta );
$f = $l->favicon( '/fav.ico' );
$c = $l->css( '/s.css' );
$j = $l->js( '/s.js' );
echo 'favicon set: <b>' . ( $l->favicon === $f ? 'true' : 'false' ) . '</b><br>';
echo 'css set: <b>' . ( $l->css === $c ? 'true' : 'false' ) . '</b><br>';
echo 'js set: <b>' . ( $l->js === $j ? 'true' : 'false' ) . '</b><br>';
echo 'type: <b>' . $l->type . '</b> (ожидаем js — последний вызов)<br>';

# 18. visuometa ссылка
echo '<h2>18. visuometa ссылка</h2>';
$meta2 = new visuometa( );
$l = new visuolink( $meta2 );
echo 'visuometa: <b>' . ( $l->visuometa === $meta2 ? 'true' : 'false' ) . '</b><br>';

# 19. jQuery без rand
echo '<h2>19. jQuery без rand</h2>';
$l = new visuolink( $meta );
$l->js( '/media/release/default/js/jquery-1.8.3.min.js' );
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# 20. пустой href
echo '<h2>20. пустой href</h2>';
$l = new visuolink( $meta );
$l->css( '' );
$vt = $l->as_tag( );
echo 'release: <b>' . htmlspecialchars( $vt->release( ) ) . '</b><br>';

# -------- cache-buster (опциональный) --------

# 21. css с rand=true
echo '<h2>21. css rand=true</h2>';
$l = new visuolink( $meta );
$l->css( '/main.css' , true );
$html = $l->as_tag( )->release( );
echo 'release: <b>' . htmlspecialchars( $html ) . '</b><br>';
echo 'содержит ?: <b>' . ( strpos( $html , '?' ) !== false ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 22. js с rand=true
echo '<h2>22. js rand=true</h2>';
$l = new visuolink( $meta );
$l->js( '/main.js' , true );
$html = $l->as_tag( )->release( );
echo 'release: <b>' . htmlspecialchars( $html ) . '</b><br>';
echo 'содержит ?: <b>' . ( strpos( $html , '?' ) !== false ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 23. favicon с rand=true
echo '<h2>23. favicon rand=true</h2>';
$l = new visuolink( $meta );
$l->favicon( '/favicon.ico' , true );
$html = $l->as_tag( )->release( );
echo 'release: <b>' . htmlspecialchars( $html ) . '</b><br>';
echo 'содержит ?: <b>' . ( strpos( $html , '?' ) !== false ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 24. rand=true idempotent
echo '<h2>24. rand=true idempotent</h2>';
$l = new visuolink( $meta );
$l->css( '/a.css' , true );
$h1 = $l->as_tag( )->release( );
$h2 = $l->as_tag( )->release( );
echo 'identical: <b>' . ( $h1 === $h2 ? 'true' : 'false' ) . '</b> (ожидаем true — buster добавлен один раз в конструкторе)<br>';
echo 'release: <b>' . htmlspecialchars( $h1 ) . '</b><br>';

# 25. rand с уже существующим query
echo '<h2>25. rand + существующий query</h2>';
$l = new visuolink( $meta );
$l->css( '/main.css?v=1' , true );
$html = $l->as_tag( )->release( );
echo 'release: <b>' . htmlspecialchars( $html ) . '</b><br>';
echo 'содержит ?v=1&amp;: <b>' . ( strpos( $html , '?v=1&amp;' ) !== false ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 26. rand + якорь
echo '<h2>26. rand + якорь</h2>';
$l = new visuolink( $meta );
$l->favicon( '/favicon.ico#v1' , true );
$html = $l->as_tag( )->release( );
echo 'release: <b>' . htmlspecialchars( $html ) . '</b><br>';
echo 'якорь в конце: <b>' . ( preg_match( '/#v1"$/' , $html ) === 1 ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 27. resolve_rand без константы
echo '<h2>27. resolve_rand</h2>';
echo 'resolve_rand(null): <b>' . var_export( visuolink::resolve_rand( null ) , true ) . '</b> (ожидаем false — константа не определена)<br>';
echo 'resolve_rand(true): <b>' . var_export( visuolink::resolve_rand( true ) , true ) . '</b> (ожидаем true)<br>';
echo 'resolve_rand(false): <b>' . var_export( visuolink::resolve_rand( false ) , true ) . '</b> (ожидаем false)<br>';

echo '<hr>done';
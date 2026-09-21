<?PHP
# visuotag::el() + visuoel — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';

require $root_dir . '/sources/visuo/sources/visuoel.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group_element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer/visuoel_primer_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer/visuoel_primer_image.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer.php';

echo '<h1>visuotag::el() + visuoel</h1>';

echo '<h2>1. method_exists visuotag::el</h2>';
echo '<b>' . var_export( method_exists( 'visuotag' , 'el' ) , true ) . '</b> (ожидаем true)<br>';

# --- simple chain ---
echo '<h2>2. vt->el(visuoel) → returns visuotag (chainable)</h2>';
$vt = new visuotag( );
$wrap = $vt->tag( 'div' , class : 'wrap' );   // ← сохраняем возврат
$root = new visuoel( );
$root->primer( )->text( )->data( )->set( 'Привет' );
$ret = $wrap->el( $root );                     // ← el в wrap, не в корень
echo 'is visuotag: <b>' . var_export( $ret instanceof visuotag , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. prepare() → visuoel vt_start вшит в плейсхолдер</h2>';
$vt->prepare( );
$html = $vt->release( );
echo 'contains class="primer": <b>' . var_export( str_contains( $html , 'class="primer"' ) , true ) . '</b> (ожидаем true)<br>';
echo 'contains class="text": <b>' . var_export( str_contains( $html , 'class="text"' ) , true ) . '</b> (ожидаем true)<br>';
echo 'contains "Привет": <b>' . var_export( str_contains( $html , 'Привет' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. HTML НЕ содержит &lt;el class="cl_el"&gt;</h2>';
echo 'no cl_el: <b>' . var_export( ! str_contains( $html , 'cl_el' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>5. HTML содержит обёртку &lt;div class="wrap"&gt;</h2>';
echo 'wrap: <b>' . var_export( str_contains( $html , 'class="wrap"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>6. primer внутри wrap</h2>';
$pos_wrap   = strpos( $html , 'class="wrap"' );
$pos_primer = strpos( $html , 'class="primer"' );
echo 'primer after wrap: <b>' . var_export( $pos_primer > $pos_wrap , true ) . '</b> (ожидаем true)<br>';

echo '<h2>7. Два visuoel подряд в одном wrap</h2>';
$vt2   = new visuotag( );
$wrap2 = $vt2->tag( 'div' , class : 'wrap' );

$r1 = new visuoel( );
$r1->primer( )->text( )->data( )->set( 'Первый' );
$r1->primer( )->text( )->data( )->set( 'Первый 22' );

$r2 = new visuoel( );
$r2->primer( )->text( )->data( )->set( 'Второй' );

$wrap2->el( $r1 )->el( $r2 );
$vt2->prepare( );
$h2 = $vt2->release( );
echo 'Первый: <b>' . var_export( str_contains( $h2 , 'Первый' ) , true ) . '</b> (ожидаем true)<br>';
echo 'Второй: <b>' . var_export( str_contains( $h2 , 'Второй' ) , true ) . '</b> (ожидаем true)<br>';
echo 'Порядок: <b>' . var_export( strpos( $h2 , 'Первый' ) < strpos( $h2 , 'Второй' ) , true ) . '</b> (ожидаем true)<br>';

echo '<hr>';
echo '<h2>Полный HTML</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $html )
	. '</pre>';
	
echo '<hr>';
echo '<h2>Полный h2</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h2 )
	. '</pre>';

echo '<hr>done';
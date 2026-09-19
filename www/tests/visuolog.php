<?PHP
# Visuolog tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

# visuotag stack (visuolog::as_tag uses visuotag)
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';

require $root_dir . '/sources/visuo/sources/visuolog.php';

echo '<h1>Visuolog tests</h1>';

echo '<h2>1. class_exists visuolog</h2>';
echo '<b>' . var_export( class_exists( 'visuolog' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuolog_row</h2>';
echo '<b>' . var_export( class_exists( 'visuolog_row' ) , true ) . '</b> (ожидаем true)<br>';

$log = new visuolog( );
echo '<h2>3. new visuolog()</h2>';
echo 'class: <b>' . get_class( $log ) . '</b> (ожидаем visuolog)<br>';
echo 'rows_count: <b>' . $log->rows_count . '</b> (ожидаем 0)<br>';

$log->add( 'test 1' , __METHOD__ , __FILE__ , __LINE__ );
echo '<h2>4. add() → rows_count +1, rows[0] = visuolog_row</h2>';
echo 'rows_count: <b>' . $log->rows_count . '</b> (ожидаем 1)<br>';
echo 'rows[0]: <b>' . get_class( $log->rows[0] ) . '</b> (ожидаем visuolog_row)<br>';

$log->add( 'test 2' , __METHOD__ , __FILE__ , __LINE__ );
echo '<h2>5. add() второй раз</h2>';
echo 'rows_count: <b>' . $log->rows_count . '</b> (ожидаем 2)<br>';

$log2 = new visuolog( );
$t = $log2->as_tag( );
echo '<h2>6. as_tag() пустой → visuotag</h2>';
echo 'class: <b>' . get_class( $t ) . '</b> (ожидаем visuotag)<br>';

$r = $log2->release( );
echo '<h2>7. release() пустой → содержит «Лог пустой»</h2>';
echo 'contains: <b>' . var_export( str_contains( $r , 'Лог пустой' ) , true ) . '</b> (ожидаем true)<br>';

$log3 = new visuolog( );
$log3->add( 'msg-A' , 'm' , '/f' , '1' );
$log3->add( 'msg-B' , 'm' , '/f' , '2' );
$r = $log3->release( );
echo '<h2>8. release() с двумя строками содержит оба msg</h2>';
echo 'msg-A: <b>' . var_export( str_contains( $r , 'msg-A' ) , true ) . '</b> (ожидаем true)<br>';
echo 'msg-B: <b>' . var_export( str_contains( $r , 'msg-B' ) , true ) . '</b> (ожидаем true)<br>';

$row = $log3->rows[0];
echo '<h2>9. row->hrtime и row->datetime после add</h2>';
echo 'hrtime > 0: <b>' . var_export( $row->hrtime > 0 , true ) . '</b> (ожидаем true)<br>';
echo 'datetime: <b>' . get_class( $row->datetime ) . '</b> (ожидаем DateTime)<br>';

echo '<h2>10. row->as_tag(0) — fallback (bugfix: no $start_hr)</h2>';
$GLOBALS[ 'start_hr' ] = (int) $row->hrtime;
$t0 = $row->as_tag( 0 );
echo 'class: <b>' . get_class( $t0 ) . '</b> (ожидаем visuotag)<br>';

echo '<h2>11. row->as_tag(1) — hrtime предыдущей строки (bugfix: isset guard)</h2>';
$row2 = $log3->rows[1];
$t1 = $row2->as_tag( 1 );
echo 'class: <b>' . get_class( $t1 ) . '</b> (ожидаем visuotag)<br>';

echo '<h2>12. add() без $_SERVER[DOCUMENT_ROOT] не падает (bugfix)</h2>';
$saved = $_SERVER[ 'DOCUMENT_ROOT' ] ?? null;
unset( $_SERVER[ 'DOCUMENT_ROOT' ] );
$log4 = new visuolog( );
$err = null;
try {
	$log4->add( 'no-dr' , 'm' , '/some/path' , '1' );
} catch ( \Throwable $e ) {
	$err = $e->getMessage( );
}
if( $saved !== null ) $_SERVER[ 'DOCUMENT_ROOT' ] = $saved;
echo 'no error: <b>' . var_export( $err === null , true ) . '</b> (ожидаем true)';
if( $err !== null ) echo ' — ' . $err;
echo '<br>';

echo '<h2>13. final() при online=false → print release</h2>';
$log5 = new visuolog( false );
$log5->add( 'final-msg' , 'm' , '/f' , '1' );
ob_start( );
$log5->final( );
$out = ob_get_clean( );
echo 'contains final-msg: <b>' . var_export( str_contains( $out , 'final-msg' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>14. visuolog_row::release() без add() не падает (bugfix)</h2>';
$row_orphan = new visuolog_row( $log3 );
$r = $row_orphan->release( );
echo 'is string: <b>' . var_export( is_string( $r ) , true ) . '</b> (ожидаем true)<br>';

echo '<hr>done';
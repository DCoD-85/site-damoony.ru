<?PHP
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

require_once $root_dir . '/sources/visuo/sources/visuoels.php';

echo '<h1>visuoel — table tests</h1>';

echo '<h2>1. class_exists visuoel_table</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_table' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_table_section</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_table_section' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. class_exists visuoel_table_row</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_table_row' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. class_exists visuoel_table_cell</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_table_cell' ) , true ) . '</b> (ожидаем true)<br>';

# --- дивный режим ---
echo '<hr>';
echo '<h2>5. Дивный режим: table-&gt;row-&gt;cell</h2>';

$ve = new visuoel( );
$t = $ve->table( );

$row1 = $t->row( );
$row1->cell( width : 30 )->set( 'Логин' );
$row1->cell( width : 70 )->set( 'Значение' );

$row2 = $t->row( );
$row2->cell( width : 30 )->set( 'Имя' );
$row2->cell( width : 70 )->set( 'Иван' );

$row3 = $t->row( );
$row3->cell( width : 30 , inline : true )->set( 'A' );
$row3->cell( width : 70 , inline : true )->set( 'B' );

$ve->init( );
$h = $ve->release( );

echo '<h2>6. HTML дивного режима</h2>';
foreach ( [
	'<div class="visuotable">' ,
	'<div class="ttype tbody">' ,
	'<div class="row">' ,
	'<div class="col cols_30">' ,
	'<div class="col-inside">Логин</div>' ,
	'<div class="col cols_70 colelsinline">' ,
	'<div class="col-inside">B</div>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
}

# --- html режим ---
echo '<hr>';
echo '<h2>7. HTML-режим: &lt;table&gt; + rowspan/colspan</h2>';

$ve2 = new visuoel( );
$t2 = $ve2->table( );
$t2->html_mode( )->attr( 'border' , '1' )->attr( 'cellpadding' , '10' );

$r1 = $t2->row( );
$r1->cell( rowspan : 2 )->set( 'Объединённая (вертикально)' );
$r1->cell( )->set( 'Ячейка 2' );

$r2 = $t2->row( );
$r2->cell( )->set( 'Ячейка 4' );

$ve2->init( );
$h2 = $ve2->release( );

foreach ( [
	'<table class="visuotable_html"' ,
	'border="1"' ,
	'cellpadding="10"' ,
	'<tbody>' ,
	'<tr>' ,
	'<td rowspan="2">Объединённая (вертикально)</td>' ,
	'<td>Ячейка 2</td>' ,
	'<td>Ячейка 4</td>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h2 , $needle ) , true ) . '</b><br>';
}

# --- с визуоэл-детьми ---
echo '<hr>';
echo '<h2>8. Ячейка с вложенным field</h2>';

$ve3 = new visuoel( );
$t3 = $ve3->table( );
$r = $t3->row( );
$c = $r->cell( width : 100 );

$inner = new visuoel( );
$inner->field( )->text( )->data( )->set( 'q' )->label( 'Поиск' );
$c->add( $inner );

$ve3->init( );
$h3 = $ve3->release( );

echo '<h2>9. Полный HTML (дивный)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h ) . '</pre>';

echo '<h2>10. Полный HTML (HTML-режим)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h2 ) . '</pre>';

echo '<h2>11. Полный HTML (с field)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h3 ) . '</pre>';

echo '<h2>12. Живой рендер (дивный)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

echo '<h2>13. Живой рендер (HTML-таблица)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h2 . '</div>';

echo '<h2>14. Живой рендер (с field)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h3 . '</div>';

echo '<hr>done';
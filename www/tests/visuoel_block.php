<?PHP
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
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_block__element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_block/visuoel_block_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_block/visuoel_block_html.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_block.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_message__element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_message/visuoel_message_info.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_message/visuoel_message_success.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_message/visuoel_message_warning.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_message/visuoel_message_error.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_message.php';

echo '<h1>visuoel — block + message tests</h1>';

echo '<h2>1. class_exists visuoel_block</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_block' ) , true ) . '</b><br>';

echo '<h2>2. class_exists visuoel_block_text</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_block_text' ) , true ) . '</b><br>';

echo '<h2>3. class_exists visuoel_block_html</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_block_html' ) , true ) . '</b><br>';

echo '<h2>4. class_exists visuoel_message</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_message' ) , true ) . '</b><br>';

echo '<h2>5. class_exists visuoel_message_info</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_message_info' ) , true ) . '</b><br>';

# --- block ---
echo '<hr>';
echo '<h2>6. block-&gt;text() дважды — multi</h2>';
$ve = new visuoel( );
$b = $ve->block( );
$b->text( )->data( )->set( 'Первый абзац' );
$b->text( )->data( )->set( 'Второй абзац' );
$b->html( )->data( )->set( '<b>Жирный</b> HTML' );
echo 'elements count: <b>' . count( $b->elements ) . '</b> (ожидаем 3)<br>';

$ve->init( );
$h = $ve->release( );

echo '<h2>7. HTML block</h2>';
foreach ( [
	'<div class="block_text">Первый абзац</div>' ,
	'<div class="block_text">Второй абзац</div>' ,
	'<div class="block_html"><b>Жирный</b> HTML</div>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
}

echo '<h2>8. Экранирование в text</h2>';
$ve2 = new visuoel( );
$ve2->block( )->text( )->data( )->set( '<script>alert(1)</script>' );
$ve2->init( );
$h2 = $ve2->release( );
echo 'no raw script: <b>' . var_export( ! str_contains( $h2 , '<script>' ) , true ) . '</b> (ожидаем true)<br>';
echo 'escaped: <b>' . var_export( str_contains( $h2 , '&lt;script&gt;' ) , true ) . '</b> (ожидаем true)<br>';

# --- message ---
echo '<hr>';
echo '<h2>9. message — четыре плашки</h2>';
$vm = new visuoel( );
$msg = $vm->message( );
$msg->info( )->data( )->set( 'Информация' );
$msg->success( )->data( )->set( 'Успешно' );
$msg->warning( )->data( )->set( 'Внимание' );
$msg->error( )->data( )->set( 'Ошибка' );

$vm->init( );
$hm = $vm->release( );

foreach ( [
	'<div class="message message_info">Информация</div>' ,
	'<div class="message message_success">Успешно</div>' ,
	'<div class="message message_warning">Внимание</div>' ,
	'<div class="message message_error">Ошибка</div>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $hm , $needle ) , true ) . '</b><br>';
}

echo '<h2>10. Полный HTML (block)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h ) . '</pre>';

echo '<h2>11. Полный HTML (message)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $hm ) . '</pre>';

echo '<h2>12. Живой рендер (block)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

echo '<h2>13. Живой рендер (message)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $hm . '</div>';

echo '<hr>done';
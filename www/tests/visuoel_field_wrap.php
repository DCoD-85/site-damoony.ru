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

echo '<h1>visuoel — field wrap tests</h1>';

# --- 1. class_exists ---
echo '<h2>1. class_exists visuoel_field_text</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_field_text' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_field_checkbox</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_field_checkbox' ) , true ) . '</b> (ожидаем true)<br>';

# --- 3. field_text: label + hint ---
echo '<hr><h2>3. field_text: label + hint</h2>';

$ve = new visuoel( );
$f = $ve->field( )->text( );
$f->data( )->name = 'login';
$f->data( )->label( 'Логин' )->hint( 'Латиница, 3-32 символа' );

$ve->init( );
$h = $ve->release( );

foreach ( [
	'<div class="field field_text">' ,
	'<div class="box">' ,
	'<label class="field-label"' ,
	'for="field_login"' ,
	'>Логин</label>' ,
	'<div class="border-box">' ,
	'<input' ,
	'<div class="hint">' ,
	'Латиница, 3-32 символа' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
}

# --- 4. field_text: hide_label + hide_hint ---
echo '<hr><h2>4. field_text: hide_label + hide_hint</h2>';

$ve2 = new visuoel( );
$f2 = $ve2->field( )->text( );
$f2->data( )->label( 'Скрытый' )->hint( 'Скрытый hint' );
$f2->data( )->hide_label( )->hide_hint( );
$f2->data( )->name = 'hidden_label';

$ve2->init( );
$h2 = $ve2->release( );

echo 'label отсутствует: <b>' . var_export( ! str_contains( $h2 , 'Скрытый' ) , true ) . '</b> (ожидаем true)<br>';
echo 'hint отсутствует: <b>' . var_export( ! str_contains( $h2 , 'Скрытый hint' ) , true ) . '</b> (ожидаем true)<br>';

# --- 5. field_checkbox: label + checked ---
echo '<hr><h2>5. field_checkbox: label + checked</h2>';

$ve3 = new visuoel( );
$c = $ve3->field( )->checkbox( );
$c->data( )->label( 'Запомнить меня' );
$c->data( )->name = 'remember';
$c->data( )->checked = true;

$ve3->init( );
$h3 = $ve3->release( );

foreach ( [
	'<div class="field field_checkbox">' ,
	'<label class="field-label"' ,
	'<span class="border-box">' ,
	'type="checkbox"' ,
	'checked="checked"' ,
	'<span class="field-name">Запомнить меня</span>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h3 , $needle ) , true ) . '</b><br>';
}

# --- 6-8. Полные листинги HTML ---
echo '<hr><h2>6. HTML field_text (label + hint)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h ) . '</pre>';

echo '<h2>7. HTML field_text (hide_label + hide_hint)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h2 ) . '</pre>';

echo '<h2>8. HTML field_checkbox</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h3 ) . '</pre>';

# --- 9-11. Живой рендер ---
echo '<h2>9. Живой рендер field_text</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

echo '<h2>10. Живой рендер field_text (hide)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h2 . '</div>';

echo '<h2>11. Живой рендер field_checkbox</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h3 . '</div>';

# --- 12. field_radio ---
echo '<hr><h2>12. field_radio</h2>';

$ve4 = new visuoel( );
$r = $ve4->field( )->radio( );
$r->data( )->name  = 'gender';
$r->data( )->value = 'm';
$r->data( )->label( 'Мужской' );

$ve4->init( );
$h4 = $ve4->release( );

foreach ( [
	'<div class="field field_radio">' ,
	'<label class="field-label"' ,
	'<span class="border-box">' ,
	'type="radio"' ,
	'id="field_gender_m"' ,
	'name="gender"' ,
	'value="m"' ,
	'<span class="field-name">Мужской</span>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h4 , $needle ) , true ) . '</b><br>';
}

echo '<h2>13. HTML field_radio</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h4 ) . '</pre>';

echo '<h2>14. Живой рендер field_radio</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h4 . '</div>';

# --- 15. field_textarea ---
echo '<hr><h2>15. field_textarea</h2>';

$ve5 = new visuoel( );
$t5 = $ve5->field( )->textarea( );
$t5->data( )->name = 'bio';
$t5->data( )->label( 'О себе' );
$t5->data( )->hint( 'Пара слов, необязательно' );
$t5->data( )->rows = 4;
$t5->data( )->cols = 40;

$ve5->init( );
$h5 = $ve5->release( );

foreach ( [
	'<div class="field field_textarea">' ,
	'<label class="field-label"' ,
	'for="field_bio"' ,
	'<div class="border-box">' ,
	'<textarea' ,
	'rows="4"' ,
	'cols="40"' ,
	'name="bio"' ,
	'</textarea>' ,
	'<div class="hint">' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h5 , $needle ) , true ) . '</b><br>';
}

echo '<h2>16. HTML field_textarea</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h5 ) . '</pre>';

# --- 17. field_select ---
echo '<hr><h2>17. field_select</h2>';

$ve6 = new visuoel( );
$s6 = $ve6->field( )->select( );
$s6->data( )->name = 'city';
$s6->data( )->label( 'Город' );
$s6->data( )->hint( 'Где вы живёте' );
$s6->data( )->options = [ '' => '— выберите —' , 'msk' => 'Москва' , 'spb' => 'Санкт-Петербург' ];

$ve6->init( );
$h6 = $ve6->release( );

foreach ( [
	'<div class="field field_select">' ,
	'<label class="field-label"' ,
	'for="field_city"' ,
	'<div class="border-box">' ,
	'<select' ,
	'name="city"' ,
	'<option' ,
	'value="msk"' ,
	'Москва' ,
	'</select>' ,
	'<div class="hint">' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h6 , $needle ) , true ) . '</b><br>';
}

echo '<h2>18. HTML field_select</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h6 ) . '</pre>';

echo '<h2>19. Живой рендер field_textarea</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h5 . '</div>';

echo '<h2>20. Живой рендер field_select</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h6 . '</div>';

echo '<hr>done';
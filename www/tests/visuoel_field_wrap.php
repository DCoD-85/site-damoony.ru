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

function needles ( string $h , array $needles ) {
	foreach ( $needles as $needle ) {
		echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
	}
}
function dump_html ( string $h , string $title ) {
	echo '<h2>' . $title . '</h2>';
	echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
		. htmlspecialchars( $h ) . '</pre>';
}
function live ( string $h , string $title ) {
	echo '<h2>' . $title . '</h2>';
	echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';
}

echo '<h1>visuoel — field wrap tests</h1>';

# =====================================================
# 1. class_exists — все типы
# =====================================================
echo '<h2>1. class_exists</h2>';

$classes = [
	'visuoel_field_text' ,
	'visuoel_field_password' ,
	'visuoel_field_email' ,
	'visuoel_field_file' ,
	'visuoel_field_textarea' ,
	'visuoel_field_select' ,
	'visuoel_field_checkbox' ,
	'visuoel_field_radio' ,
	'visuoel_field_checkbox_group' ,
	'visuoel_field_radio_group' ,
];
foreach ( $classes as $c ) {
	echo htmlspecialchars( $c ) . ': <b>' . var_export( class_exists( $c ) , true ) . '</b> (ожидаем true)<br>';
}

# =====================================================
# 2. field_text
# =====================================================
echo '<hr><h2>2. field_text — label + hint</h2>';

$ve = new visuoel( );
$f = $ve->field( )->text( );
$f->data( )->name = 'login';
$f->data( )->label( 'Логин' )->hint( 'Латиница, 3-32 символа' );

$ve->init( );
$h = $ve->release( );

needles( $h , [
	'<div class="field field_text">' ,
	'<div class="box">' ,
	'<label class="field-label" for="field_login">' ,
	'>Логин</label>' ,
	'<div class="border-box">' ,
	'<input type="text" id="field_login" name="login"' ,
	'<div class="hint">Латиница, 3-32 символа</div>' ,
] );
dump_html( $h , '3. HTML field_text' );
live( $h , '4. Live field_text' );

# =====================================================
# 3. field_text — hide_label + hide_hint
# =====================================================
echo '<hr><h2>5. field_text — hide_label + hide_hint</h2>';

$ve2 = new visuoel( );
$f2 = $ve2->field( )->text( );
$f2->data( )->name = 'hidden_label';
$f2->data( )->label( 'Скрытый' )->hint( 'Скрытый hint' );
$f2->data( )->hide_label( )->hide_hint( );

$ve2->init( );
$h2 = $ve2->release( );

echo 'label отсутствует: <b>' . var_export( ! str_contains( $h2 , 'Скрытый' ) , true ) . '</b><br>';
echo 'hint отсутствует: <b>' . var_export( ! str_contains( $h2 , 'Скрытый hint' ) , true ) . '</b><br>';
dump_html( $h2 , '6. HTML field_text hide' );

# =====================================================
# 4. field_checkbox
# =====================================================
echo '<hr><h2>7. field_checkbox</h2>';

$ve3 = new visuoel( );
$c = $ve3->field( )->checkbox( );
$c->data( )->name = 'remember';
$c->data( )->label( 'Запомнить меня' );
$c->data( )->checked = true;

$ve3->init( );
$h3 = $ve3->release( );

needles( $h3 , [
	'<div class="field field_checkbox">' ,
	'<label class="field-label">' ,
	'<span class="border-box">' ,
	'type="checkbox" id="field_remember" name="remember"' ,
	'checked="checked"' ,
	'<span class="field-name">Запомнить меня</span>' ,
] );
dump_html( $h3 , '8. HTML field_checkbox' );
live( $h3 , '9. Live field_checkbox' );

# =====================================================
# 5. field_radio
# =====================================================
echo '<hr><h2>10. field_radio</h2>';

$ve4 = new visuoel( );
$r = $ve4->field( )->radio( );
$r->data( )->name  = 'gender';
$r->data( )->value = 'm';
$r->data( )->label( 'Мужской' );

$ve4->init( );
$h4 = $ve4->release( );

needles( $h4 , [
	'<div class="field field_radio">' ,
	'type="radio" id="field_gender_m" name="gender" value="m"' ,
	'<span class="field-name">Мужской</span>' ,
] );
dump_html( $h4 , '11. HTML field_radio' );

# =====================================================
# 6. field_textarea
# =====================================================
echo '<hr><h2>12. field_textarea</h2>';

$ve5 = new visuoel( );
$t5 = $ve5->field( )->textarea( );
$t5->data( )->name = 'bio';
$t5->data( )->label( 'О себе' )->hint( 'Пара слов, необязательно' );
$t5->data( )->rows = 4;
$t5->data( )->cols = 40;

$ve5->init( );
$h5 = $ve5->release( );

needles( $h5 , [
	'<div class="field field_textarea">' ,
	'<label class="field-label" for="field_bio">' ,
	'<div class="border-box">' ,
	'<textarea id="field_bio" name="bio" rows="4" cols="40">' ,
	'</textarea>' ,
] );
dump_html( $h5 , '13. HTML field_textarea' );
live( $h5 , '14. Live field_textarea' );

# =====================================================
# 7. field_select
# =====================================================
echo '<hr><h2>15. field_select</h2>';

$ve6 = new visuoel( );
$s6 = $ve6->field( )->select( );
$s6->data( )->name = 'city';
$s6->data( )->label( 'Город' )->hint( 'Где вы живёте' );
$s6->data( )->options = [ '' => '— выберите —' , 'msk' => 'Москва' , 'spb' => 'Санкт-Петербург' ];

$ve6->init( );
$h6 = $ve6->release( );

needles( $h6 , [
	'<div class="field field_select">' ,
	'<label class="field-label" for="field_city">' ,
	'<div class="border-box">' ,
	'<select id="field_city" name="city">' ,
	'<option value="msk">Москва</option>' ,
	'<option value="spb">Санкт-Петербург</option>' ,
	'</select>' ,
] );
dump_html( $h6 , '16. HTML field_select' );
live( $h6 , '17. Live field_select' );

# =====================================================
# 8. field_password / field_email / field_file
# =====================================================
echo '<hr><h2>18. field_password / field_email / field_file</h2>';

$ve7 = new visuoel( );
$p = $ve7->field( )->password( );
$p->data( )->name = 'pass';
$p->data( )->label( 'Пароль' );

$e = $ve7->field( )->email( );
$e->data( )->name = 'mail';
$e->data( )->label( 'Email' );

$fl = $ve7->field( )->file( );
$fl->data( )->name = 'avatar';
$fl->data( )->label( 'Аватар' );
$fl->data( )->accept = 'image/*';

$ve7->init( );
$h7 = $ve7->release( );

needles( $h7 , [
	'<div class="field field_password">' ,
	'type="password" id="field_pass" name="pass"' ,
	'<div class="field field_email">' ,
	'type="email" id="field_mail" name="mail"' ,
	'<div class="field field_file">' ,
	'type="file" id="field_avatar" name="avatar"' ,
	'accept="image/*"' ,
] );
dump_html( $h7 , '19. HTML thin fields' );
live( $h7 , '20. Live thin fields' );

# =====================================================
# 9. field_checkbox_group
# =====================================================
echo '<hr><h2>21. field_checkbox_group</h2>';

$ve8 = new visuoel( );
$g8 = $ve8->field( )->checkbox_group( );
$g8->data( )->label( 'Интересы' )->hint( 'Выберите одно или несколько' );

$c1 = $ve8->field( )->checkbox( );
$c1->data( )->name = 'interests[]';
$c1->data( )->label( 'Спорт' )->value( 'sport' );
$g8->add( $c1 );

$c2 = $ve8->field( )->checkbox( );
$c2->data( )->name = 'interests[]';
$c2->data( )->label( 'Музыка' )->value( 'music' )->checked( true );
$g8->add( $c2 );

$ve8->init( );
$h8 = $ve8->release( );

needles( $h8 , [
	'<div class="field field_checkbox_group">' ,
	'<div class="field-label">Интересы</div>' ,
	'<div class="options">' ,
	'id="field_interests_sport"' ,
	'id="field_interests_music"' ,
	'<span class="field-name">Спорт</span>' ,
	'<span class="field-name">Музыка</span>' ,
	'checked="checked"' ,
	'<div class="hint">Выберите одно или несколько</div>' ,
] );

# проверка, что группа ровно одна
$count_groups = substr_count( $h8 , '<div class="field field_checkbox_group">' );
echo 'групп ровно одна: <b>' . var_export( $count_groups === 1 , true ) . "</b> (найдено: $count_groups)<br>";

dump_html( $h8 , '22. HTML field_checkbox_group' );
live( $h8 , '23. Live field_checkbox_group' );

# =====================================================
# 10. field_radio_group
# =====================================================
echo '<hr><h2>24. field_radio_group</h2>';

$ve9 = new visuoel( );
$g9 = $ve9->field( )->radio_group( );
$g9->data( )->label( 'Пол' );

$r1 = $ve9->field( )->radio( );
$r1->data( )->name = 'gender';
$r1->data( )->label( 'Мужской' )->value( 'm' );
$g9->add( $r1 );

$r2 = $ve9->field( )->radio( );
$r2->data( )->name = 'gender';
$r2->data( )->label( 'Женский' )->value( 'f' )->checked( true );
$g9->add( $r2 );

$ve9->init( );
$h9 = $ve9->release( );

needles( $h9 , [
	'<div class="field field_radio_group">' ,
	'<div class="field-label">Пол</div>' ,
	'<div class="options">' ,
	'id="field_gender_m"' ,
	'id="field_gender_f"' ,
	'<span class="field-name">Мужской</span>' ,
	'<span class="field-name">Женский</span>' ,
	'checked="checked"' ,
] );

$count_groups9 = substr_count( $h9 , '<div class="field field_radio_group">' );
echo 'групп ровно одна: <b>' . var_export( $count_groups9 === 1 , true ) . "</b> (найдено: $count_groups9)<br>";

dump_html( $h9 , '25. HTML field_radio_group' );
live( $h9 , '26. Live field_radio_group' );

echo '<hr>done';
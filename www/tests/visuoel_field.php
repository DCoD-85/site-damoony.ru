<?PHP
# Visuoel field tests — isolated
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

require $root_dir . '/sources/visuo/sources/visuoel/visuoel_form/visuoel_form_form.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_form.php';

require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input__element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_hidden.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_password.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_submit.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_reset.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_button.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_email.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_file.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_textarea.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_select.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_checkbox.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_radio.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input.php';

require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field__element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_password.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_email.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_file.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_textarea.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_select.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_checkbox.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_radio.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field.php';

echo '<h1>visuoel — field tests</h1>';

echo '<h2>1. class_exists visuoel_field</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_field' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_field_text</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_field_text' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. class_exists visuoel_field_password</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_field_password' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. class_exists visuoel_field__element</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_field__element' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>5. visuoel_field extends visuoel_group</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_field' , 'visuoel_group' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>6. visuoel_field_text extends visuoel_field__element</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_field_text' , 'visuoel_field__element' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>7. visuoel_field__element__data extends visuoel_input__element__data</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_field__element__data' , 'visuoel_input__element__data' ) , true ) . '</b> (ожидаем true)<br>';

# --- chain ---
echo '<h2>8. new visuoel()->field() → visuoel_field</h2>';
$root = new visuoel( );
$grp  = $root->field( );
echo 'class: <b>' . get_class( $grp ) . '</b> (ожидаем visuoel_field)<br>';
echo 'parent is $root: <b>' . var_export( $grp->parent === $root , true ) . '</b> (ожидаем true)<br>';

echo '<h2>9. -&gt;text() → visuoel_field_text</h2>';
$t = $grp->text( );
echo 'class: <b>' . get_class( $t ) . '</b> (ожидаем visuoel_field_text)<br>';
echo 'parent is $grp: <b>' . var_export( $t->parent === $grp , true ) . '</b> (ожидаем true)<br>';

echo '<h2>10. -&gt;password() → visuoel_field_password</h2>';
$p = $grp->password( );
echo 'class: <b>' . get_class( $p ) . '</b> (ожидаем visuoel_field_password)<br>';

echo '<h2>11. data class text</h2>';
echo 'class: <b>' . get_class( $t->data( ) ) . '</b> (ожидаем visuoel_field_text__data)<br>';

echo '<h2>12. multi: два text() создают разные</h2>';
$t2 = $grp->text( );
echo 't1 !== t2: <b>' . var_export( $t !== $t2 , true ) . '</b> (ожидаем true)<br>';
echo 'elements count: <b>' . count( $grp->elements ) . '</b> (ожидаем 3)<br>';

# --- set ---
echo '<h2>13. data-&gt;set(name, value, placeholder)</h2>';
$t->data( )->set( 'login' , 'admin' , 'Email' );
echo 'name: <b>' . $t->data( )->name . '</b> (ожидаем login)<br>';
echo 'value: <b>' . $t->data( )->value . '</b> (ожидаем admin)<br>';
echo 'placeholder: <b>' . $t->data( )->placeholder . '</b> (ожидаем Email)<br>';

echo '<h2>14. label() / hint() / error()</h2>';
$t->data( )->label( 'Логин' , 'обязательное' );
$t->data( )->hint( 'Email или телефон' );
echo 'label: <b>' . $t->data( )->label . '</b> (ожидаем Логин)<br>';
echo 'label_comment: <b>' . $t->data( )->label_comment . '</b> (ожидаем обязательное)<br>';
echo 'hint: <b>' . $t->data( )->hint . '</b> (ожидаем Email или телефон)<br>';

echo '<h2>15. required()</h2>';
$t->data( )->required( );
echo 'required: <b>' . var_export( $t->data( )->required , true ) . '</b> (ожидаем true)<br>';

# --- init + release ---
echo '<h2>16. init() + release()</h2>';
$root->init( );
$html = $root->release( );
echo 'is_string: <b>' . var_export( is_string( $html ) , true ) . '</b><br>';
echo 'len: <b>' . strlen( $html ) . '</b><br>';

echo '<h2>17. HTML содержит структуру field</h2>';
foreach ( [
	'<div class="field field_text">' ,
	'<label' ,
	'for="field_login"' ,
	'<input type="text"' ,
	'name="login"' ,
	'id="field_login"' ,
	'placeholder="Email"' ,
	'required="required"' ,
	'<div class="hint">' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $html , $needle ) , true ) . '</b><br>';
}

echo '<h2>18. HTML НЕ содержит &lt;div class="input"&gt; внутри field (bugfix: bypass input group)</h2>';
echo 'no double-wrap: <b>' . var_export( ! str_contains( $html , 'class="input"' ) , true ) . '</b> (ожидаем true)<br>';

# --- сценарий: форма логина ---
echo '<hr>';
echo '<h1>Сценарий: форма логина на field + input</h1>';

$ve = new visuoel( );
$form = $ve->form( )->form( );
$form->data( )->set( 'login_form' , 'post' , '/auth' , title_text : 'Вход' );

$csrf = new visuoel( );
$csrf->input( )->hidden( )->data( )->set( 'csrf' , 'abc123' );

$login = new visuoel( );
$login->field( )->text( )->data( )->set( 'login' )->placeholder( 'Email' )->label( 'Логин' )->hint( 'Email' )->required( );

$pass = new visuoel( );
$pass->field( )->password( )->data( )->set( 'password' )->label( 'Пароль' )->required( );

$submit = new visuoel( );
$submit->input( )->submit( )->data( )->set( 'submit' )->value( 'Войти' );

$form->data( )->add( $csrf )->add( $login )->add( $pass )->add( $submit );

$ve->init( );
$h = $ve->release( );

echo '<h2>19. Форма: ожидаемые фрагменты</h2>';
foreach ( [
	'<form action="/auth"' ,
	'type="hidden"' ,
	'name="csrf"' ,
	'<div class="field field_text">' ,
	'<label for="field_login">' ,
	'Логин' ,
	'<input type="text"' ,
	'id="field_login"' ,
	'name="login"' ,
	'required="required"' ,
	'<div class="hint">Email</div>' ,
	'<div class="field field_password">' ,
	'<input type="password"' ,
	'id="field_password"' ,
	'<input type="submit"' ,
	'value="Войти"' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
}

echo '<h2>20. Полный HTML</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h )
	. '</pre>';

echo '<h2>21. Живой рендер</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

# --- новые типы ---
echo '<hr>';
echo '<h1>Новые типы: email, file, textarea</h1>';

$ve = new visuoel( );
$grp = $ve->field( );

$em = $grp->email( );
$em->data( )->set( 'email' )->label( 'Email' )->required( );

$fl = $grp->file( );
$fl->data( )->set( 'avatar' )->label( 'Аватар' )->accept( 'image/*' );

$ta = $grp->textarea( );
$ta->data( )->set( 'bio' )->label( 'О себе' )->rows( 5 )->cols( 40 )->placeholder( 'Расскажите о себе' );

$ve->init( );
$h = $ve->release( );

echo '<h2>22. field/email</h2>';
echo 'type=email: <b>' . var_export( str_contains( $h , 'type="email"' ) , true ) . '</b><br>';
echo 'id=field_email: <b>' . var_export( str_contains( $h , 'id="field_email"' ) , true ) . '</b><br>';
echo 'label Email: <b>' . var_export( str_contains( $h , '>Email<' ) , true ) . '</b><br>';

echo '<h2>23. field/file</h2>';
echo 'type=file: <b>' . var_export( str_contains( $h , 'type="file"' ) , true ) . '</b><br>';
echo 'accept=image/*: <b>' . var_export( str_contains( $h , 'accept="image/*"' ) , true ) . '</b><br>';

echo '<h2>24. field/textarea</h2>';
echo '&lt;textarea: <b>' . var_export( str_contains( $h , '<textarea' ) , true ) . '</b><br>';
echo 'rows=5: <b>' . var_export( str_contains( $h , 'rows="5"' ) , true ) . '</b><br>';
echo 'cols=40: <b>' . var_export( str_contains( $h , 'cols="40"' ) , true ) . '</b><br>';
echo 'placeholder: <b>' . var_export( str_contains( $h , 'placeholder="Расскажите о себе"' ) , true ) . '</b><br>';
echo '&lt;/textarea&gt;: <b>' . var_export( str_contains( $h , '</textarea>' ) , true ) . '</b><br>';

echo '<h2>25. Полный HTML (новые типы)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h )
	. '</pre>';

echo '<h2>26. Живой рендер (новые типы)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

echo '<hr>';
echo '<h1>Select</h1>';

$ve = new visuoel( );
$grp = $ve->field( );

$city = $grp->select( );
$city->data( )->set( 'city' )->label( 'Город' )->required( );
$city->data( )->placeholder_option( '— выберите —' );
$city->data( )->options( [
	'msk' => 'Москва' ,
	'spb' => 'Санкт-Петербург' ,
	'nsk' => 'Новосибирск' ,
] );
$city->data( )->selected( 'msk' );

$ve->init( );
$h = $ve->release( );

echo '<h2>27. field/select — структура</h2>';
foreach ( [
	'<div class="field field_select">' ,
	'<label for="field_city">Город</label>' ,
	'<select' ,
	'id="field_city"' ,
	'name="city"' ,
	'required="required"' ,
	'<option value="">— выберите —</option>' ,
	'<option value="msk" selected="selected">Москва</option>' ,
	'<option value="spb">Санкт-Петербург</option>' ,
	'<option value="nsk">Новосибирск</option>' ,
	'</select>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
}

echo '<h2>28. field/select — optgroup + multiple</h2>';
$ve2 = new visuoel( );
$roles = $ve2->field( )->select( );
$roles->data( )->set( 'roles' )->label( 'Роли' )->multiple( );
$roles->data( )->options( [
	'admin' => 'Администратор' ,
	'mod'   => 'Модератор' ,
	'user'  => 'Пользователь' ,
] );
$roles->data( )->selected( [ 'admin' , 'user' ] );

$ve2->init( );
$h2 = $ve2->release( );

echo 'multiple: <b>' . var_export( str_contains( $h2 , 'multiple="multiple"' ) , true ) . '</b><br>';
echo 'admin selected: <b>' . var_export( str_contains( $h2 , 'value="admin" selected="selected"' ) , true ) . '</b><br>';
echo 'user selected: <b>' . var_export( str_contains( $h2 , 'value="user" selected="selected"' ) , true ) . '</b><br>';
echo 'mod NOT selected: <b>' . var_export( ! str_contains( $h2 , 'value="mod" selected' ) , true ) . '</b><br>';

echo '<h2>29. Полный HTML (select)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h )
	. '</pre>';

echo '<h2>30. Живой рендер (select)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

echo '<h2>31. Живой рендер (select multiple)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h2 . '</div>';

echo '<hr>';
echo '<h1>checkbox / radio</h1>';

$ve = new visuoel( );

$agree = $ve->field( )->checkbox( );
$agree->data( )->set( 'agree' )->label( 'Согласен с условиями' )->required( );

$news = $ve->field( )->checkbox( );
$news->data( )->set( 'news' )->label( 'Подписаться на новости' )->checked( )->hint( 'Раз в неделю' );

$ve->init( );
$h = $ve->release( );

echo '<h2>32. field/checkbox — структура</h2>';
foreach ( [
	'<div class="field field_checkbox">' ,
	'<label>' ,
	'<input type="checkbox"' ,
	'id="field_agree"' ,
	'name="agree"' ,
	'value="1"' ,
	'required="required"' ,
	'Согласен с условиями' ,
	'</label>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
}

echo '<h2>33. field/checkbox — checked + hint</h2>';
echo 'checked="checked": <b>' . var_export( str_contains( $h , 'checked="checked"' ) , true ) . '</b><br>';
echo 'Подписаться: <b>' . var_export( str_contains( $h , 'Подписаться на новости' ) , true ) . '</b><br>';
echo 'hint: <b>' . var_export( str_contains( $h , '<div class="hint">Раз в неделю</div>' ) , true ) . '</b><br>';

echo '<h2>34. field/radio — структура</h2>';
$ve2 = new visuoel( );

$m = $ve2->field( )->radio( );
$m->data( )->set( 'gender' , 'm' )->label( 'Мужской' )->checked( );

$f = $ve2->field( )->radio( );
$f->data( )->set( 'gender' , 'f' )->label( 'Женский' );

$ve2->init( );
$h2 = $ve2->release( );

echo 'field_radio: <b>' . var_export( str_contains( $h2 , 'class="field field_radio"' ) , true ) . '</b><br>';
echo 'type="radio": <b>' . var_export( str_contains( $h2 , 'type="radio"' ) , true ) . '</b><br>';
echo 'id=field_gender_m: <b>' . var_export( str_contains( $h2 , 'id="field_gender_m"' ) , true ) . '</b><br>';
echo 'id=field_gender_f: <b>' . var_export( str_contains( $h2 , 'id="field_gender_f"' ) , true ) . '</b><br>';
echo 'name="gender" x2: <b>' . substr_count( $h2 , 'name="gender"' ) . '</b> (ожидаем 2)<br>';
echo 'checked="checked": <b>' . var_export( str_contains( $h2 , 'checked="checked"' ) , true ) . '</b><br>';
echo 'Мужской: <b>' . var_export( str_contains( $h2 , 'Мужской' ) , true ) . '</b><br>';
echo 'Женский: <b>' . var_export( str_contains( $h2 , 'Женский' ) , true ) . '</b><br>';

echo '<h2>35. Полный HTML (checkbox)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h )
	. '</pre>';

echo '<h2>36. Полный HTML (radio)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h2 )
	. '</pre>';

echo '<h2>37. Живой рендер (checkbox)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

echo '<h2>38. Живой рендер (radio)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h2 . '</div>';

echo '<hr>done';
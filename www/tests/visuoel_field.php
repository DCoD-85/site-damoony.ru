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
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input.php';

require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_password.php';

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

echo '<hr>done';
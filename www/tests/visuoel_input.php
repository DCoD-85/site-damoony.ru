<?PHP
# Visuoel input tests — isolated
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

require $root_dir . '/sources/visuo/sources/visuoel/visuoel_tabs/visuoel_tabs_page.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_tabs.php';

require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input__element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_hidden.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_password.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_submit.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_reset.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_button.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input.php';

echo '<h1>visuoel — input tests</h1>';

echo '<h2>1. class_exists visuoel_input</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_input' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_input_text</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_input_text' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. class_exists visuoel_input_hidden</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_input_hidden' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. class_exists visuoel_input_password</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_input_password' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>5. class_exists visuoel_input_submit</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_input_submit' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>6. visuoel_input extends visuoel_group</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_input' , 'visuoel_group' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>7. visuoel_input_text extends visuoel_input__element</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_input_text' , 'visuoel_input__element' ) , true ) . '</b> (ожидаем true)<br>';

# --- chain ---
echo '<h2>8. new visuoel()->input() → visuoel_input</h2>';
$root = new visuoel( );
$grp  = $root->input( );
echo 'class: <b>' . get_class( $grp ) . '</b> (ожидаем visuoel_input)<br>';
echo 'css_class: <b>' . $grp->css_class( ) . '</b> (ожидаем input)<br>';
echo 'parent is $root: <b>' . var_export( $grp->parent === $root , true ) . '</b> (ожидаем true)<br>';

echo '<h2>9. -&gt;text() → visuoel_input_text</h2>';
$t = $grp->text( );
echo 'class: <b>' . get_class( $t ) . '</b> (ожидаем visuoel_input_text)<br>';
echo 'parent is $grp: <b>' . var_export( $t->parent === $grp , true ) . '</b> (ожидаем true)<br>';

echo '<h2>10. data class text</h2>';
echo 'class: <b>' . get_class( $t->data( ) ) . '</b> (ожидаем visuoel_input_text__data)<br>';

echo '<h2>11. input() singleton на корне</h2>';
echo 'same: <b>' . var_export( $grp === $root->input( ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>12. multi: text() два раза создаёт разные</h2>';
$t2 = $grp->text( );
echo 't1 !== t2: <b>' . var_export( $t !== $t2 , true ) . '</b> (ожидаем true)<br>';
echo 'elements count: <b>' . count( $grp->elements ) . '</b> (ожидаем 2)<br>';

# --- set ---
echo '<h2>13. data-&gt;set(name, value, placeholder)</h2>';
$t3 = new visuoel_input_text( );
$t3->data( )->set( 'login' , 'admin' , 'Email' );
echo 'name: <b>' . $t3->data( )->name . '</b><br>';
echo 'value: <b>' . $t3->data( )->value . '</b><br>';
echo 'placeholder: <b>' . $t3->data( )->placeholder . '</b><br>';

echo '<h2>14. required() / readonly() / disabled()</h2>';
$t3->data( )->required( )->readonly( );
echo 'required: <b>' . var_export( $t3->data( )->required , true ) . '</b><br>';
echo 'readonly: <b>' . var_export( $t3->data( )->readonly , true ) . '</b><br>';
echo 'disabled: <b>' . var_export( $t3->data( )->disabled , true ) . '</b><br>';

# --- init + release ---
echo '<h2>15. root-&gt;init() — vt_start/vt_end</h2>';
$root->init( );
echo 'root vt_start: <b>' . get_class( $root->vt_start ) . '</b><br>';
echo 'input vt_start: <b>' . get_class( $grp->vt_start ) . '</b><br>';
echo 'text vt_start: <b>' . get_class( $t->vt_start ) . '</b><br>';
echo 'text vt_end === vt_start: <b>' . var_export( $t->vt_start === $t->vt_end , true ) . '</b> (ожидаем true — лист)<br>';

echo '<h2>16. release()</h2>';
$html = $root->release( );
echo 'is_string: <b>' . var_export( is_string( $html ) , true ) . '</b><br>';
echo 'len: <b>' . strlen( $html ) . '</b><br>';

echo '<h2>17. HTML содержит &lt;div class="input"&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $html , 'class="input"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>18. HTML содержит &lt;input type="text"&gt;</h2>';
echo 'contains type: <b>' . var_export( str_contains( $html , 'type="text"' ) , true ) . '</b><br>';

echo '<hr>';
echo '<h1>Полный сценарий: форма логина</h1>';

# --- форма логина ---
echo '<h2>19. Форма с тремя полями</h2>';
$ve = new visuoel( );
$form = $ve->form( )->form( );
$form->data( )->set( 'login' , 'post' , '/auth' , title_text : 'Вход' );

# hidden csrf
$csrf = new visuoel( );
$csrf->input( )->hidden( )->data( )->set( 'csrf' , 'abc123' );

# login
$login = new visuoel( );
$login->input( )->text( )->data( )->set( 'login' )->placeholder( 'Email' )->required( );

# password
$pass = new visuoel( );
$pass->input( )->password( )->data( )->set( 'password' )->required( );

# submit
$submit = new visuoel( );
$submit->input( )->submit( )->data( )->set( 'submit' )->value( 'Войти' );

$form->data( )->add( $csrf )->add( $login )->add( $pass )->add( $submit );

$ve->init( );
$h = $ve->release( );

echo '<h2>20. Проверка HTML формы</h2>';
foreach ( [
	'<form' ,
	'action="/auth"' ,
	'name="login"' ,
	'type="hidden"' ,
	'name="csrf"' ,
	'type="text"' ,
	'type="password"' ,
	'type="submit"' ,
	'value="Войти"' ,
	'placeholder="Email"' ,
	'required' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
}

echo '<h2>21. Полный HTML</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h )
	. '</pre>';

echo '<h2>22. Живой рендер</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

echo '<hr>done';
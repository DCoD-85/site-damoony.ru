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
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group_element.php';require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input__element.php';
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
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_checkbox_group.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_input/visuoel_input_radio_group.php';
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
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_checkbox_group.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field/visuoel_field_radio_group.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_field.php';

require $root_dir . '/sources/visuo/sources/visuoel/visuoel_form/visuoel_form_fieldset.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_form/visuoel_form_form.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_form.php';

echo '<h1>visuoel — form/fieldset test</h1>';

echo '<h2>1. class_exists visuoel_form_fieldset</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_form_fieldset' ) , true ) . '</b><br>';

echo '<h2>2. fieldset() через form_form</h2>';
$ve = new visuoel( );
$form = $ve->form( )->form( );
$form->data( )->set( 'reg' , 'post' , '/reg' );

$fs = $form->fieldset( );
$fs->data( )->set( 'Личные данные' );

$login = new visuoel( );
$login->field( )->text( )->data( )->set( 'login' )->label( 'Логин' )->required( );

$fs->add( $login );

$ve->init( );
$h = $ve->release( );

echo '<h2>3. HTML</h2>';
foreach ( [
	'<fieldset class="form_fieldset">' ,
	'<legend>Личные данные</legend>' ,
	'<div class="field field_text">' ,
	'<label for="field_login">Логин</label>' ,
	'</fieldset>' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $h , $needle ) , true ) . '</b><br>';
}

echo '<h2>4. Полный HTML</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h ) . '</pre>';

echo '<h2>5. Живой рендер</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $h . '</div>';

echo '<hr>done';
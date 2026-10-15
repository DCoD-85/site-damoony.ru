<?PHP
# Visuoel button tests — isolated
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
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_button__element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_button/visuoel_button_submit.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_button/visuoel_button_reset.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_button/visuoel_button_button.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_button.php';

echo '<h1>visuoel — button tests</h1>';

echo '<h2>1. class_exists visuoel_button</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_button' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_button_submit</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_button_submit' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. class_exists visuoel_button_reset</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_button_reset' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. class_exists visuoel_button_button</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_button_button' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>5. visuoel_button extends visuoel_group</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_button' , 'visuoel_group' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>6. visuoel_button_submit extends visuoel_button__element</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_button_submit' , 'visuoel_button__element' ) , true ) . '</b> (ожидаем true)<br>';

# --- chain ---
echo '<h2>7. new visuoel()->button() → visuoel_button</h2>';
$root = new visuoel( );
$grp = $root->button( );
echo 'class: <b>' . get_class( $grp ) . '</b> (ожидаем visuoel_button)<br>';
echo 'parent is $root: <b>' . var_export( $grp->parent === $root , true ) . '</b> (ожидаем true)<br>';

echo '<h2>8. -&gt;submit() → visuoel_button_submit</h2>';
$s = $grp->submit( );
echo 'class: <b>' . get_class( $s ) . '</b> (ожидаем visuoel_button_submit)<br>';
echo 'parent is $grp: <b>' . var_export( $s->parent === $grp , true ) . '</b> (ожидаем true)<br>';

echo '<h2>9. -&gt;reset() → visuoel_button_reset</h2>';
$r = $grp->reset( );
echo 'class: <b>' . get_class( $r ) . '</b> (ожидаем visuoel_button_reset)<br>';

echo '<h2>10. -&gt;button() → visuoel_button_button</h2>';
$b = $grp->button( );
echo 'class: <b>' . get_class( $b ) . '</b> (ожидаем visuoel_button_button)<br>';

echo '<h2>11. multi: три кнопки — три элемента</h2>';
echo 'elements count: <b>' . count( $grp->elements ) . '</b> (ожидаем 3)<br>';

# --- set ---
echo '<h2>12. data-&gt;set() текст кнопки</h2>';
$s->data( )->set( 'Отправить' );
$r->data( )->set( 'Сбросить' );
$b->data( )->set( 'Просто кнопка' );
echo 'submit text: <b>' . $s->data( )->text . '</b><br>';
echo 'reset text: <b>' . $r->data( )->text . '</b><br>';

echo '<h2>13. name / value / css_class</h2>';
$b->data( )->name( 'action' )->value( 'x' )->css_class( 'btn_primary' );
echo 'name: <b>' . $b->data( )->name . '</b><br>';
echo 'value: <b>' . $b->data( )->value . '</b><br>';
echo 'css_class: <b>' . $b->data( )->css_class . '</b><br>';

# --- init + release ---
echo '<h2>14. init() + release()</h2>';
$root->init( );
$html = $root->release( );
echo 'is_string: <b>' . var_export( is_string( $html ) , true ) . '</b><br>';
echo 'len: <b>' . strlen( $html ) . '</b><br>';

echo '<h2>15. HTML содержит кнопки</h2>';
foreach ( [
	'<button type="submit"' ,
	'<button type="reset"' ,
	'<button type="button"' ,
	'class="button button_submit"' ,
	'class="button button_reset"' ,
	'class="button button_button"' ,
	'Отправить' ,
	'Сбросить' ,
	'Просто кнопка' ,
	'name="action"' ,
	'value="x"' ,
	'btn_primary' ,
] as $needle ) {
	echo htmlspecialchars( $needle ) . ': <b>' . var_export( str_contains( $html , $needle ) , true ) . '</b><br>';
}

echo '<h2>16. НЕ содержит &lt;input type="submit"&gt;</h2>';
echo 'no input submit: <b>' . var_export( ! str_contains( $html , '<input' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>17. disabled</h2>';
$d = new visuoel_button_submit( );
$d->data( )->set( 'Disabled' )->disabled( );
$d->init( );
echo 'disabled="disabled": <b>' . var_export( str_contains( $d->release( ) , 'disabled="disabled"' ) , true ) . '</b><br>';

echo '<h2>18. Полный HTML</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $html )
	. '</pre>';

echo '<h2>19. Живой рендер</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $html . '</div>';

echo '<hr>done';
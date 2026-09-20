<?PHP
# Visuoel form/form tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

# visuotag stack
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';

# visuoel stack
require $root_dir . '/sources/visuo/sources/visuoel.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group_element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_form/visuoel_form_form.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_form.php';

echo '<h1>visuoel — form/form tests</h1>';

# --- class structure ---
echo '<h2>1. class_exists visuoel</h2>';
echo '<b>' . var_export( class_exists( 'visuoel' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_form</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_form' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. class_exists visuoel_form_form</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_form_form' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. visuoel_form extends visuoel_group</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_form' , 'visuoel_group' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>5. visuoel_form_form extends visuoel_group_element</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_form_form' , 'visuoel_group_element' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>6. visuoel_form__data extends visuoel_group__data</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_form__data' , 'visuoel_group__data' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>7. visuoel_form_form__data extends visuoel_group_element__data</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_form_form__data' , 'visuoel_group_element__data' ) , true ) . '</b> (ожидаем true)<br>';

# --- chain: new visuoel()->form()->form() ---
echo '<h2>8. new visuoel()</h2>';
$root = new visuoel( );
echo 'class: <b>' . get_class( $root ) . '</b> (ожидаем visuoel)<br>';
echo 'parent: <b>' . var_export( $root->parent , true ) . '</b> (ожидаем NULL)<br>';
echo 'is_root: <b>' . var_export( $root->is_root( ) , true ) . '</b> (ожидаем true)<br>';
echo 'css_class: <b>' . $root->css_class( ) . '</b> (ожидаем visuoel)<br>';

echo '<h2>9. -&gt;form() → visuoel_form</h2>';
$grp = $root->form( );
echo 'class: <b>' . get_class( $grp ) . '</b> (ожидаем visuoel_form)<br>';
echo 'parent is $root: <b>' . var_export( $grp->parent === $root , true ) . '</b> (ожидаем true)<br>';
echo 'visuoel is $root: <b>' . var_export( $grp->visuoel === $root , true ) . '</b> (ожидаем true)<br>';
echo 'css_class: <b>' . $grp->css_class( ) . '</b> (ожидаем form)<br>';

echo '<h2>10. -&gt;form() → visuoel_form_form</h2>';
$el = $grp->form( );
echo 'class: <b>' . get_class( $el ) . '</b> (ожидаем visuoel_form_form)<br>';
echo 'parent is $grp: <b>' . var_export( $el->parent === $grp , true ) . '</b> (ожидаем true)<br>';
echo 'visuoel is $root: <b>' . var_export( $el->visuoel === $root , true ) . '</b> (ожидаем true)<br>';
echo 'css_class: <b>' . $el->css_class( ) . '</b> (ожидаем form_form)<br>';
echo 'tag: <b>' . $el->tag( ) . '</b> (ожидаем form)<br>';

# --- singletons ---
echo '<h2>11. form() singleton на группе</h2>';
$el2 = $grp->form( );
echo 'same: <b>' . var_export( $el === $el2 , true ) . '</b> (ожидаем true)<br>';

echo '<h2>12. form() singleton на корне</h2>';
$grp2 = $root->form( );
echo 'same: <b>' . var_export( $grp === $grp2 , true ) . '</b> (ожидаем true)<br>';

# --- data ---
echo '<h2>13. $el-&gt;data() → visuoel_form_form__data</h2>';
$data = $el->data( );
echo 'class: <b>' . get_class( $data ) . '</b> (ожидаем visuoel_form_form__data)<br>';
echo 'node is $el: <b>' . var_export( $data->node === $el , true ) . '</b> (ожидаем true)<br>';

echo '<h2>14. $grp-&gt;get_form_form__data() === $el-&gt;data()</h2>';
echo 'same: <b>' . var_export( $grp->get_form_form__data( ) === $data , true ) . '</b> (ожидаем true)<br>';

# --- init ---
echo '<h2>15. init() на корне создаёт vt_start / vt_end</h2>';
$root->init( );
echo 'vt_start class: <b>' . ( $root->vt_start !== null ? get_class( $root->vt_start ) : 'NULL' ) . '</b> (ожидаем visuotag)<br>';
echo 'vt_end class: <b>' . ( $root->vt_end !== null ? get_class( $root->vt_end ) : 'NULL' ) . '</b> (ожидаем visuotag)<br>';

# --- release ---
echo '<h2>16. release() returns string</h2>';
$html = $root->release( );
echo 'is_string: <b>' . var_export( is_string( $html ) , true ) . '</b> (ожидаем true)<br>';
echo 'len &gt; 0: <b>' . var_export( strlen( $html ) > 0 , true ) . '</b> (ожидаем true)<br>';

echo '<h2>17. HTML contains &lt;div class="visuoel"&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $html , 'class="visuoel"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>18. HTML contains &lt;div class="form"&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $html , 'class="form"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>19. HTML contains &lt;form class="form_form"&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $html , '<form' ) && str_contains( $html , 'class="form_form"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>20. show() === release()</h2>';
echo 'same: <b>' . var_export( $root->show( ) === $html , true ) . '</b> (ожидаем true)<br>';

# --- html preview ---
echo '<hr>';
echo '<h2>Полный HTML</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $html )
	. '</pre>';

echo '<hr>';
echo '<h2>Живой рендер (в iframe-подобном div)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $html . '</div>';

echo '<hr>done';
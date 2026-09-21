<?PHP
# Test: visuoelement()->form()->form() chain — isolated
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

# visuoelement base + form
require $root_dir . '/sources/visuo/sources/visuoelement.php';
require $root_dir . '/sources/visuo/sources/visuoelements/form.php';
require $root_dir . '/sources/visuo/sources/visuoelements/form/form.php';

echo '<h1>visuoelement()-&gt;form()-&gt;form() — isolated</h1>';

# --- 1. class_exists ---
echo '<h2>1. class_exists visuoelement</h2>';
echo '<b>' . var_export( class_exists('visuoelement') , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoelement_form</h2>';
echo '<b>' . var_export( class_exists('visuoelement_form') , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. class_exists visuoelement_form_form</h2>';
echo '<b>' . var_export( class_exists('visuoelement_form_form') , true ) . '</b> (ожидаем true)<br>';

# --- 2. chain ---
$ve = new visuoelement( );
echo '<h2>4. new visuoelement()</h2>';
echo 'class: <b>' . get_class( $ve ) . '</b> (ожидаем visuoelement)<br>';
echo 'visuoelement_group: <b>' . var_export( $ve->visuoelement_group , true ) . '</b> (ожидаем NULL)<br>';

$grp = $ve->form( );
echo '<h2>5. $ve-&gt;form() → visuoelement_form</h2>';
echo 'class: <b>' . get_class( $grp ) . '</b> (ожидаем visuoelement_form)<br>';
echo '$ve-&gt;visuoelement_group set: <b>' . var_export( $ve->visuoelement_group === $grp , true ) . '</b> (ожидаем true)<br>';
echo '$ve-&gt;visuoelement_form set: <b>' . var_export( $ve->visuoelement_form === $grp , true ) . '</b> (ожидаем true)<br>';

$data = new visuoelement_form_form__data( );
echo '<h2>6. new visuoelement_form_form__data()</h2>';
echo 'class: <b>' . get_class( $data ) . '</b> (ожидаем visuoelement_form_form__data)<br>';
echo 'visuoelement: <b>' . var_export( $data->visuoelement , true ) . '</b> (ожидаем NULL — пока не передан в group)<br>';

$el = $grp->form( $data );
echo '<h2>7. $grp-&gt;form($data) → visuoelement_form_form</h2>';
echo 'class: <b>' . get_class( $el ) . '</b> (ожидаем visuoelement_form_form)<br>';
echo 'data-&gt;visuoelement is $ve: <b>' . var_export( $data->visuoelement === $ve , true ) . '</b> (ожидаем true)<br>';
echo 'data-&gt;visuoelement_group is $grp: <b>' . var_export( $data->visuoelement_group === $grp , true ) . '</b> (ожидаем true)<br>';
echo '$grp-&gt;visuoelement_group_element is $el: <b>' . var_export( $grp->visuoelement_group_element === $el , true ) . '</b> (ожидаем true)<br>';

echo '<h2>8. $ve-&gt;get_form_form__data() returns $data</h2>';
echo 'same: <b>' . var_export( $ve->get_form_form__data( ) === $data , true ) . '</b> (ожидаем true)<br>';

# --- 3. set ---
$data->set( 'testform' , 'POST' , '/submit' , form_jquery : true , title_text : 'Test Form' );
echo '<h2>9. data-&gt;set() populates fields</h2>';
echo 'form_name: <b>' . $data->form_name . '</b> (ожидаем testform)<br>';
echo 'form_method: <b>' . $data->form_method . '</b> (ожидаем post)<br>';
echo 'form_action: <b>' . $data->form_action . '</b> (ожидаем /submit)<br>';
echo 'form_jquery: <b>' . var_export( $data->form_jquery , true ) . '</b> (ожидаем true)<br>';
echo 'title_text: <b>' . $data->title_text . '</b> (ожидаем Test Form)<br>';

# --- 4. add element (visuotag) ---
$vt_child = new visuotag( );
$vt_child->tag( 'p' )->content( 'hello from child' );
$data->add( $vt_child );
echo '<h2>10. data-&gt;add(visuotag) → elements count 1</h2>';
echo 'count: <b>' . count( $data->elements ) . '</b> (ожидаем 1)<br>';

# --- 5. init ---
echo '<h2>11. init() — passes (no fatal)</h2>';
$err = null;
try {
	$data->init( );
} catch ( \Throwable $e ) {
	$err = get_class( $e ) . ': ' . $e->getMessage( ) . ' @ ' . $e->getFile( ) . ':' . $e->getLine( );
}
echo 'no error: <b>' . var_export( $err === null , true ) . '</b> (ожидаем true)';
if( $err !== null ) echo '<br>error: <b>' . htmlspecialchars( $err ) . '</b>';
echo '<br>';

echo '<h2>12. after init — $ve-&gt;visuotag_start is visuotag</h2>';
echo 'class: <b>' . ( $ve->visuotag_start !== null ? get_class( $ve->visuotag_start ) : 'NULL' ) . '</b> (ожидаем visuotag)<br>';

echo '<h2>13. show() — HTML</h2>';
$err = null;
$html = null;
try {
	$html = $ve->show( );
} catch ( \Throwable $e ) {
	$err = get_class( $e ) . ': ' . $e->getMessage( ) . ' @ ' . $e->getFile( ) . ':' . $e->getLine( );
}
echo 'no error: <b>' . var_export( $err === null , true ) . '</b> (ожидаем true)';
if( $err !== null ) echo '<br>error: <b>' . htmlspecialchars( $err ) . '</b>';
echo '<br>';

if( is_string( $html ) ) {
	echo '<h2>14. HTML contains &lt;form&gt;</h2>';
	echo 'contains: <b>' . var_export( str_contains( $html , '<form' ) , true ) . '</b> (ожидаем true)<br>';

	echo '<h2>15. HTML contains class="blockform"</h2>';
	echo 'contains: <b>' . var_export( str_contains( $html , 'blockform' ) , true ) . '</b> (ожидаем true)<br>';

	echo '<h2>16. HTML contains class="form_body"</h2>';
	echo 'contains: <b>' . var_export( str_contains( $html , 'form_body' ) , true ) . '</b> (ожидаем true)<br>';

	echo '<h2>17. HTML contains child &lt;p&gt;</h2>';
	echo 'contains: <b>' . var_export( str_contains( $html , 'hello from child' ) , true ) . '</b> (ожидаем true)<br>';

	echo '<h2>18. HTML contains title "Test Form"</h2>';
	echo 'contains: <b>' . var_export( str_contains( $html , 'Test Form' ) , true ) . '</b> (ожидаем true)<br>';

	echo '<h2>19. HTML length</h2>';
	echo 'len: <b>' . strlen( $html ) . '</b> (ожидаем &gt; 0)<br>';

	echo '<h2>20. Полный HTML (в &lt;pre&gt;)</h2>';
	echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
		. htmlspecialchars( $html )
		. '</pre>';
}

echo '<hr>done';
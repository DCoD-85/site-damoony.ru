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

# visuoel stack (form_form before form — PHP covariance)
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

# --- chain ---
echo '<h2>8. new visuoel()</h2>';
$root = new visuoel( );
echo 'class: <b>' . get_class( $root ) . '</b> (ожидаем visuoel)<br>';
echo 'is_root: <b>' . var_export( $root->is_root( ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>9. -&gt;form() → visuoel_form</h2>';
$grp = $root->form( );
echo 'class: <b>' . get_class( $grp ) . '</b> (ожидаем visuoel_form)<br>';
echo 'parent is $root: <b>' . var_export( $grp->parent === $root , true ) . '</b> (ожидаем true)<br>';
echo 'css_class: <b>' . $grp->css_class( ) . '</b> (ожидаем blockform)<br>';

echo '<h2>10. -&gt;form() → visuoel_form_form</h2>';
$el = $grp->form( );
echo 'class: <b>' . get_class( $el ) . '</b> (ожидаем visuoel_form_form)<br>';
echo 'parent is $grp: <b>' . var_export( $el->parent === $grp , true ) . '</b> (ожидаем true)<br>';

echo '<h2>11. form() singleton</h2>';
echo 'grp same: <b>' . var_export( $grp === $root->form( ) , true ) . '</b> (ожидаем true)<br>';
echo 'el same: <b>' . var_export( $el === $grp->form( ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>12. $el-&gt;data() → visuoel_form_form__data</h2>';
$data = $el->data( );
echo 'class: <b>' . get_class( $data ) . '</b> (ожидаем visuoel_form_form__data)<br>';
echo 'node is $el: <b>' . var_export( $data->node === $el , true ) . '</b> (ожидаем true)<br>';

echo '<h2>13. $grp-&gt;get_form_form__data() === $el-&gt;data()</h2>';
echo 'same: <b>' . var_export( $grp->get_form_form__data( ) === $data , true ) . '</b> (ожидаем true)<br>';

# --- init + vt_start / vt_end ---
echo '<h2>14. init() на корне создаёт vt_start / vt_end</h2>';
$root->init( );
echo 'root vt_start: <b>' . get_class( $root->vt_start ) . '</b> (ожидаем visuotag)<br>';
echo 'form vt_start: <b>' . get_class( $grp->vt_start ) . '</b> (ожидаем visuotag)<br>';
echo 'form vt_end: <b>' . get_class( $grp->vt_end ) . '</b> (ожидаем visuotag)<br>';
echo 'form_form vt_start: <b>' . get_class( $el->vt_start ) . '</b> (ожидаем visuotag)<br>';
echo 'form_form vt_end: <b>' . get_class( $el->vt_end ) . '</b> (ожидаем visuotag)<br>';

echo '<h2>15. form_form: vt_start !== vt_end (bugfix: slot inside form)</h2>';
echo 'not same: <b>' . var_export( $el->vt_start !== $el->vt_end , true ) . '</b> (ожидаем true)<br>';

echo '<h2>16. release() returns string &gt; 0</h2>';
$html = $root->release( );
echo 'is_string: <b>' . var_export( is_string( $html ) , true ) . '</b> (ожидаем true)<br>';
echo 'len: <b>' . strlen( $html ) . '</b> (ожидаем &gt; 0)<br>';

echo '<h2>17. HTML НЕ содержит обёртки &lt;div class="visuoel"&gt;</h2>';
echo 'not contains: <b>' . var_export( ! str_contains( $html , 'class="visuoel"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>18. HTML contains &lt;div class="blockform"&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $html , 'class="blockform"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>19. HTML contains &lt;form&gt; and &lt;div class="form_body"&gt;</h2>';
echo 'form: <b>' . var_export( str_contains( $html , '<form' ) , true ) . '</b> (ожидаем true)<br>';
echo 'form_body: <b>' . var_export( str_contains( $html , 'class="form_body"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>20. show() === release()</h2>';
echo 'same: <b>' . var_export( $root->show( ) === $html , true ) . '</b> (ожидаем true)<br>';

# --- set() ---
echo '<hr>';
echo '<h1>2.2 — data-&gt;set() + build()</h1>';

$root2 = new visuoel( );
$grp2  = $root2->form( );
$el2   = $grp2->form( );
$d2    = $el2->data( );

$d2->set( 'testform' , 'post' , '/submit' , title_text : 'Форма авторизации' , form_jquery : true );

echo '<h2>21. set() populates data fields</h2>';
echo 'form_name: <b>' . $d2->form_name . '</b> (ожидаем testform)<br>';
echo 'form_method: <b>' . $d2->form_method . '</b> (ожидаем post)<br>';
echo 'form_action: <b>' . $d2->form_action . '</b> (ожидаем /submit)<br>';
echo 'title_text: <b>' . $d2->title_text . '</b> (ожидаем Форма авторизации)<br>';
echo 'form_jquery: <b>' . var_export( $d2->form_jquery , true ) . '</b> (ожидаем true)<br>';

echo '<h2>22. set(file) → method=POST, enctype=multipart/form-data</h2>';
$d2->set( 'upload' , 'file' );
echo 'method: <b>' . $d2->form_method . '</b> (ожидаем POST)<br>';
echo 'enctype: <b>' . $d2->form_enctype . '</b> (ожидаем multipart/form-data)<br>';

# reset for next checks
$d2->set( 'testform' , 'post' , '/submit' , title_text : 'Форма авторизации' , form_jquery : true );
$el2->init( );

echo '<h2>23. HTML contains action="/submit"</h2>';
$h2 = $el2->vt_start->release( );
echo 'contains: <b>' . var_export( str_contains( $h2 , 'action="/submit"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>24. HTML contains name="testform"</h2>';
echo 'contains: <b>' . var_export( str_contains( $h2 , 'name="testform"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>25. HTML contains method="post"</h2>';
echo 'contains: <b>' . var_export( str_contains( $h2 , 'method="post"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>26. HTML contains data-form-jquery</h2>';
echo 'contains: <b>' . var_export( str_contains( $h2 , 'data-form-jquery' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>27. HTML contains &lt;div class="form_title"&gt;Форма авторизации&lt;/div&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $h2 , 'class="form_title"' ) , true ) . '</b> (ожидаем true)<br>';
echo 'title text: <b>' . var_export( str_contains( $h2 , 'Форма авторизации' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>28. HTML contains &lt;div class="form_body"&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $h2 , 'class="form_body"' ) , true ) . '</b> (ожидаем true)<br>';

# --- add child ---
# --- add child (visuotag) ---
echo '<h2>29. add(visuotag) → children go inside form_body</h2>';
$root3 = new visuoel( );
$grp3  = $root3->form( );
$el3   = $grp3->form( );
$d3    = $el3->data( );
$d3->set( 'testform' , 'post' , '/submit' );
$d3->title_text = '';

$child = new visuotag( );
$child->tag( 'p' )->content( 'hello from child' );

$d3->add( $child );

$root3->init( );
$h3 = $root3->release( );

echo 'contains "hello from child": <b>' . var_export( str_contains( $h3 , 'hello from child' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>30. child appears AFTER form_body open tag</h2>';
$pos_body  = strpos( $h3 , 'class="form_body"' );
$pos_child = strpos( $h3 , 'hello from child' );
echo 'body pos: <b>' . var_export( $pos_body , true ) . '</b><br>';
echo 'child pos: <b>' . var_export( $pos_child , true ) . '</b><br>';
echo 'child after body: <b>' . var_export( $pos_body !== false && $pos_child !== false && $pos_child > $pos_body , true ) . '</b> (ожидаем true)<br>';
# --- html preview ---
echo '<hr>';
echo '<h2>Полный HTML (первый тест, без set)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $html )
	. '</pre>';

echo '<h2>Полный HTML (второй тест, с set + child)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $h3 )
	. '</pre>';

echo '<hr>done';
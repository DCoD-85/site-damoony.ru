<?PHP
# Visuoel primer tests — isolated
# primer = multi-режим (каждый ->text()/->image() создаёт новый элемент)
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

# visuoel stack (elements before groups — PHP covariance)
require $root_dir . '/sources/visuo/sources/visuoel.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group_element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer/visuoel_primer_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer/visuoel_primer_image.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer.php';

echo '<h1>visuoel — primer/text + primer/image tests</h1>';

# --- class structure ---
echo '<h2>1. class_exists visuoel_primer</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_primer' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_primer_text</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_primer_text' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. class_exists visuoel_primer_image</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_primer_image' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. visuoel_primer extends visuoel_group</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_primer' , 'visuoel_group' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>5. visuoel_primer_text extends visuoel_group_element</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_primer_text' , 'visuoel_group_element' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>6. visuoel_primer_image extends visuoel_group_element</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_primer_image' , 'visuoel_group_element' ) , true ) . '</b> (ожидаем true)<br>';

# --- chain: root->primer->text/image ---
echo '<h2>7. new visuoel()->primer() → visuoel_primer</h2>';
$root = new visuoel( );
$grp  = $root->primer( );
echo 'class: <b>' . get_class( $grp ) . '</b> (ожидаем visuoel_primer)<br>';
echo 'css_class: <b>' . $grp->css_class( ) . '</b> (ожидаем primer)<br>';
echo 'parent is $root: <b>' . var_export( $grp->parent === $root , true ) . '</b> (ожидаем true)<br>';

echo '<h2>8. -&gt;text() → visuoel_primer_text</h2>';
$t = $grp->text( );
echo 'class: <b>' . get_class( $t ) . '</b> (ожидаем visuoel_primer_text)<br>';
echo 'css_class: <b>' . $t->css_class( ) . '</b> (ожидаем text)<br>';
echo 'parent is $grp: <b>' . var_export( $t->parent === $grp , true ) . '</b> (ожидаем true)<br>';

echo '<h2>9. -&gt;image() → visuoel_primer_image</h2>';
$i = $grp->image( );
echo 'class: <b>' . get_class( $i ) . '</b> (ожидаем visuoel_primer_image)<br>';
echo 'css_class: <b>' . $i->css_class( ) . '</b> (ожидаем image)<br>';
echo 'parent is $grp: <b>' . var_export( $i->parent === $grp , true ) . '</b> (ожидаем true)<br>';

# --- multi-режим: каждый вызов создаёт новый ---
echo '<h2>10. multi-режим: каждый вызов создаёт новый элемент</h2>';
$p_multi = new visuoel_primer( );
$t1 = $p_multi->text( );
$t2 = $p_multi->text( );
$i1 = $p_multi->image( );
$i2 = $p_multi->image( );
echo 'singleton flag: <b>' . var_export( $p_multi->singleton , true ) . '</b> (ожидаем false)<br>';
echo 't1 !== t2: <b>' . var_export( $t1 !== $t2 , true ) . '</b> (ожидаем true)<br>';
echo 'i1 !== i2: <b>' . var_export( $i1 !== $i2 , true ) . '</b> (ожидаем true)<br>';
echo 'elements count 4: <b>' . var_export( count( $p_multi->elements ) === 4 , true ) . '</b> (ожидаем true)<br>';

echo '<h2>11. data classes</h2>';
echo 'text data: <b>' . get_class( $t->data( ) ) . '</b> (ожидаем visuoel_primer_text__data)<br>';
echo 'image data: <b>' . get_class( $i->data( ) ) . '</b> (ожидаем visuoel_primer_image__data)<br>';

# --- text content ---
echo '<h2>12. text-&gt;data()-&gt;set("Hello, primer!")</h2>';
$t->data( )->set( 'Hello, primer!' );
echo 'content: <b>' . htmlspecialchars( $t->data( )->content ) . '</b> (ожидаем Hello, primer!)<br>';

# --- image content ---
echo '<h2>13. image-&gt;data()-&gt;set(src, alt, title, w, h)</h2>';
$i->data( )->set( '/img/logo.png' , 'Logo' , 'Site Logo' , 100 , 50 );
echo 'src: <b>' . htmlspecialchars( $i->data( )->src ) . '</b> (ожидаем /img/logo.png)<br>';
echo 'alt: <b>' . htmlspecialchars( $i->data( )->alt ) . '</b> (ожидаем Logo)<br>';
echo 'title: <b>' . htmlspecialchars( $i->data( )->title ) . '</b> (ожидаем Site Logo)<br>';
echo 'width: <b>' . $i->data( )->width . '</b> (ожидаем 100)<br>';
echo 'height: <b>' . $i->data( )->height . '</b> (ожидаем 50)<br>';

# --- init + vt_start / vt_end ---
echo '<h2>14. init() на корне — vt_start/vt_end</h2>';
$root->init( );
echo 'root vt_start: <b>' . get_class( $root->vt_start ) . '</b> (ожидаем visuotag)<br>';
echo 'primer vt_start: <b>' . get_class( $grp->vt_start ) . '</b> (ожидаем visuotag)<br>';
echo 'text vt_start: <b>' . get_class( $t->vt_start ) . '</b> (ожидаем visuotag)<br>';
echo 'text vt_end === vt_start: <b>' . var_export( $t->vt_start === $t->vt_end , true ) . '</b> (ожидаем true — лист)<br>';
echo 'image vt_end === vt_start: <b>' . var_export( $i->vt_start === $i->vt_end , true ) . '</b> (ожидаем true — лист)<br>';

# --- release ---
echo '<h2>15. release() → HTML</h2>';
$html = $root->release( );
echo 'is_string: <b>' . var_export( is_string( $html ) , true ) . '</b> (ожидаем true)<br>';
echo 'len: <b>' . strlen( $html ) . '</b> (ожидаем &gt; 0)<br>';

echo '<h2>16. HTML contains &lt;div class="visuoel"&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $html , 'class="visuoel"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>17. HTML contains &lt;div class="primer"&gt;</h2>';
echo 'contains: <b>' . var_export( str_contains( $html , 'class="primer"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>18. HTML contains &lt;div class="text"&gt;Hello, primer!&lt;/div&gt;</h2>';
echo 'contains div.text: <b>' . var_export( str_contains( $html , 'class="text"' ) , true ) . '</b> (ожидаем true)<br>';
echo 'contains content: <b>' . var_export( str_contains( $html , 'Hello, primer!' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>19. HTML contains &lt;div class="image"&gt; with &lt;img&gt;</h2>';
echo 'contains div.image: <b>' . var_export( str_contains( $html , 'class="image"' ) , true ) . '</b> (ожидаем true)<br>';
echo 'contains img src: <b>' . var_export( str_contains( $html , 'src="/img/logo.png"' ) , true ) . '</b> (ожидаем true)<br>';
echo 'contains alt: <b>' . var_export( str_contains( $html , 'alt="Logo"' ) , true ) . '</b> (ожидаем true)<br>';
echo 'contains width="100": <b>' . var_export( str_contains( $html , 'width="100"' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>20. show() === release()</h2>';
echo 'same: <b>' . var_export( $root->show( ) === $html , true ) . '</b> (ожидаем true)<br>';

echo '<h2>21. Standalone primer (без root-обёртки)</h2>';
$p2 = new visuoel_primer( );
$p2->text( )->data( )->set( 'Standalone text' );
$p2->init( );
$h2 = $p2->vt_start->release( );
echo 'contains div.primer: <b>' . var_export( str_contains( $h2 , 'class="primer"' ) , true ) . '</b> (ожидаем true)<br>';
echo 'contains text: <b>' . var_export( str_contains( $h2 , 'Standalone text' ) , true ) . '</b> (ожидаем true)<br>';

# --- multi text + image ---
echo '<hr>';
echo '<h1>multi text + image</h1>';

$pm = new visuoel_primer( );
$pm->text( )->data( )->set( 'Первый' );
$pm->text( )->data( )->set( 'Второй' );
$pm->text( )->data( )->set( 'Третий' );
$pm->image( )->data( )->set( '/a.png' , 'A' );
$pm->image( )->data( )->set( '/b.png' , 'B' );

$pm->init( );
$hpm = $pm->vt_start->release( );

echo '<h2>22. Все три text присутствуют</h2>';
echo 'Первый: <b>' . var_export( str_contains( $hpm , 'Первый' ) , true ) . '</b> (ожидаем true)<br>';
echo 'Второй: <b>' . var_export( str_contains( $hpm , 'Второй' ) , true ) . '</b> (ожидаем true)<br>';
echo 'Третий: <b>' . var_export( str_contains( $hpm , 'Третий' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>23. Оба image присутствуют</h2>';
echo 'a.png: <b>' . var_export( str_contains( $hpm , '/a.png' ) , true ) . '</b> (ожидаем true)<br>';
echo 'b.png: <b>' . var_export( str_contains( $hpm , '/b.png' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>24. Порядок: text → text → text → image → image</h2>';
$pp1 = strpos( $hpm , 'Первый' );
$pp2 = strpos( $hpm , 'Второй' );
$pp3 = strpos( $hpm , 'Третий' );
$pp4 = strpos( $hpm , '/a.png' );
$pp5 = strpos( $hpm , '/b.png' );
echo 'П1 &lt; П2 &lt; П3 &lt; A &lt; B: <b>'
	. var_export( $pp1 < $pp2 && $pp2 < $pp3 && $pp3 < $pp4 && $pp4 < $pp5 , true )
	. '</b> (ожидаем true)<br>';

# --- remove / clear ---
echo '<hr>';
echo '<h1>remove / clear</h1>';

echo '<h2>25. remove( конкретный элемент )</h2>';
$pr = new visuoel_primer( );
$t_rm = new visuoel_primer_text( );
$t_rm->data( )->set( 'DEL' );
$t_keep = new visuoel_primer_text( );
$t_keep->data( )->set( 'KEEP' );
$pr->add( $t_rm )->add( $t_keep );
echo 'count before: <b>' . count( $pr->elements ) . '</b> (ожидаем 2)<br>';
$pr->remove( $t_rm );
echo 'count after: <b>' . count( $pr->elements ) . '</b> (ожидаем 1)<br>';
$pr->init( );
$hr = $pr->vt_start->release( );
echo 'no DEL: <b>' . var_export( ! str_contains( $hr , 'DEL' ) , true ) . '</b> (ожидаем true)<br>';
echo 'KEEP: <b>' . var_export( str_contains( $hr , 'KEEP' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>26. remove возвращает bool</h2>';
echo 'remove(existing): <b>' . var_export( $pr->remove( $t_keep ) , true ) . '</b> (ожидаем true)<br>';
echo 'remove(missing): <b>' . var_export( $pr->remove( $t_rm ) , true ) . '</b> (ожидаем false)<br>';

echo '<h2>27. remove_class удаляет все text</h2>';
$p2r = new visuoel_primer( );
$p2r->add( new visuoel_primer_text( ) );
$p2r->add( new visuoel_primer_text( ) );
$p2r->add( new visuoel_primer_image( ) );
echo 'before: <b>' . count( $p2r->elements ) . '</b> (ожидаем 3)<br>';
$n = $p2r->remove_class( 'visuoel_primer_text' );
echo 'removed: <b>' . $n . '</b> (ожидаем 2)<br>';
echo 'after: <b>' . count( $p2r->elements ) . '</b> (ожидаем 1)<br>';

echo '<h2>28. clear() полностью очищает</h2>';
$p2r->clear( );
echo 'count: <b>' . count( $p2r->elements ) . '</b> (ожидаем 0)<br>';

echo '<h2>29. data()-&gt;remove() работает</h2>';
$p3 = new visuoel_primer( );
$tx = new visuoel_primer_text( );
$p3->add( $tx );
$p3->data( )->remove( $tx );
echo 'count: <b>' . count( $p3->elements ) . '</b> (ожидаем 0)<br>';

# --- html preview ---
echo '<hr>';
echo '<h2>Полный HTML (root-тест)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $html )
	. '</pre>';

echo '<h2>Полный HTML (multi text + image)</h2>';
echo '<pre style="background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;">'
	. htmlspecialchars( $hpm )
	. '</pre>';

echo '<hr>';
echo '<h2>Живой рендер (multi)</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $hpm . '</div>';

echo '<hr>done';
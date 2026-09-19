<?PHP
# Visuotag tests — baseline (current behavior)
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

echo '<h1>Visuotag tests (baseline)</h1>';

# 1. empty root
echo '<h2>1. Empty root</h2>';
$r = new visuotag();
echo 'tagname: <b>' . $r->tagname . '</b> (ожидаем root)<br>';
echo 'root: <b>' . ( $r->root ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'tag_closed: <b>' . ( $r->tag_closed ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';
echo 'parent null: <b>' . ( $r->visuotag === null ? 'true' : 'false' ) . '</b><br>';
echo 'elements empty: <b>' . ( empty( $r->elements ) ? 'true' : 'false' ) . '</b><br>';

# 2. root release empty
echo '<h2>2. Root release (empty)</h2>';
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b> (ожидаем пусто)<br>';

# 3. tag child
echo '<h2>3. tag child</h2>';
$r = new visuotag();
$div = $r->tag('div');
echo 'tagname: <b>' . $div->tagname . '</b> (ожидаем div)<br>';
echo 'root: <b>' . ( $div->root ? 'true' : 'false' ) . '</b> (ожидаем false)<br>';
echo 'parent is root: <b>' . ( $div->visuotag === $r ? 'true' : 'false' ) . '</b><br>';
echo 'root elements count: <b>' . count( $r->elements ) . '</b> (ожидаем 1)<br>';

# 4. content
echo '<h2>4. content</h2>';
$div->content('hello');
echo 'elements count: <b>' . count( $div->elements ) . '</b> (ожидаем 1)<br>';
echo 'elements[0]: <b>' . htmlspecialchars( (string) $div->elements[0] ) . '</b> (ожидаем hello)<br>';

# 5. release tree
echo '<h2>5. release tree</h2>';
$r = new visuotag();
$r->tag('div')->content('hello');
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b><br>';
echo 'ожидаем: &lt;div&gt;hello&lt;/div&gt;<br>';

# 6. self-closing
echo '<h2>6. self-closing tag</h2>';
$r = new visuotag();
$img = $r->tag('img', false);
$img->attrs_add('src', '/test.png');
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b><br>';

# 7. nested
echo '<h2>7. nested</h2>';
$r = new visuotag();
$r->tag('div')->tag('span')->content('x');
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b><br>';

# 8. parent navigation
echo '<h2>8. parent navigation</h2>';
$r = new visuotag();
$d = $r->tag('div');
$s = $d->tag('span');
echo 'span->parent() is div: <b>' . ( $s->parent() === $d ? 'true' : 'false' ) . '</b><br>';
echo 'div->parent() is root: <b>' . ( $d->parent() === $r ? 'true' : 'false' ) . '</b><br>';
echo 'span->root() is r: <b>' . ( $s->root() === $r ? 'true' : 'false' ) . '</b><br>';

# 9. parent with tagname
echo '<h2>9. parent with tagname</h2>';
$r = new visuotag();
$d = $r->tag('div');
$s = $d->tag('span');
$p = $s->tag('p');
echo 'p->parent("div") is div: <b>' . ( $p->parent('div') === $d ? 'true' : 'false' ) . '</b><br>';
echo 'p->div() is div: <b>' . ( $p->div() === $d ? 'true' : 'false' ) . '</b><br>';

# 10. find
echo '<h2>10. find</h2>';
$r = new visuotag();
$d = $r->tag('div');
$s = $d->tag('span');
$found = $r->find('span');
echo 'find span is s: <b>' . ( $found === $s ? 'true' : 'false' ) . '</b><br>';

# 11. append
echo '<h2>11. append</h2>';
$r1 = new visuotag();
$r1->tag('a');
$r2 = new visuotag();
$r2->tag('b')->tag('c');
$r1->append($r2);
echo 'r1 elements count: <b>' . count( $r1->elements ) . '</b> (ожидаем 2 — append сливает root-детей, а c — внутри b)<br>';
echo 'release: <b>' . htmlspecialchars( $r1->release() ) . '</b><br>';

# 12. prepend
echo '<h2>12. prepend</h2>';
$r1 = new visuotag();
$r1->tag('a');
$r2 = new visuotag();
$r2->tag('b');
$r1->prepend($r2);
$names = [];
foreach ( $r1->elements as $el ) if ( $el instanceof visuotag ) $names[] = $el->tagname;
echo 'order: <b>' . implode( ',' , $names ) . '</b> (ожидаем b,a)<br>';

# 13. clear
echo '<h2>13. clear</h2>';
$r = new visuotag();
$r->tag('a')->tag('b');
$r->clear();
echo 'elements count: <b>' . count( $r->elements ) . '</b> (ожидаем 0)<br>';

# 14. attrs_add
echo '<h2>14. attrs_add</h2>';
$r = new visuotag();
$r->tag('div')->attrs_add('id', 'main');
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b><br>';

# 15. attrs_class_add
echo '<h2>15. attrs_class_add</h2>';
$r = new visuotag();
$r->tag('div')->attrs_class_add('box');
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b><br>';

# 16. data_add
echo '<h2>16. data_add</h2>';
$r = new visuotag();
$r->tag('div')->data_add('ajax', true);
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b><br>';

# 17. id
echo '<h2>17. id</h2>';
$r = new visuotag();
$r->tag('div')->id('foo');
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b><br>';

# 18. next
echo '<h2>18. next</h2>';
$r = new visuotag();
$r->content('first');
$n = $r->next();
echo 'next type: <b>' . gettype($n) . '</b>, value: <b>' . htmlspecialchars( (string) $n ) . '</b><br>';

# 19. tag with named params
echo '<h2>19. tag with named params</h2>';
$r = new visuotag();
$r->tag('div', id: 'x', class: 'y', style: 'color:red');
echo 'release: <b>' . htmlspecialchars( $r->release() ) . '</b><br>';

# 20. final
echo '<h2>20. final</h2>';
$r = new visuotag();
$r->tag('div')->content('x');
echo 'final: <b>' . htmlspecialchars( $r->final() ) . '</b><br>';

echo '<hr>done';
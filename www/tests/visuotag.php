<?PHP
# Visuotag tests — baseline + stage 2 + stage 3 + stage 4 + stage 5
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

# --- stubs for stage-5 hooks ---
if ( ! class_exists( 'visuostruct' ) ) {
	class visuostruct { }
}
if ( ! class_exists( 'visuoelement' ) ) {
	class visuoelement {
		public string $name = '';
		public bool   $init = false;
		public null|visuotag $visuotag_start = null;
		public null|visuotag $visuotag_end   = null;
		public function __construct ( $visuorelease = null , $data = null , $visuotag = null ) {}
		public function init ( ) : void { $this->init = true; }
		public function release ( visuotag $vt ) : void {}
	}
}
if ( ! class_exists( 'visuometa' ) ) {
	class visuometa {
		public function as_tag ( ) : visuotag {
			$vt = new visuotag();
			$vt->tag( 'title' )->content( 'stub' );
			return $vt;
		}
	}
}

echo '<h1>Visuotag tests</h1>';

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
echo 'r1 elements count: <b>' . count( $r1->elements ) . '</b> (ожидаем 2)<br>';
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

# -------- stage 2 --------

# 21. find — deep
echo '<h2>21. find deep</h2>';
$r = new visuotag();
$a = $r->tag('div');
$b = $a->tag('section');
$c = $b->tag('span');
echo 'find span is c: <b>' . ( $r->find('span') === $c ? 'true' : 'false' ) . '</b><br>';

# 22. find by class
echo '<h2>22. find by class</h2>';
$r = new visuotag();
$a = $r->tag('div');
$a->attrs_class_add('box');
$b = $r->tag('div');
echo 'find class=box is a: <b>' . ( $r->find(class: 'box') === $a ? 'true' : 'false' ) . '</b><br>';

# 23. find tag + class
echo '<h2>23. find tag + class</h2>';
$r = new visuotag();
$a = $r->tag('div');
$b = $r->tag('div');
$b->attrs_class_add('x');
echo 'find div.x is b: <b>' . ( $r->find('div', class: 'x') === $b ? 'true' : 'false' ) . '</b><br>';

# 24. find — no match
echo '<h2>24. find — no match</h2>';
$r = new visuotag();
$r->tag('div');
echo 'find span: <b>' . var_export( $r->find('span') , true ) . '</b> (ожидаем null)<br>';

# 25. find — id
echo '<h2>25. find by id</h2>';
$r = new visuotag();
$a = $r->tag('div');
$a->id('first');
$b = $r->tag('div');
$b->id('second');
echo 'find id=second is b: <b>' . ( $r->find(id: 'second') === $b ? 'true' : 'false' ) . '</b><br>';

# 26. parent eq
echo '<h2>26. parent eq</h2>';
$r = new visuotag();
$d1 = $r->tag('div');
$d2 = $d1->tag('div');
$s = $d2->tag('span');
echo 'span->div(0) is d2: <b>' . ( $s->div(0) === $d2 ? 'true' : 'false' ) . '</b><br>';
echo 'span->div(1) is d1: <b>' . ( $s->div(1) === $d1 ? 'true' : 'false' ) . '</b><br>';

# 27. parent eq negative
echo '<h2>27. parent eq negative</h2>';
$r = new visuotag();
$d = $r->tag('div');
echo 'd->parent("div", -1) is d: <b>' . ( $d->parent('div', -1) === $d ? 'true' : 'false' ) . '</b><br>';

# 28. find not found in deep tree
echo '<h2>28. find missing in deep tree</h2>';
$r = new visuotag();
$r->tag('div')->tag('section')->tag('article');
echo 'find aside: <b>' . var_export( $r->find('aside') , true ) . '</b> (ожидаем null)<br>';

# 29. find — first wins
echo '<h2>29. find first match</h2>';
$r = new visuotag();
$first = $r->tag('span');
$r->tag('span');
echo 'find span is first: <b>' . ( $r->find('span') === $first ? 'true' : 'false' ) . '</b><br>';

# 30. _body / _html — null when missing
echo '<h2>30. _body when missing</h2>';
$r = new visuotag();
$r->tag('div');
echo '_body: <b>' . var_export( $r->_body() , true ) . '</b> (ожидаем null)<br>';
echo '_html: <b>' . var_export( $r->_html() , true ) . '</b> (ожидаем null)<br>';

# -------- stage 3 --------

# 31. attrs() returns visuoattrs
echo '<h2>31. attrs() type</h2>';
$r = new visuotag();
$r->tag('div');
echo 'attrs is visuoattrs: <b>' . ( $r->elements[0]->attrs( ) instanceof visuoattrs ? 'true' : 'false' ) . '</b><br>';

# 32. attrs_add chain
echo '<h2>32. attrs_add chain</h2>';
$r = new visuotag();
$v = $r->tag('div')->attrs_add('id', 'x')->attrs_add('data-x', 'y');
echo 'chain works: <b>' . ( $v instanceof visuotag ? 'true' : 'false' ) . '</b><br>';
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b><br>';

# 33. attrs_remove
echo '<h2>33. attrs_remove</h2>';
$r = new visuotag();
$r->tag('div')->attrs_add('id', 'x')->attrs_remove('id');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b> (ожидаем без id)<br>';

# 34. class add/remove
echo '<h2>34. class add/remove</h2>';
$r = new visuotag();
$r->tag('div')->attrs_class_add('a b')->attrs_class_remove('a');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b> (ожидаем class="b")<br>';

# 35. style add/remove
echo '<h2>35. style add/remove</h2>';
$r = new visuotag();
$r->tag('div')->attrs_style_add('color:red;font-size:14px')->attrs_style_remove('color');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b> (ожидаем font-size:14px)<br>';

# 36. actions add/remove
echo '<h2>36. actions add/remove</h2>';
$r = new visuotag();
$r->tag('div')->attrs_actions_add('onclick', 'a()')->attrs_actions_add('onmouseover', 'b()')->attrs_actions_remove('onclick');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b> (ожидаем onmouseover, без onclick)<br>';

# 37. data add/remove
echo '<h2>37. data add/remove</h2>';
$r = new visuotag();
$r->tag('div')->data_add('ajax', true)->data_add('role', 'admin', false)->data_remove('ajax');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b> (ожидаем только data-role)<br>';

# 38. data() returns visuoattrsdata
echo '<h2>38. data() type</h2>';
$r = new visuotag();
$d = $r->tag('div')->data( );
echo 'data is visuoattrsdata: <b>' . ( $d instanceof visuoattrsdata ? 'true' : 'false' ) . '</b><br>';

# 39. full attr combination
echo '<h2>39. full combination</h2>';
$r = new visuotag();
$r->tag('a')->id('link')->attrs_class_add('btn')->attrs_style_add('color:blue')->attrs_actions_add('onclick', 'go()')->data_add('role', 'nav', false);
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b><br>';

# -------- stage 4 --------

# 40. release — content mixed with tags
echo '<h2>40. release — mixed content</h2>';
$r = new visuotag();
$r->tag('div')->content('a')->tag('span')->content('b');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b><br>';

# 41. release — deep nesting
echo '<h2>41. release — deep nesting</h2>';
$r = new visuotag();
$r->tag('div')->tag('section')->tag('article')->tag('p')->content('deep');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b><br>';

# 42. release — attrs on every level
echo '<h2>42. release — attrs everywhere</h2>';
$r = new visuotag();
$r->tag('div')->id('a')->tag('span')->attrs_class_add('inner')->content('x');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b><br>';

# 43. final — from nested node
echo '<h2>43. final from nested</h2>';
$r = new visuotag();
$inner = $r->tag('div')->tag('span');
$inner->content('x');
echo 'final: <b>' . htmlspecialchars( $inner->final( ) ) . '</b><br>';

# 44. release — self-closing with attrs + data
echo '<h2>44. release — self-closing + data</h2>';
$r = new visuotag();
$r->tag('input', false)->attrs_add('type', 'text')->data_add('role', 'name', false);
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b><br>';

# 45. prepare — empty tree
echo '<h2>45. prepare — empty</h2>';
$r = new visuotag();
$r->prepare( );
echo 'no crash: <b>true</b><br>';

# 46. prepare — recursive
echo '<h2>46. prepare — tree</h2>';
$r = new visuotag();
$r->tag('div')->tag('span')->content('x');
$r->prepare( );
echo 'no crash: <b>true</b><br>';

# 47. releasetree — simple tree
echo '<h2>47. releasetree — simple</h2>';
$r = new visuotag();
$r->tag('div')->id('a')->content('x');
$tree = $r->releasetree( );
echo 'has debugtree_row: <b>' . ( strpos( $tree , 'debugtree_row' ) !== false ? 'true' : 'false' ) . '</b><br>';
echo 'has content marker: <b>' . ( strpos( $tree , '[ content ]' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 48. releasetree — includes classname
echo '<h2>48. releasetree — classname</h2>';
$r = new visuotag();
$r->tag('div');
$tree = $r->releasetree( );
echo 'has classname visuotag: <b>' . ( strpos( $tree , 'classname: visuotag' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 49. tree() no crash
echo '<h2>49. tree()</h2>';
$r = new visuotag();
$r->tag('div');
$r->tree( );
echo 'no crash: <b>true</b><br>';

# 50. release — empty content
echo '<h2>50. release — empty content</h2>';
$r = new visuotag();
$r->tag('div')->content('');
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b> (ожидаем &lt;div&gt;&lt;/div&gt;)<br>';

# -------- stage 5 --------

# 51. structv — tagname + class, no id (bugfix)
echo '<h2>51. structv — clean tag</h2>';
$r = new visuotag();
$st = $r->structv( 'main' );
echo 'tagname: <b>' . $st->tagname . '</b> (ожидаем struct)<br>';
echo 'has class cl_struct: <b>' . ( $st->attrs( )->class( )->has( 'cl_struct' ) ? 'true' : 'false' ) . '</b><br>';
echo 'has id attr: <b>' . ( $st->attrs( )->has( 'id' ) ? 'true' : 'false' ) . '</b> (ожидаем false — bugfix)<br>';
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b><br>';

# 52. structv — returns new node
echo '<h2>52. structv — returns newtag</h2>';
$r = new visuotag();
$st = $r->structv( 'main' );
echo 'returned is child: <b>' . ( $st === $r->elements[0] ? 'true' : 'false' ) . '</b><br>';

# 53. element — creates cl_element tag
echo '<h2>53. element — cl_element</h2>';
$r = new visuotag();
$r->element( 'test' );
echo 'elements count: <b>' . count( $r->elements ) . '</b> (ожидаем 1)<br>';
echo 'child tagname: <b>' . $r->elements[0]->tagname . '</b> (ожидаем element)<br>';
echo 'has class cl_element: <b>' . ( $r->elements[0]->attrs( )->class( )->has( 'cl_element' ) ? 'true' : 'false' ) . '</b><br>';

# 54. element — returns $this (chain)
echo '<h2>54. element — chain</h2>';
$r = new visuotag();
$ret = $r->element( 'test' );
echo 'returned is $this: <b>' . ( $ret === $r ? 'true' : 'false' ) . '</b> (ожидаем true)<br>';

# 55. meta — appends as child
echo '<h2>55. meta — append</h2>';
$r = new visuotag();
$m = new visuometa();
$r->meta( $m );
echo 'elements count: <b>' . count( $r->elements ) . '</b> (ожидаем 1)<br>';
echo 'release: <b>' . htmlspecialchars( $r->release( ) ) . '</b><br>';

# 56. findstruct — finds named struct
echo '<h2>56. findstruct — named</h2>';
$r = new visuotag();
$st1 = $r->structv( 'alpha' );
$st2 = $r->structv( 'beta' );
$found = [ ];
$vt_ref = null;
$r->findstruct( 'beta' , $found , $vt_ref );
echo 'found count: <b>' . count( $found ) . '</b> (ожидаем 1)<br>';
echo 'found is st2: <b>' . ( $found[0] === $st2 ? 'true' : 'false' ) . '</b><br>';
echo 'vt_ref is st2: <b>' . ( $vt_ref === $st2 ? 'true' : 'false' ) . '</b><br>';

# 57. findstruct — deep
echo '<h2>57. findstruct — deep</h2>';
$r = new visuotag();
$outer = $r->tag( 'div' );
$st = $outer->structv( 'deep' );
$found = [ ];
$vt_ref = null;
$r->findstruct( 'deep' , $found , $vt_ref );
echo 'found count: <b>' . count( $found ) . '</b> (ожидаем 1)<br>';
echo 'found is st: <b>' . ( $found[0] === $st ? 'true' : 'false' ) . '</b><br>';

# 58. findstruct — no match
echo '<h2>58. findstruct — no match</h2>';
$r = new visuotag();
$r->structv( 'alpha' );
$found = [ ];
$vt_ref = null;
$r->findstruct( 'gamma' , $found , $vt_ref );
echo 'found count: <b>' . count( $found ) . '</b> (ожидаем 0)<br>';
echo 'vt_ref null: <b>' . ( $vt_ref === null ? 'true' : 'false' ) . '</b><br>';

echo '<hr>done';
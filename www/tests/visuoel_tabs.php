<?PHP
# Visuoel tabs tests — isolated
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
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer/visuoel_primer_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer/visuoel_primer_image.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_tabs/visuoel_tabs_page.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_tabs.php';

?><!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>visuoel tabs</title>
<style>
	.tabs { border: 1px solid #ccc; padding: 15px; border-radius: 6px; margin: 10px 0; background: #fff; }
	.hhead { display: flex; gap: 8px; border-bottom: 1px solid #ddd; padding-bottom: 8px; margin-bottom: 12px; }
	.tab_title { padding: 6px 14px; cursor: pointer; border-radius: 4px; background: #f0f0f0; font-size: 14px; user-select: none; }
	.tab_title:hover { background: #e4e4e4; }
	.tab_title.selected { background: #2a5c9a; color: #fff; }
	.tab_page { display: none; padding: 5px; }
	.tab_page.selected { display: block; }
	.tab_page .primer { margin-bottom: 12px; padding: 10px; background: #fafafa; border-radius: 6px; }
	.tab_page .text { margin-bottom: 4px; }
	.tab_page .image img { max-width: 200px; border-radius: 4px; }
	.tab_page table { border-collapse: collapse; width: 100%; }
	.tab_page table th, .tab_page table td { border: 1px solid #ddd; padding: 6px 10px; text-align: left; }
	.tab_page table th { background: #f4f4f4; }
	.btn_link { display: inline-block; padding: 6px 14px; background: #2a5c9a; color: #fff; text-decoration: none; border-radius: 4px; font-size: 14px; }
	pre.code { background:#f4f4f4;padding:10px;border:1px solid #ccc;overflow:auto;font-size:12px; }
</style></head><body>

<?php
echo '<h1>visuoel — tabs tests</h1>';

echo '<h2>1. class_exists visuoel_tabs</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_tabs' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_tabs_page</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_tabs_page' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. visuoel_tabs extends visuoel_group</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_tabs' , 'visuoel_group' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. visuoel_tabs_page extends visuoel_group_element</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_tabs_page' , 'visuoel_group_element' ) , true ) . '</b> (ожидаем true)<br>';

# ============================================================
# СБОРКА
# ============================================================
echo '<hr>';
echo '<h2>5. Сборка: root-&gt;tabs()-&gt;page() x3</h2>';

$root  = new visuoel( );
$tabs  = $root->tabs( );

# ── Таб 1: два primer + кнопка-ссылка ──
$page1 = $tabs->page( );
$page1->data( )->set( 'Обзор' );

$p1a = new visuoel_primer( );
$p1a->text( )->data( )->set( 'Обзор проекта' );
$p1a->text( )->data( )->set( 'Первый таб содержит текстовые блоки и ссылку.' );
$page1->add( $p1a );

$p1b = new visuoel_primer( );
$p1b->text( )->data( )->set( 'Второй блок' );
$p1b->text( )->data( )->set( 'Ещё один primer ниже.' );
$page1->add( $p1b );

$btn_vt = new visuotag( );
$btn_vt = $btn_vt->tag( 'div' , class : 'action_row' );
$btn_vt->tag( 'a' , class : 'btn_link' )->attrs_add( 'href' , '/login' )->content( 'Перейти к входу' )->parent( );
$page1->add( $btn_vt );

# ── Таб 2: картинка + текст ──
$page2 = $tabs->page( );
$page2->data( )->set( 'Медиа' );

$p2 = new visuoel_primer( );
$p2->image( )->data( )->set( '/img/logo.png' , 'Логотип' , 'Damoony' );
$p2->text( )->data( )->set( 'Картинка и описание во втором табе.' );
$p2->text( )->data( )->set( 'Кликни по первому табу — вернёшься.' );
$page2->add( $p2 );

# ── Таб 3: таблица (собрана visuotag вручную) ──
$page3 = $tabs->page( );
$page3->data( )->set( 'Таблица' );

$table_vt = new visuotag( );
$table_vt = $table_vt->tag( 'table' );

$thead = $table_vt->tag( 'thead' );
$tr = $thead->tag( 'tr' );
$tr->tag( 'th' )->content( 'Модуль' )->parent( );
$tr->tag( 'th' )->content( 'Статус' )->parent( );
$tr->parent( )->parent( );

$tbody = $table_vt->tag( 'tbody' );
foreach ( [
	[ 'visuoel' , 'готов' ] ,
	[ 'visuotag' , 'готов' ] ,
	[ 'visuostruct' , 'готов' ] ,
	[ 'visuolink' , 'готов' ] ,
	[ 'input/text' , 'в работе' ] ,
] as $row ) {
	$tr = $tbody->tag( 'tr' );
	$tr->tag( 'td' )->content( $row[ 0 ] )->parent( );
	$tr->tag( 'td' )->content( $row[ 1 ] )->parent( );
	$tr->parent( );
}
$page3->add( $table_vt );

echo 'tabs class: <b>' . get_class( $tabs ) . '</b> (ожидаем visuoel_tabs)<br>';
echo 'pages count: <b>' . count( $tabs->elements ) . '</b> (ожидаем 3)<br>';
echo 'page1 elements: <b>' . count( $page1->elements ) . '</b> (ожидаем 3)<br>';
echo 'page2 elements: <b>' . count( $page2->elements ) . '</b> (ожидаем 1)<br>';
echo 'page3 elements: <b>' . count( $page3->elements ) . '</b> (ожидаем 1)<br>';

# ============================================================
# INIT + RELEASE
# ============================================================
echo '<h2>6. init() + release()</h2>';
$root->init( );
$html = $root->release( );

echo 'is_string: <b>' . var_export( is_string( $html ) , true ) . '</b><br>';
echo 'len: <b>' . strlen( $html ) . '</b> (ожидаем &gt; 0)<br>';

echo '<h2>7. HTML содержит .tabs, .hhead, .bbody</h2>';
foreach ( [ 'class="tabs"' , 'class="hhead"' , 'class="bbody"' ] as $needle ) {
	echo $needle . ': <b>' . var_export( str_contains( $html , $needle ) , true ) . '</b><br>';
}

echo '<h2>8. Три .tab_title и три .tab_page</h2>';
echo 'tab_title: <b>' . substr_count( $html , 'class="tab_title' ) . '</b> (ожидаем 3)<br>';
echo 'tab_page: <b>' . substr_count( $html , 'class="tab_page' ) . '</b> (ожидаем 3)<br>';

echo '<h2>9. Заголовки табов</h2>';
foreach ( [ 'Обзор' , 'Медиа' , 'Таблица' ] as $t ) {
	echo $t . ': <b>' . var_export( str_contains( $html , $t ) , true ) . '</b><br>';
}

echo '<h2>10. Первый .tab_title.selected — только один</h2>';
echo 'count "tab_title selected": <b>' . substr_count( $html , 'tab_title selected' ) . '</b> (ожидаем 1)<br>';

echo '<h2>11. data-tab-target без JSON-кавычек (bugfix)</h2>';
echo 'no &amp;quot;: <b>' . var_export( ! str_contains( $html , 'data-tab-target="&quot;' ) , true ) . '</b> (ожидаем true)<br>';
echo 'has clean id: <b>' . var_export( preg_match( '/data-tab-target="tab_\d+"/' , $html ) === 1 , true ) . '</b> (ожидаем true)<br>';

echo '<h2>12. Содержимое page1 — два primer + ссылка</h2>';
echo 'primer count: <b>' . substr_count( $html , 'class="primer"' ) . '</b> (ожидаем 2)<br>';
echo 'Обзор проекта: <b>' . var_export( str_contains( $html , 'Обзор проекта' ) , true ) . '</b><br>';
echo 'Второй блок: <b>' . var_export( str_contains( $html , 'Второй блок' ) , true ) . '</b><br>';
echo 'btn_link: <b>' . var_export( str_contains( $html , 'class="btn_link"' ) , true ) . '</b><br>';
echo 'href /login: <b>' . var_export( str_contains( $html , 'href="/login"' ) , true ) . '</b><br>';

echo '<h2>13. Содержимое page2 — картинка + текст</h2>';
echo 'src: <b>' . var_export( str_contains( $html , 'src="/img/logo.png"' ) , true ) . '</b><br>';
echo 'alt: <b>' . var_export( str_contains( $html , 'alt="Логотип"' ) , true ) . '</b><br>';
echo 'Картинка и описание: <b>' . var_export( str_contains( $html , 'Картинка и описание' ) , true ) . '</b><br>';

echo '<h2>14. Содержимое page3 — таблица</h2>';
echo '&lt;table&gt;: <b>' . var_export( str_contains( $html , '<table' ) , true ) . '</b><br>';
echo '&lt;thead&gt;: <b>' . var_export( str_contains( $html , '<thead' ) , true ) . '</b><br>';
echo '&lt;tbody&gt;: <b>' . var_export( str_contains( $html , '<tbody' ) , true ) . '</b><br>';
echo 'Модуль: <b>' . var_export( str_contains( $html , 'Модуль' ) , true ) . '</b><br>';
echo 'visuostruct: <b>' . var_export( str_contains( $html , 'visuostruct' ) , true ) . '</b><br>';
echo 'в работе: <b>' . var_export( str_contains( $html , 'в работе' ) , true ) . '</b><br>';

echo '<h2>15. Полный HTML</h2>';
echo '<pre class="code">' . htmlspecialchars( $html ) . '</pre>';

echo '<h2>16. Живой рендер</h2>';
echo '<div style="border:1px dashed #888;padding:10px;">' . $html . '</div>';

echo '<hr>done';
?>

<script>
function visuotabs_show(el) {
	var tabs = el.closest('.tabs');
	tabs.querySelectorAll('.tab_title').forEach(function(t){ t.classList.remove('selected'); });
	tabs.querySelectorAll('.tab_page').forEach(function(p){ p.classList.remove('selected'); });
	el.classList.add('selected');
	var target = document.getElementById(el.dataset.tabTarget);
	if (target) target.classList.add('selected');
}
</script>

</body></html>
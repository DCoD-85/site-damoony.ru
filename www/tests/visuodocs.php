<?PHP
# Visuodocs — вся страница построена через visuoel
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
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer/visuoel_primer_text.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer/visuoel_primer_image.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_primer.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_tabs/visuoel_tabs_page.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_tabs.php';

# ── хелперы ─────────────────────────────────────────────────
function vt_code ( string $php ) : visuotag {
	$vt = new visuotag( );
	$vt->tag( 'pre' , class : 'code' )->content( htmlspecialchars( $php ) )->parent( );
	return $vt;
}

function vt_pre ( string $text , string $class = 'tree' ) : visuotag {
	$vt = new visuotag( );
	$vt->tag( 'pre' , class : $class )->content( htmlspecialchars( $text ) )->parent( );
	return $vt;
}

function vt_table ( array $rows , array $head = [] ) : visuotag {
	$vt = new visuotag( );
	$table = $vt->tag( 'table' , class : 'api' );
	if ( ! empty( $head ) ) {
		$tr = $table->tag( 'tr' );
		foreach ( $head as $h ) $tr->tag( 'th' )->content( $h )->parent( );
		$tr->parent( );
	}
	foreach ( $rows as $row ) {
		$tr = $table->tag( 'tr' );
		foreach ( $row as $cell ) $tr->tag( 'td' )->content( $cell )->parent( );
		$tr->parent( );
	}
	return $vt;
}

function vt_section ( string $id , visuoel $ve_inner ) : visuotag {
	$vt = new visuotag( );
	$vt->tag( 'section' , class : 'doc_section' , id : $id )->el( $ve_inner );
	return $vt;
}

# ════════════════════════════════════════════════════════════
#  КОРЕНЬ
# ════════════════════════════════════════════════════════════
$ve = new visuoel( );

# ── HEADER ─────────────────────────────────────────────────
$hdr = new visuotag( );
$hdr->tag( 'header' , class : 'page_header' )
	->tag( 'h1' )->content( 'Visuo' )->parent( )
	->tag( 'span' , class : 'meta' )->content( 'документация · текущий срез' )->parent( );
$ve->add( $hdr );

# ── NAV ────────────────────────────────────────────────────
$nav_root = new visuotag( );
$nav = $nav_root->tag( 'nav' , class : 'nav_fixed' , id : 'nav_fixed' );

$nav->tag( 'button' , class : 'nav_toggle' )
	->attrs_actions_add( 'onclick' , "document.getElementById('nav_fixed').classList.toggle('collapsed')" )
	->content( '≡' )
	->parent( );

$nav->tag( 'div' , class : 'nav_title' )->content( 'Разделы' )->parent( );

$nav_ul = $nav->tag( 'ul' );

foreach ( [
	'sec-architecture' => 'Архитектура' ,
	'sec-api'          => 'Базовое API' ,
	'sec-primer'       => 'primer' ,
	'sec-form'         => 'form' ,
	'sec-tabs'         => 'tabs' ,
	'sec-visuotag'     => 'visuotag' ,
	'sec-families'     => 'Карта семейств' ,
	'sec-rules'        => 'Правила' ,
] as $id => $label ) {
	$a = $nav_ul->tag( 'li' )->tag( 'a' );
	$a->attrs_add( 'href' , '#' . $id )->content( $label );
	$a->parent( )->parent( );
}
$nav_ul->parent( );

$ve->add( $nav_root );

# ════════════════════════════════════════════════════════════
#  СЕКЦИЯ 1 — АРХИТЕКТУРА
# ════════════════════════════════════════════════════════════
$s = new visuoel_primer( );
$s->text( )->data( )->set( 'Архитектура' );
$s->text( )->data( )->set( 'Три уровня: корень → группа → элемент. Каждый уровень — отдельный класс со своей обёрткой и данными.' );
$s->add( vt_pre(
"visuoel                    <div class=\"visuoel\">
├── visuoel_group          <div class=\"group\">
│   ├── visuoel_form       <div class=\"blockform\">       singleton
│   ├── visuoel_primer     <div class=\"primer\">          multi
│   └── visuoel_tabs       <div class=\"tabs\">            сборка
└── visuoel_group_element  <div class=\"element\">
    ├── visuoel_form_form  <form>…<div.form_body>
    ├── visuoel_primer_text|image
    └── visuoel_tabs_page"
) );

$s->text( )->data( )->set( 'vt_start / vt_end' );
$s->text( )->data( )->set( 'Каждый visuoel при инициализации строит два якоря: vt_start — верх поддерева (attach point для родителя); vt_end — слот, куда идут дети. У листьев vt_start === vt_end, у контейнеров vt_end вложен внутрь (например, div.form_body).' );

$s->text( )->data( )->set( 'Порядок инициализации' );
$s->add( vt_pre( <<<'TXT'
1. init() родителя → build() строит свой скелет
2. Обход детей: $child->init() рекурсивно
3. $this->vt_end->append( $child->vt_start )
TXT
) );
$ve->add( vt_section( 'sec-architecture' , $s ) );

# ════════════════════════════════════════════════════════════
#  СЕКЦИЯ 2 — БАЗОВОЕ API
# ════════════════════════════════════════════════════════════
$s = new visuoel_primer( );
$s->text( )->data( )->set( 'Базовое API' );
$s->add( vt_table( [
	[ 'new visuoel()'        , 'Корень дерева' ] ,
	[ '->add( $el )'         , 'Добавить ребёнка (visuoel | visuotag)' ] ,
	[ '->remove( $el )'      , 'Удалить по идентичности, возвращает bool' ] ,
	[ '->remove_class( $c )' , 'Удалить все элементы класса, возвращает int' ] ,
	[ '->clear()'            , 'Удалить всех детей' ] ,
	[ '->init()'             , 'Построить дерево visuotag (идемпотентно)' ] ,
	[ '->release()'          , 'Вернуть HTML (сам вызывает init)' ] ,
	[ '->data()'             , 'Данные элемента (set, get, add)' ] ,
	[ '$singleton'           , 'true — повторный вызов фабрики вернёт тот же элемент' ] ,
] , [ 'Метод' , 'Что делает' ] ) );

$s->text( )->data( )->set( 'Живой пример' );
$live = new visuotag( );
$live->tag( 'div' , class : 'live' )->tag( 'div' , class : 'live_label' )->content( 'Live render' )->parent( )->parent( );
$live_inner = new visuoel( );
$lp = $live_inner->primer( );
$lp->text( )->data( )->set( 'Привет, visuoel' );
$lp->text( )->data( )->set( 'Это primer внутри visuoel.' );
$live->el( $live_inner );
$s->add( $live );

$s->add( vt_code( <<<'CODE'
$ve = new visuoel();
$p = $ve->primer();
$p->text()->data()->set( 'Привет, visuoel' );
$p->text()->data()->set( 'Это primer внутри visuoel.' );
echo $ve->release();
CODE
) );
$ve->add( vt_section( 'sec-api' , $s ) );

# ════════════════════════════════════════════════════════════
#  СЕКЦИЯ 3 — primer
# ════════════════════════════════════════════════════════════
$s = new visuoel_primer( );
$s->text( )->data( )->set( 'primer' );
$s->text( )->data( )->set( 'Multi-группа. Каждый вызов ->text() / ->image() создаёт новый элемент.' );

$s->text( )->data( )->set( 'Свойства элемента' );
$s->add( vt_table( [
	[ 'text'  , "set( string \$content )" ] ,
	[ 'image' , "set( string \$src , string \$alt = '' , string \$title = '' , int \$width = 0 , int \$height = 0 )" ] ,
] ) );

$s->text( )->data( )->set( 'Живой пример' );
$live = new visuotag( );
$live->tag( 'div' , class : 'live' )->tag( 'div' , class : 'live_label' )->content( 'Live render' )->parent( )->parent( );
$live_inner = new visuoel_primer( );
$live_inner->text( )->data( )->set( 'Заголовок' );
$live_inner->text( )->data( )->set( 'Первый абзац' );
$live_inner->image( )->data( )->set( '/img/logo.png' , 'Лого' , 'Damoony' );
$live_inner->text( )->data( )->set( 'Ещё один абзац' );
$live->el( $live_inner );
$s->add( $live );

$s->add( vt_code( <<<'CODE'
$p = $ve->primer();
$p->text()->data()->set( 'Заголовок' );
$p->text()->data()->set( 'Первый абзац' );
$p->image()->data()->set( '/img/logo.png' , 'Лого' , 'Damoony' );
$p->text()->data()->set( 'Ещё один абзац' );
CODE
) );
$ve->add( vt_section( 'sec-primer' , $s ) );

# ════════════════════════════════════════════════════════════
#  СЕКЦИЯ 4 — form
# ════════════════════════════════════════════════════════════
$s = new visuoel_primer( );
$s->text( )->data( )->set( 'form' );
$s->text( )->data( )->set( 'Singleton-группа. Одна форма на корень.' );

$s->text( )->data( )->set( 'form_form' );
$s->add( vt_table( [
	[ 'set()' , "set( \$name , \$method = 'POST' , \$action = '' , \$enctype = '…' , \$jquery = false , \$title = '' , \$class = '' )" ] ,
	[ 'add()' , 'add( visuoel | visuotag $child ) — кладётся в form_body' ] ,
] ) );

$s->text( )->data( )->set( 'Живой пример' );
$live = new visuotag( );
$live->tag( 'div' , class : 'live' )->tag( 'div' , class : 'live_label' )->content( 'Live render' )->parent( )->parent( );
$live_inner = new visuoel( );
$ff = $live_inner->form( )->form( );
$ff->data( )->set( 'testform' , 'post' , '/submit' , title_text : 'Форма' , form_jquery : true );
$live->el( $live_inner );
$s->add( $live );

$s->add( vt_code( <<<'CODE'
$ve = new visuoel();
$form = $ve->form()->form();
$form->data()->set( 'testform' , 'post' , '/submit'
    , title_text : 'Форма'
    , form_jquery : true
);
CODE
) );
$ve->add( vt_section( 'sec-form' , $s ) );

# ════════════════════════════════════════════════════════════
#  СЕКЦИЯ 5 — tabs
# ════════════════════════════════════════════════════════════
$s = new visuoel_primer( );
$s->text( )->data( )->set( 'tabs' );
$s->text( )->data( )->set( 'Сборка: разделение на head и body. Внутрь каждой вкладки можно положить любой visuoel или visuotag.' );

$s->text( )->data( )->set( 'API' );
$s->add( vt_table( [
	[ '->tabs()'            , 'Фабрика группы tabs' ] ,
	[ '->page()'            , 'Создать вкладку, возвращает visuoel_tabs_page' ] ,
	[ 'page->data()->set()' , 'set( string $title ) — заголовок вкладки' ] ,
	[ 'page->add()'         , 'Любой visuoel или visuotag — содержимое вкладки' ] ,
] ) );

$s->text( )->data( )->set( 'Живой пример' );
$live = new visuotag( );
$live->tag( 'div' , class : 'live' )->tag( 'div' , class : 'live_label' )->content( 'Live render' )->parent( )->parent( );
$live_inner = new visuoel( );
$tb = $live_inner->tabs( );
$pg1 = $tb->page( ); $pg1->data( )->set( 'Первая' );
$pp = new visuoel_primer( );
$pp->text( )->data( )->set( 'Содержимое первой вкладки' );
$pg1->add( $pp );
$pg2 = $tb->page( ); $pg2->data( )->set( 'Вторая' );
$pp2 = new visuoel_primer( );
$pp2->text( )->data( )->set( 'Содержимое второй вкладки' );
$pp2->image( )->data( )->set( '/img/logo.png' , 'Лого' );
$pg2->add( $pp2 );
$live->el( $live_inner );
$s->add( $live );

$s->add( vt_code( <<<'CODE'
$tabs = $ve->tabs();

$p1 = $tabs->page();
$p1->data()->set( 'Первая' );
$p1->add( $inner_primer );

$p2 = $tabs->page();
$p2->data()->set( 'Вторая' );
$p2->add( $another_visuoel );
CODE
) );
$ve->add( vt_section( 'sec-tabs' , $s ) );

# ════════════════════════════════════════════════════════════
#  СЕКЦИЯ 6 — интеграция с visuotag
# ════════════════════════════════════════════════════════════
$s = new visuoel_primer( );
$s->text( )->data( )->set( 'Интеграция с visuotag' );
$s->text( )->data( )->set( 'Любой visuoel можно вложить в существующее visuotag-дерево через ->el().' );
$s->add( vt_code( <<<'CODE'
$vt = new visuotag();
$vt->tag( 'div' , class : 'welcome' )->el( $ve );
$vt->prepare();   # ← разворачивает visuoel-плейсхолдеры
echo $vt->release();
CODE
) );
$s->text( )->data( )->set( 'Важно: перед release() должен быть вызван prepare() — иначе visuoel-плейсхолдеры останутся пустыми.' );
$ve->add( vt_section( 'sec-visuotag' , $s ) );

# ════════════════════════════════════════════════════════════
#  СЕКЦИЯ 7 — карта семейств
# ════════════════════════════════════════════════════════════
$s = new visuoel_primer( );
$s->text( )->data( )->set( 'Карта семейств' );
$s->add( vt_table( [
	[ 'primer'  , 'text, image' , 'готово' ] ,
	[ 'form'    , 'form_form' , 'базово' ] ,
	[ 'tabs'    , 'tabs_page' , 'готово' ] ,
	[ 'input'   , 'text, hidden, password, submit, checkbox, radio, select, textarea, file' , 'план' ] ,
	[ 'button'  , 'submit, reset, button, link, icon' , 'план' ] ,
	[ 'field'   , 'text, password, select, checkbox, textarea, file (label + input + hint)' , 'обсуждение' ] ,
	[ 'link'    , 'simple, load, popup, external, download' , 'план' ] ,
	[ 'block'   , 'text, image, html, quote, code, spacer' , 'план' ] ,
	[ 'table'   , 'table, row, cell, head, tablebox, block, blocktext' , 'план' ] ,
	[ 'list'    , 'ul, ol, dl, breadcrumbs, pagination, menu' , 'план' ] ,
	[ 'message' , 'info, success, warning, error' , 'план' ] ,
	[ 'layout'  , 'row, col, container, section' , 'план' ] ,
] , [ 'Семейство' , 'Элементы' , 'Статус' ] ) );
$ve->add( vt_section( 'sec-families' , $s ) );

# ════════════════════════════════════════════════════════════
#  СЕКЦИЯ 8 — правила
# ════════════════════════════════════════════════════════════
$s = new visuoel_primer( );
$s->text( )->data( )->set( 'Правила' );
$s->text( )->data( )->set( '1. Родитель раньше детей. init() сначала строит скелет родителя, потом инициализирует детей.' );
$s->text( )->data( )->set( '2. Дети идут в vt_end. Хочешь особое место для детей — сделай vt_end этим местом.' );
$s->text( )->data( )->set( '3. Листья самозакрывающиеся. input, image, text: vt_start === vt_end.' );
$s->text( )->data( )->set( '4. Один файл — одна группа + её элемент.' );
$s->text( )->data( )->set( '5. Порядок include: сначала элементы, потом группа (PHP ковариантность возвращаемых типов).' );
$s->text( )->data( )->set( '6. Данные отделены от рендера. $__data — поля, build() — как они превращаются в HTML.' );
$s->text( )->data( )->set( '7. visuoelement не тронут. Старая схема работает параллельно.' );
$ve->add( vt_section( 'sec-rules' , $s ) );

# ════════════════════════════════════════════════════════════
#  RELEASE
# ════════════════════════════════════════════════════════════
$ve->init( );
$body_html = $ve->release( );

?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Visuo — документация</title>
<style>
	* { box-sizing: border-box; }
	html { scroll-behavior: smooth; }
	body { font-family: system-ui, -apple-system, "Segoe UI", sans-serif; background:#eef1f5; color:#222; margin:0; padding:0; line-height:1.55; }

	.visuoel { max-width: 980px; margin: 0 auto; padding: 0 24px 80px; }

	.page_header { background:#1e2a3a; color:#fff; padding: 22px 32px; margin: 0 -24px 28px; display:flex; align-items: baseline; justify-content: space-between; }
	.page_header h1 { margin:0; font-size: 22px; font-weight: 600; letter-spacing: 0.5px; }
	.page_header .meta { font-size: 13px; color: #a6b1c2; }

	.doc_section { margin-bottom: 24px; }
	.doc_section .primer { background:#fff; border-radius:10px; padding: 22px 26px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
	.doc_section .primer > .text:first-child { font-size: 20px; font-weight: 700; color:#111; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #d6dae1; }
	.doc_section .primer .text { color:#333; margin-bottom: 6px; }

	.primer .primer { background:#f7f9fc; padding: 14px 16px; margin: 8px 0; box-shadow:none; border:1px solid #e0e3e8; }
	.primer .primer > .text:first-child { font-size:15px; font-weight: 600; padding-bottom:0; border:none; margin-bottom:6px; }

	pre.code { background:#1e2a3a; color:#e8ecf1; padding:14px 16px; border-radius:6px; overflow:auto; font-size:12.5px; line-height:1.5; font-family: Consolas, Monaco, monospace; margin: 10px 0; }
	pre.tree { background:#f7f9fc; color:#333; border:1px solid #e0e3e8; padding:12px 16px; border-radius:6px; font-size:12.5px; overflow-x:auto; margin: 10px 0; font-family: Consolas, Monaco, monospace; white-space: pre; }

	table.api { border-collapse: collapse; width: 100%; font-size: 13px; margin: 10px 0; }
	table.api th, table.api td { border: 1px solid #e0e3e8; padding: 7px 10px; text-align: left; vertical-align: top; }
	table.api th { background: #f4f6f9; font-weight: 600; }
	table.api td:first-child { font-family: Consolas, monospace; white-space: nowrap; color:#2a5c9a; }

	.live { background:#f7f9fc; border:1px dashed #c0c8d4; border-radius:6px; padding:14px 16px; margin: 10px 0; }
	.live_label { font-size: 10px; text-transform: uppercase; color:#888; letter-spacing:1px; margin-bottom: 10px; }

	.primer .text { color:#444; }
	.primer .image img { max-width:200px; border-radius:6px; }

	.tabs { background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; margin: 8px 0; }
	.hhead { display:flex; flex-wrap:wrap; gap:2px; padding:8px 8px 0; background:#f7f8fa; border-bottom:1px solid #e0e3e8; }
	.tab_title { padding:8px 14px; cursor:pointer; border-radius:6px 6px 0 0; font-size:13px; color:#4a5568; user-select:none; border:1px solid transparent; border-bottom:none; position:relative; top:1px; }
	.tab_title:hover { background:#eef1f5; }
	.tab_title.selected { background:#fff; color:#1e2a3a; font-weight:600; border-color:#e0e3e8; }
	.tab_page { display:none; padding:14px; }
	.tab_page.selected { display:block; }

	.blockform { margin: 8px 0; }
	.blockform form { background:#fff; border:1px solid #e0e3e8; border-radius:6px; padding: 14px 16px; }
	.form_title { font-weight: 600; margin-bottom: 8px; }

	.nav_fixed { position: fixed; right: 20px; bottom: 20px; background: #1e2a3a; color: #e8ecf1; border-radius: 10px; box-shadow: 0 6px 20px rgba(0,0,0,0.25); padding: 12px 14px; max-width: 220px; font-size: 12.5px; z-index: 100; }
	.nav_fixed .nav_title { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #8fa1b8; margin-bottom: 6px; }
	.nav_fixed ul { list-style: none; margin: 0; padding: 0; }
	.nav_fixed a { display: block; color: #cbd5e0; text-decoration: none; padding: 4px 8px; border-radius: 5px; transition: background 0.15s, color 0.15s; }
	.nav_fixed a:hover { background: #2a3a52; color: #fff; }
	.nav_fixed a.active { background: #2a5c9a; color: #fff; }
	.nav_toggle { position: absolute; top: 8px; right: 8px; background: none; border: none; color: #8fa1b8; cursor: pointer; font-size: 14px; padding: 0 4px; }
	.nav_fixed.collapsed ul { display: none; }
	.nav_fixed.collapsed { max-width: 120px; padding: 8px 12px; }
</style>
</head>
<body>

<?php echo $body_html; ?>

<script>
function visuotabs_show( el ) {
	var tabs = el.closest( '.tabs' );
	if ( ! tabs ) return;
	var head = tabs.querySelector( ':scope > .hhead' );
	var body = tabs.querySelector( ':scope > .bbody' );
	if ( head ) head.querySelectorAll( ':scope > .tab_title' ).forEach( function ( t ) { t.classList.remove( 'selected' ); } );
	if ( body ) body.querySelectorAll( ':scope > .tab_page' ).forEach( function ( p ) { p.classList.remove( 'selected' ); } );
	el.classList.add( 'selected' );
	var target = document.getElementById( el.dataset.tabTarget );
	if ( target ) target.classList.add( 'selected' );
}

( function ( ) {
	var links = document.querySelectorAll( '.nav_fixed a[href^="#"]' );
	if ( ! links.length ) return;
	var sections = [];
	links.forEach( function ( a ) {
		var id = a.getAttribute( 'href' ).slice( 1 );
		var el = document.getElementById( id );
		if ( el ) sections.push( { el: el , a: a } );
	} );
	function update( ) {
		var y = window.scrollY + 140;
		var current = null;
		sections.forEach( function ( s ) {
			if ( s.el.offsetTop <= y ) current = s;
		} );
		links.forEach( function ( a ) { a.classList.remove( 'active' ); } );
		if ( current ) current.a.classList.add( 'active' );
	}
	window.addEventListener( 'scroll' , update , { passive: true } );
	update( );
} )();
</script>

</body>
</html>
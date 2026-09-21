<?PHP
# Visuo test runner
# Секция 1: демо вложенности (visuotag + visuoel_tabs)
# Секция 2: тесты в табах (lazy iframe)

error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

# --- visuotag stack ---
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';

# --- visuoel stack ---
require $root_dir . '/sources/visuo/sources/visuoel.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group_element.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_tabs/visuoel_tabs_page.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_tabs.php';

# --- helpers ---
require __DIR__ . '/_vt.php';

# --- список тестов ---
$tests = [ ];
foreach ( glob( __DIR__ . '/visuo*.php' ) as $path ) {
	$tests[ basename( $path ) ] = basename( $path , '.php' );
}
ksort( $tests );

# ============================================================
# СБОРКА ДЕРЕВА
# ============================================================
$ve = new visuoel( );

# ── Секция 1: демо вложенности ─────────────────────────────
$intro = vt_primer( );
$intro->append( vt_text( 'Демонстрация вложенности visuoel' ) );
$intro->append( vt_text( 'primer → tabs → page → tabs → page' ) );

$demo_tabs = new visuoel_tabs( );
$demo_tabs_holder = new visuotag( );
$demo_tabs_holder->el( $demo_tabs );
$intro->append( $demo_tabs_holder );

$ve->add( $intro );

# Вкладка A — простой текст
$demo_a = $demo_tabs->page( );
$demo_a->data( )->set( 'Вкладка A' );
$pa = vt_primer( );
$pa->append( vt_text( 'Содержимое вкладки A' ) );
$pa->append( vt_text( 'Здесь нет вложенности — просто текст.' ) );
$demo_a->add( $pa );

# Вкладка B — с вложенным tabs
$demo_b = $demo_tabs->page( );
$demo_b->data( )->set( 'Вкладка B — с вложенным tabs' );

$nested_tabs = new visuoel_tabs( );
$demo_b->add( $nested_tabs );

$nested_1 = $nested_tabs->page( );
$nested_1->data( )->set( 'Sub-вкладка 1' );
$pn1 = vt_primer( );
$pn1->append( vt_text( 'Третий уровень вложенности' ) );
$pn1->append( vt_text( 'primer внутри tabs внутри tabs.' ) );
$nested_1->add( $pn1 );

$nested_2 = $nested_tabs->page( );
$nested_2->data( )->set( 'Sub-вкладка 2' );
$pn2 = vt_primer( );
$pn2->append( vt_text( 'Второй вложенный таб' ) );
$pn2->append( vt_img( '/img/logo.png' , 'Лого' ) );
$nested_2->add( $pn2 );

# ── Секция 2: тесты ────────────────────────────────────────
$tests_tabs = new visuoel_tabs( );
$ve->add( $tests_tabs );

foreach ( $tests as $file => $title ) {
	$page = $tests_tabs->page( );
	$page->data( )->set( $title );

	$iframe = new visuotag( );
	$iframe = $iframe->tag( 'iframe' , class : 'test_frame' , tag_closed : true );
	$iframe->attrs_add( 'data-src' , '/tests/' . $file );
	$iframe->attrs_add( 'frameborder' , '0' );

	$page->add( $iframe );
}

$ve->init( );
$body_html = $ve->release( );

?><!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Visuo — tests</title>
	<style>
		* { box-sizing: border-box; }
		body { font-family: system-ui, -apple-system, "Segoe UI", sans-serif; margin: 0; background: #eef1f5; color: #222; }
		.page_header { padding: 16px 24px; background: #1e2a3a; color: #fff; display: flex; align-items: center; justify-content: space-between; }
		.page_header h1 { margin: 0; font-size: 18px; font-weight: 600; letter-spacing: 0.5px; }
		.page_header .meta { font-size: 13px; color: #a6b1c2; }
		.page_wrap { padding: 20px; max-width: 1200px; margin: 0 auto; }
		.visuoel > * { margin-bottom: 20px; }

		.primer { background: #fff; border-radius: 8px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
		.primer .text { margin-bottom: 6px; color: #333; }
		.primer > .text:first-child { font-size: 17px; font-weight: 600; color: #111; margin-bottom: 10px; }
		.primer .image img { max-width: 200px; border-radius: 6px; }

		.tabs { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
		.hhead { display: flex; flex-wrap: wrap; gap: 2px; padding: 8px 8px 0; background: #f7f8fa; border-bottom: 1px solid #e0e3e8; }
		.tab_title { padding: 8px 14px; cursor: pointer; border-radius: 6px 6px 0 0; font-size: 13px; color: #4a5568; user-select: none; border: 1px solid transparent; border-bottom: none; position: relative; top: 1px; }
		.tab_title:hover { background: #eef1f5; }
		.tab_title.selected { background: #fff; color: #1e2a3a; font-weight: 600; border-color: #e0e3e8; }
		.tab_page { display: none; padding: 14px; }
		.tab_page.selected { display: block; }

		.tabs .tabs { box-shadow: none; border: 1px solid #cbd5e0; background: #fafbfc; margin-top: 12px; }
		.tabs .tabs .hhead { background: #edf2f7; }
		.tabs .tabs .tab_title.selected { background: #fafbfc; color: #2a5c9a; }

		.test_frame { display: block; width: 100%; height: calc(100vh - 260px); min-height: 500px; border: none; background: #fff; }
	</style>
</head>
<body>

<div class="page_header">
	<h1>Visuo — tests</h1>
	<div class="meta"><?php echo count( $tests ); ?> тестов · lazy-load iframe · секция демо</div>
</div>

<div class="page_wrap">
	<?php echo $body_html; ?>
</div>

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
	if ( ! target ) return;
	target.classList.add( 'selected' );
	lazy_load( target );
}

function lazy_load( page ) {
	var frame = page.querySelector( 'iframe[data-src]' );
	if ( ! frame ) return;
	if ( frame.getAttribute( 'src' ) ) return;
	frame.setAttribute( 'src' , frame.dataset.src );
}

document.addEventListener( 'DOMContentLoaded' , function ( ) {
	var first = document.querySelector( '.tab_page.selected' );
	if ( first ) lazy_load( first );
} );
</script>

</body>
</html>
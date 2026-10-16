<?PHP

# ============================================================
#   Visuo tests — единое окно с вкладками.
# ============================================================

error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

# --- базовые константы ---
define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

# --- stub error-функций (для visuo) ---
if ( ! function_exists( 'error' ) ) {
    function error ( $t , $m , $f = '' , $l = '' ) {
        echo '<div style="color:red">ERROR: ' . htmlspecialchars( $t ) . ' | ' . htmlspecialchars( $m ) . '</div>';
    }
}
if ( ! function_exists( 'error_file' ) ) {
    function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }
}

# --- пути ---
$root_dir  = dirname( __DIR__ );             # www/
$proj_root = dirname( $root_dir );           # корень проекта
define( 'VISUO_SRC' , $proj_root . '/sources/visuo/sources' );
define( 'SOURCES'   , $proj_root . '/sources' );
define( 'TEST_ROOT' , __DIR__ );

# --- visuotag stack ---
require_once VISUO_SRC . '/visuoattrs/visuoattrsclass.php';
require_once VISUO_SRC . '/visuoattrs/visuoattrsstyle.php';
require_once VISUO_SRC . '/visuoattrs/visuoattrsactions.php';
require_once VISUO_SRC . '/visuoattrs/visuoattrsdata.php';
require_once VISUO_SRC . '/visuoattrs.php';
require_once VISUO_SRC . '/visuotag.php';

# --- visuoel stack ---
require_once VISUO_SRC . '/visuoel.php';
require_once VISUO_SRC . '/visuoel/visuoel_group.php';
require_once VISUO_SRC . '/visuoel/visuoel_group_element.php';
require_once VISUO_SRC . '/visuoel/visuoel_tabs/visuoel_tabs_page.php';
require_once VISUO_SRC . '/visuoel/visuoel_tabs.php';

# --- test lib ---
require_once TEST_ROOT . '/lib/AssertException.php';
require_once TEST_ROOT . '/lib/Assert.php';
require_once TEST_ROOT . '/lib/CaseResult.php';
require_once TEST_ROOT . '/lib/Suite.php';
require_once TEST_ROOT . '/lib/Runner.php';
require_once TEST_ROOT . '/lib/View.php';
require_once TEST_ROOT . '/lib/Stubs.php';

# --- discovery + run ---
$suites  = Runner::discover( TEST_ROOT . '/cases' );
$by_name = [];

# --- режим single: ?s=suite ---
$single = isset( $_GET[ 's' ] ) ? (string) $_GET[ 's' ] : '';

if ( $single !== '' ) {
    if ( ! isset( $suites[ $single ] ) ) {
        http_response_code( 404 );
        exit( 'Suite not found: ' . htmlspecialchars( $single ) );
    }
    $suites = [ $single => $suites[ $single ] ];
}

foreach ( $suites as $name => $suite ) {
    $by_name[ $name ] = Runner::run( $suite );
}

# --- собираем дерево через visuo ---
$ve = new visuoel( );

# шапка
$ve->add( View::summary( $by_name ) );

# вкладки
$tabs = $ve->tabs( );
foreach ( $by_name as $name => $results ) {
    $page = $tabs->page( );
    $page->data( )->set( View::tab_title( $name , $results ) );
    $page->add( View::cases( $results ) );
}

$ve->init( );
$body = $ve->release( );

?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Visuo tests</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, "Segoe UI", sans-serif; margin: 0; background: #eef1f5; color: #222; }
        .page_header { padding: 16px 24px; background: #1e2a3a; color: #fff; }
        .page_header h1 { margin: 0; font-size: 18px; }
        .page_wrap { padding: 20px; max-width: 1200px; margin: 0 auto; }

        .test-summary { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
        .test-summary.ok  { background: #e6f7ec; color: #1a7a3e; }
        .test-summary.err { background: #fde8e8; color: #9b1c1c; }
        .summary-line { margin: 4px 0; }

        .tabs { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
        .hhead { display: flex; flex-wrap: wrap; gap: 2px; padding: 8px 8px 0; background: #f7f8fa; border-bottom: 1px solid #e0e3e8; }
        .tab_title { padding: 8px 14px; cursor: pointer; border-radius: 6px 6px 0 0; font-size: 13px; color: #4a5568; user-select: none; border: 1px solid transparent; border-bottom: none; position: relative; top: 1px; }
        .tab_title:hover { background: #eef1f5; }
        .tab_title.selected { background: #fff; color: #1e2a3a; font-weight: 600; border-color: #e0e3e8; }
        .tab_page { display: none; padding: 14px; }
        .tab_page.selected { display: block; }

        .test-cases { font-family: ui-monospace, Consolas, monospace; font-size: 13px; }
        .case { display: grid; grid-template-columns: 1fr 80px; padding: 6px 10px; border-bottom: 1px solid #eef1f5; }
        .case.err { background: #fef2f2; }
        .case-title { color: #1e2a3a; }
        .case.err .case-title { color: #9b1c1c; font-weight: 600; }
        .case-time { text-align: right; color: #8a94a4; font-size: 12px; }
        .case-msg { grid-column: 1 / -1; margin: 6px 0 0; padding: 8px 12px; background: #fff; border-left: 3px solid #d33; color: #333; white-space: pre-wrap; font-size: 12px; overflow: auto; }
		
		.page_header { display: flex; justify-content: space-between; align-items: center; }
		.page_header a:hover { color: #fff; }
	</style>
</head>
<body>

<div class="page_header">
    <h1>Visuo — tests<?= $single !== '' ? ' · ' . htmlspecialchars( $single ) : '' ?></h1>
    <a href="list.php" style="color:#a6b1c2;text-decoration:none;font-size:13px;">Список →</a>
</div>

<div class="page_wrap">
    <?php echo $body; ?>
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
    if ( target ) target.classList.add( 'selected' );
}
</script>

</body>
</html>
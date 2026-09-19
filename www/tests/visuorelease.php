<?PHP
# Visuorelease tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

# --- stubs ---
if ( ! class_exists( 'visuolog' ) ) {
	class visuolog {
		public array $rows = [ ];
		public function __construct ( bool $online = false ) {}
		public function add ( string $m , string $method , string $file , string $line ) : void {
			$this->rows[ ] = $m;
		}
		public function as_tag ( ) : visuotag {
			$vt = new visuotag( );
			$vt->tag( 'div' , class: 'log' );
			return $vt;
		}
	}
}

if ( ! class_exists( 'main' ) ) {
	class fake_render_data {
		public array $data = [ ];
		public function set ( string $k , mixed $v ) : void { $this->data[ $k ] = $v; }
		public function get ( string $k ) : mixed { return $this->data[ $k ] ?? null; }
	}
	class fake_render {
		public fake_render_data $data;
		public function __construct ( ) { $this->data = new fake_render_data( ); }
		public function setdata ( string $k , mixed $v ) : void { $this->data->set( $k , $v ); }
		public function getdata ( string $k ) : mixed { return $this->data->get( $k ); }
	}
	class main {
		public fake_render $render;
		public function __construct ( ) { $this->render = new fake_render( ); }
		public function __get ( $n ) { return null; }
	}
}

require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsclass.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsstyle.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsactions.php';
require $root_dir . '/sources/visuo/sources/visuoattrs/visuoattrsdata.php';
require $root_dir . '/sources/visuo/sources/visuoattrs.php';
require $root_dir . '/sources/visuo/sources/visuotag.php';
require $root_dir . '/sources/visuo/sources/visuolink.php';
require $root_dir . '/sources/visuo/sources/visuometa.php';
require $root_dir . '/sources/visuo/sources/visuorelease_settings.php';
require $root_dir . '/sources/visuo/sources/visuorelease.php';

echo '<h1>Visuorelease tests</h1>';

# 1. construct without args
echo '<h2>1. Construct without args</h2>';
$r = new visuorelease( );
echo 'main null: <b>' . ( $r->main === null ? 'true' : 'false' ) . '</b><br>';
echo 'visuolog set: <b>' . ( $r->visuolog instanceof visuolog ? 'true' : 'false' ) . '</b><br>';
echo 'settings set: <b>' . ( $r->settings instanceof visuorelease_settings ? 'true' : 'false' ) . '</b><br>';
echo 'visuometa set: <b>' . ( $r->visuometa instanceof visuometa ? 'true' : 'false' ) . '</b><br>';
echo 'visuotag set: <b>' . ( $r->visuotag instanceof visuotag ? 'true' : 'false' ) . '</b><br>';

# 2. init created html/head/body
echo '<h2>2. init tree</h2>';
echo 'root found: <b>' . ( $r->visuotag->root( ) !== null ? 'true' : 'false' ) . '</b><br>';
$html = $r->visuotag->find( 'html' );
echo 'html found: <b>' . ( $html !== null ? 'true' : 'false' ) . '</b><br>';
echo 'head found: <b>' . ( $r->visuotag->find( 'head' ) !== null ? 'true' : 'false' ) . '</b><br>';
echo 'body found: <b>' . ( $r->visuotag->find( 'body' ) !== null ? 'true' : 'false' ) . '</b><br>';

# 3. settings accessor
echo '<h2>3. settings()</h2>';
$s = $r->settings( );
echo 'is visuorelease_settings: <b>' . ( $s instanceof visuorelease_settings ? 'true' : 'false' ) . '</b><br>';

# 4. meta accessor
echo '<h2>4. meta()</h2>';
$m = $r->meta( );
echo 'is visuometa: <b>' . ( $m instanceof visuometa ? 'true' : 'false' ) . '</b><br>';

# 5. visuotag accessor
echo '<h2>5. visuotag()</h2>';
$vt = $r->visuotag( );
echo 'is visuotag: <b>' . ( $vt instanceof visuotag ? 'true' : 'false' ) . '</b><br>';
echo 'is body: <b>' . ( $vt->tagname === 'body' ? 'true' : 'false' ) . '</b> (ожидаем body)<br>';

# 6. visuotag_set
echo '<h2>6. visuotag_set</h2>';
$r2 = new visuorelease( );
$new_vt = new visuotag( );
$ret = $r2->visuotag_set( $new_vt );
echo 'returns $this: <b>' . ( $ret === $r2 ? 'true' : 'false' ) . '</b><br>';
echo 'visuotag replaced: <b>' . ( $r2->visuotag === $new_vt ? 'true' : 'false' ) . '</b><br>';

# 7. visuotag_clear
echo '<h2>7. visuotag_clear()</h2>';
$r3 = new visuorelease( );
$r3->visuotag_clear( );
echo 'elements count: <b>' . count( $r3->visuotag->root( )->elements ) . '</b> (ожидаем 0)<br>';

# 8. debug panel — final with log=false debug=false
echo '<h2>8. final (no log, no debug)</h2>';
$m = new main( );
$r4 = new visuorelease( $m );
$m->render->setdata( 'min' , false );

ob_start( );
$r4->final( );
$out = ob_get_clean( );

echo 'has <! doctype: <b>' . ( strpos( $out , '<! doctype' ) !== false ? 'true' : 'false' ) . '</b><br>';
echo 'has html: <b>' . ( strpos( $out , '<html' ) !== false ? 'true' : 'false' ) . '</b><br>';
echo 'has debugging_onlytime: <b>' . ( strpos( $out , 'debugging_onlytime' ) !== false ? 'true' : 'false' ) . '</b> (только если dt_debug существует)<br>';

# 9. final without args (дефолты false)
echo '<h2>9. final без аргументов</h2>';
$m2 = new main( );
$r5 = new visuorelease( $m2 );
$m2->render->setdata( 'min' , false );

ob_start( );
$r5->final( );
$out = ob_get_clean( );

echo 'no crash: <b>true</b><br>';
echo 'output length > 0: <b>' . ( strlen( $out ) > 0 ? 'true' : 'false' ) . '</b><br>';

# 10. final_ajax — data-ajax
echo '<h2>10. final_ajax — data-ajax</h2>';
$m3 = new main( );
$r6 = new visuorelease( $m3 );
$m3->render->setdata( 'min' , false );

ob_start( );
$r6->final_ajax( );
$out = ob_get_clean( );

$html = $r6->visuotag->find( 'html' );
echo 'html has data-ajax: <b>' . ( $html->data( )->has( 'ajax' ) ? 'true' : 'false' ) . '</b><br>';
echo 'html data-ajax value: <b>' . var_export( $html->data( )->get( 'ajax' ) , true ) . '</b> (ожидаем true)<br>';
echo 'html data-ajax-init value: <b>' . var_export( $html->data( )->get( 'ajax-init' ) , true ) . '</b> (ожидаем false)<br>';
echo 'output has data-ajax: <b>' . ( strpos( $out , 'data-ajax' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 11. release_min с null — не падает
echo '<h2>11. release_min(null)</h2>';
$m4 = new main( );
$r7 = new visuorelease( $m4 );
$r7->release_min( null );
echo 'no crash: <b>true</b><br>';
echo 'min set: <b>' . var_export( $m4->render->getdata( 'min' ) , true ) . '</b> (ожидаем true)<br>';

# 12. release_struct с null — не падает
echo '<h2>12. release_struct(null)</h2>';
$m5 = new main( );
$r8 = new visuorelease( $m5 );
$r8->release_struct( null );
echo 'no crash: <b>true</b><br>';

# 13. meta() возвращает visuometa, можно добавлять title
echo '<h2>13. meta + title</h2>';
$m6 = new main( );
$r9 = new visuorelease( $m6 );
$r9->meta( )->title( )->data( 'Test Page' );

$m6->render->setdata( 'min' , false );
ob_start( );
$r9->final( );
$out = ob_get_clean( );

echo 'output has <title>Test Page</title>: <b>' . ( strpos( $out , '<title>Test Page</title>' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 14. full page structure
echo '<h2>14. full page structure</h2>';
$m7 = new main( );
$r10 = new visuorelease( $m7 );
$m7->render->setdata( 'min' , false );
ob_start( );
$r10->final( );
$out = ob_get_clean( );

echo 'has doctype: <b>' . ( strpos( $out , '<! doctype' ) !== false ? 'true' : 'false' ) . '</b><br>';
echo 'has html tag: <b>' . ( strpos( $out , '<html' ) !== false ? 'true' : 'false' ) . '</b><br>';
echo 'has head: <b>' . ( strpos( $out , '<head' ) !== false ? 'true' : 'false' ) . '</b><br>';
echo 'has body: <b>' . ( strpos( $out , '<body' ) !== false ? 'true' : 'false' ) . '</b><br>';

# 15. settings language from cookie
echo '<h2>15. settings from cookie</h2>';
$_COOKIE[ 'language' ] = 'en-en';
$s = new visuorelease_settings( );
echo 'language: <b>' . $s->get( 'language' ) . '</b> (ожидаем en-en)<br>';
unset( $_COOKIE[ 'language' ] );

# 16. settings default language
echo '<h2>16. settings default language</h2>';
$s = new visuorelease_settings( );
echo 'language: <b>' . $s->get( 'language' ) . '</b> (ожидаем ru)<br>';

# 17. settings get unknown
echo '<h2>17. settings get unknown</h2>';
$s = new visuorelease_settings( );
echo 'get nope: <b>' . var_export( $s->get( 'nope' ) , true ) . '</b> (ожидаем null)<br>';

# 18. settings get falsy value
echo '<h2>18. settings get falsy (regression)</h2>';
$s = new visuorelease_settings( );
$s->set( 'language' , '0' );
echo 'get language: <b>' . var_export( $s->get( 'language' ) , true ) . '</b> (ожидаем "0", не null)<br>';

# 19. settings set chainable
echo '<h2>19. settings set chainable</h2>';
$s = new visuorelease_settings( );
$ret = $s->set( 'language' , 'de-de' );
echo 'returns $this: <b>' . ( $ret === $s ? 'true' : 'false' ) . '</b><br>';
echo 'language: <b>' . $s->get( 'language' ) . '</b> (ожидаем de-de)<br>';

# 20. custom settings injection
echo '<h2>20. custom settings injection</h2>';
$m8 = new main( );
$custom = new visuorelease_settings( );
$custom->set( 'language' , 'fr-fr' );
$r11 = new visuorelease( $m8 , $custom );
echo 'settings is custom: <b>' . ( $r11->settings( ) === $custom ? 'true' : 'false' ) . '</b><br>';
echo 'language: <b>' . $r11->settings( )->get( 'language' ) . '</b> (ожидаем fr-fr)<br>';

echo '<hr>done';
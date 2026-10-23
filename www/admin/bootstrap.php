<?PHP

# ============================================================
#   www/admin/index.php — точка входа админки
# ============================================================

define( 'ADMIN_ROOT'   , __DIR__ );
define( 'PROJECT_ROOT' , dirname( dirname( ADMIN_ROOT ) ) );

$admin_cfg = @json_decode( @file_get_contents( PROJECT_ROOT . '/config/admin.config' ) , true ) ?: [ ];
$admin_session_name = (string) ( $admin_cfg[ 'session_name' ] ?? 'admin_sid' );
session_name( $admin_session_name );

session_set_cookie_params( [
	'lifetime' => 0 , 'path' => '/' , 'domain' => '' ,
	'secure' => ( ( $_SERVER[ 'HTTPS' ] ?? '' ) === 'on' ) , 'httponly' => true , 'samesite' => 'Lax' ,
] );
session_start( );

header( 'X-Content-Type-Options: nosniff' );
header( 'X-Frame-Options: DENY' );
header( 'Referrer-Policy: same-origin' );
header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );

if ( ( $_SERVER[ 'HTTPS' ] ?? '' ) === 'on' ) {
	header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
}

header(
	"Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'none'"
);

$cs = PROJECT_ROOT . '/corescripts';

define( 'CS_ROOT' , $cs );
define( 'CS_LIB'  , $cs . '/_lib' );
define( 'CS_CMD'  , $cs . '/commands' );
define( 'CS_TPL'  , $cs . '/templates' );

require_once CS_LIB . '/Fs.php';
require_once CS_LIB . '/Json.php';
require_once CS_LIB . '/Paths.php';
require_once CS_LIB . '/Blocks.php';
require_once CS_LIB . '/Config.php';
require_once CS_LIB . '/Template.php';
require_once CS_LIB . '/Validate.php';
require_once CS_LIB . '/Result.php';
require_once CS_LIB . '/Context.php';

require_once ADMIN_ROOT . '/_lib/Auth.php';
require_once ADMIN_ROOT . '/_lib/View.php';
require_once ADMIN_ROOT . '/_lib/WebRenderer.php';
require_once ADMIN_ROOT . '/_lib/Csrf.php';
require_once ADMIN_ROOT . '/_lib/AuditLog.php';

$auth = new Auth( );
$p = (string) ( $_GET[ 'p' ] ?? 'dashboard' );

if ( $p === 'login' && $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
	require_once PROJECT_ROOT . '/engine/middleware.php';
	middleware::rateLimit( 'admin:login' , 10 , 60 , 1800 );
}

if ( $p === 'login' ) {
	if ( $auth->check( ) ) { header( 'Location: ?p=dashboard' ); exit; }
	$error = null;
	if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
		if ( ! Csrf::check( ) ) { http_response_code( 403 ); exit( 'CSRF token invalid' ); }
		$login = (string) ( $_POST[ 'login' ] ?? '' );
		$pass  = (string) ( $_POST[ 'password' ] ?? '' );
		if ( $auth->login( $login , $pass ) ) {
			AuditLog::login_ok( );
			header( 'Location: ?p=dashboard' );
			exit;
		}
		AuditLog::login_fail( );
		$error = 'Неверный логин или пароль';
	}
	View::render( 'login' , [ 'error' => $error ] );
	exit;
}

if ( $p === 'logout' ) {
	AuditLog::logout( );
	$auth->logout( );
	header( 'Location: ?p=login' );
	exit;
}

if ( ! $auth->check( ) ) { header( 'Location: ?p=login' ); exit; }

if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' && ! Csrf::check( ) ) {
	http_response_code( 403 ); exit( 'CSRF token invalid' );
}

# ============================================================
#   Роутинг (Контроллеры)
# ============================================================
require_once ADMIN_ROOT . '/_lib/AdminRouter.php';
AdminRouter::dispatch( $p );

# ============================================================
#   Остальные страницы — большой switch (временно)
# ============================================================
switch ( $p ) {

	case 'schemes:list' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		if ( $domain === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		$dir = Paths::domain( $domain ) . '/alliasscheme'; $schemes = [];
		if ( is_dir( $dir ) ) { foreach ( glob( $dir . '/alliasscheme_*.php' ) as $file ) { if ( str_ends_with( $file , '.data.php' ) ) continue; $schemes[] = str_replace( [ 'alliasscheme_' , '.php' ] , '' , basename( $file ) ); } }
		View::render( 'schemes_list' , [
			'domain' => $domain , 'schemes' => $schemes ,
			'meta' => [ 'title' => 'Схемы' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => ''] ] , 'actions' => '<a href="?p=scheme:new&domain=' . urlencode($domain) . '" class="btn-primary">+ Добавить схему</a>' , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ]
		] );
		break;

	case 'scheme:copy' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $source = (string) ( $_GET[ 'scheme' ] ?? '' );
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$target = trim((string)$_POST[ 'target' ]);
			if ( $target === '' || !preg_match('/^[a-z][a-z0-9_]*$/', $target) ) { View::render( 'result' , [ 'result' => new Result( ) , 'title' => 'Некорректное имя схемы' , 'back_url' => '?p=schemes:list&domain=' . urlencode($domain) , 'back_label' => 'Назад' ] ); break; }
			$dir = Paths::domain( $domain ) . '/alliasscheme'; $src_php = $dir . '/alliasscheme_' . $source . '.php'; $src_data = $dir . '/alliasscheme_' . $source . '.data.php'; $dst_php = $dir . '/alliasscheme_' . $target . '.php'; $dst_data = $dir . '/alliasscheme_' . $target . '.data.php';
			if ( is_file($src_php) && is_file($src_data) ) {
				$c_php = file_get_contents($src_php); $c_php = str_replace( 'class alliasscheme_' . $source , 'class alliasscheme_' . $target , $c_php ); $c_php = str_replace( 'alliasscheme_' . $source . '.data.php' , 'alliasscheme_' . $target . '.data.php' , $c_php ); file_put_contents( $dst_php , $c_php );
				$c_data = file_get_contents($src_data); file_put_contents( $dst_data , $c_data );
				$main_file = Paths::domain($domain) . '/alliasscheme.php';
				if (is_file($main_file)) {
					$main_c = file_get_contents($main_file); $prop_src = 'alliasscheme_' . $source; $prop_dst = 'alliasscheme_' . $target;
					if (strpos($main_c, $prop_dst) === false) {
						$new_prop = 'public null|' . $prop_dst . ' $' . $target . ' = null;'; $old_prop = 'public null|' . $prop_src . ' $' . $source . ' = null;'; $main_c = str_replace($old_prop, $old_prop . "\n\t" . $new_prop, $main_c);
						$new_init = '$this->' . $target . ' = new ' . $prop_dst . '( $this );'; $old_init = '$this->' . $source . ' = new ' . $prop_src . '( $this );'; $main_c = str_replace($old_init, $old_init . "\n\t\t" . $new_init, $main_c);
						$new_inc = "( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_dst . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );"; $old_inc = "( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_src . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );"; $main_c = str_replace($old_inc, $old_inc . "\n\t\t\t" . $new_inc, $main_c);
						file_put_contents($main_file, $main_c);
					}
				}
				header( 'Location: ?p=schemes:list&domain=' . urlencode($domain) . '&copied=1' ); exit;
			}
		}
		View::render( 'scheme_copy' , [ 'domain' => $domain , 'source' => $source , 'meta' => [ 'title' => 'Копировать схему' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], ['label' => 'Копировать', 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ] ] );
		break;

	case 'scheme:remove' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $scheme = (string) ( $_GET[ 'scheme' ] ?? '' );
		if ( $scheme === 'www' || $scheme === 'visuo' || $scheme === 'error' ) { die('Базовые схемы удалять нельзя'); }
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$confirm = trim((string)$_POST[ 'confirm' ]);
			if ( $confirm === $scheme ) {
				$dir = Paths::domain( $domain ) . '/alliasscheme'; @unlink( $dir . '/alliasscheme_' . $scheme . '.php' ); @unlink( $dir . '/alliasscheme_' . $scheme . '.data.php' );
				$main_file = Paths::domain($domain) . '/alliasscheme.php';
				if (is_file($main_file)) { $main_c = file_get_contents($main_file); $prop = 'alliasscheme_' . $scheme; $lines = explode("\n", $main_c); $new_lines = []; foreach ($lines as $line) { if (strpos($line, $prop) === false) $new_lines[] = $line; } file_put_contents($main_file, implode("\n", $new_lines)); }
				header( 'Location: ?p=schemes:list&domain=' . urlencode($domain) . '&removed=1' ); exit;
			}
		}
		View::render( 'scheme_remove' , [ 'domain' => $domain , 'scheme' => $scheme , 'meta' => [ 'title' => 'Удалить схему' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], ['label' => 'Удалить', 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ] ] );
		break;

	case 'scheme:new' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		if ( $domain === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$target = trim((string)$_POST[ 'target' ]); $source = trim((string)$_POST[ 'source' ]);
			if ( $target === '' || !preg_match('/^[a-z][a-z0-9_]*$/', $target) ) { $errors[] = 'Некорректное имя схемы (только a-z, 0-9, _)'; }
			else {
				$dir = Paths::domain( $domain ) . '/alliasscheme'; $src_php = $dir . '/alliasscheme_' . $source . '.php'; $src_data = $dir . '/alliasscheme_' . $source . '.data.php'; $dst_php = $dir . '/alliasscheme_' . $target . '.php'; $dst_data = $dir . '/alliasscheme_' . $target . '.data.php';
				if ( is_file($src_php) && is_file($src_data) && !is_file($dst_php) ) {
					$c_php = file_get_contents($src_php); $c_php = str_replace( 'class alliasscheme_' . $source , 'class alliasscheme_' . $target , $c_php ); $c_php = str_replace( 'alliasscheme_' . $source . '.data.php' , 'alliasscheme_' . $target . '.data.php' , $c_php ); file_put_contents( $dst_php , $c_php );
					file_put_contents( $dst_data , file_get_contents($src_data) );
					$main_file = Paths::domain($domain) . '/alliasscheme.php';
					if (is_file($main_file)) {
						$main_c = file_get_contents($main_file); $prop_src = 'alliasscheme_' . $source; $prop_dst = 'alliasscheme_' . $target;
						if (strpos($main_c, $prop_dst) === false) {
							$main_c = str_replace('public null|' . $prop_src . ' $' . $source . ' = null;', 'public null|' . $prop_src . ' $' . $source . ' = null;' . "\n\t" . 'public null|' . $prop_dst . ' $' . $target . ' = null;', $main_c);
							$main_c = str_replace('$this->' . $source . ' = new ' . $prop_src . '( $this );', '$this->' . $source . ' = new ' . $prop_src . '( $this );' . "\n\t\t" . '$this->' . $target . ' = new ' . $prop_dst . '( $this );', $main_c);
							$main_c = str_replace("( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_src . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );", "( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_dst . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );\n\t\t\t( include_once \$path = __DIR__ . DS . 'alliasscheme' . DS . '" . $prop_src . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );", $main_c);
							file_put_contents($main_file, $main_c);
						}
					}
					header( 'Location: ?p=schemes:list&domain=' . urlencode($domain) . '&saved=1' ); exit;
				} else { $errors[] = 'Исходная схема не найдена или такая уже существует'; }
			}
		}
		$dir = Paths::domain( $domain ) . '/alliasscheme'; $schemes = [];
		if ( is_dir($dir) ) { foreach ( glob( $dir . '/alliasscheme_*.php' ) as $file ) { if ( str_ends_with( $file , '.data.php' ) ) continue; $schemes[] = str_replace( [ 'alliasscheme_' , '.php' ] , '' , basename( $file ) ); } }
		View::render( 'scheme_new' , [ 'domain' => $domain , 'schemes' => $schemes , 'errors' => $errors ?? [] , 'meta' => [ 'title' => 'Новая схема' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], ['label' => 'Новая схема', 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] ] ] );
		break;

	case 'scheme:edit' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); 
		$scheme = (string) ( $_GET[ 'scheme' ] ?? '' );
		
		$file_php  = Paths::domain( $domain ) . '/alliasscheme/alliasscheme_' . $scheme . '.php'; 
		$file_data = Paths::domain( $domain ) . '/alliasscheme/alliasscheme_' . $scheme . '.data.php';
		
		if ( ! is_file( $file_data ) ) { 
			View::render( 'result' , [ 
				'result' => new Result( ) , 
				'meta' => [ 
					'title' => 'Файл схемы не найден' , 
					'breadcrumbs' => [ 
						['label' => 'Домены', 'url' => '?p=dashboard'], 
						['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], 
						['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], 
						['label' => $scheme ?: 'Неизвестно', 'url' => ''] 
					] 
				] ,
				'back_url' => '?p=schemes:list&domain=' . urlencode($domain) , 
				'back_label' => 'К списку схем' 
			] ); 
			break; 
		}
		
		$data = require $file_data;
		
		// Собираем список всех схем домена для сайдбара (только те, у которых есть .data.php)
		$dir = Paths::domain( $domain ) . '/alliasscheme';
		$schemes = [];
		if ( is_dir( $dir ) ) {
			foreach ( glob( $dir . '/alliasscheme_*.php' ) as $f ) {
				if ( str_ends_with( $f , '.data.php' ) ) continue;
				$name = str_replace( [ 'alliasscheme_' , '.php' ] , '' , basename( $f ) );
				if ( is_file( $dir . '/alliasscheme_' . $name . '.data.php' ) ) {
					$schemes[] = $name;
				}
			}
		}
		sort( $schemes );

		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$json = $_POST[ 'scheme_json' ] ?? ''; 
			$new_data = json_decode( $json , true );
			if ( is_array( $new_data ) ) { 
				$php_code = '<?PHP' . "\n# ============================================================\n#   alliasscheme_" . $scheme . ".data.php — структура схемы\n# ============================================================\nreturn " . var_export($new_data, true) . ";\n"; 
				file_put_contents($file_data, $php_code); 
			}
			header('Location: ?p=scheme:edit&domain=' . urlencode($domain) . '&scheme=' . urlencode($scheme) . '&saved=1'); exit;
		}
		
		View::render( 'scheme_edit' , [
			'domain' => $domain , 'scheme' => $scheme , 'schemes' => $schemes , 'scheme_data' => $data ,
			'meta' => [ 
				'title' => 'Схема: '.$scheme , 
				'breadcrumbs' => [ 
					['label' => 'Домены', 'url' => '?p=dashboard'], 
					['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], 
					['label' => 'Схемы', 'url' => '?p=schemes:list&domain='.urlencode($domain)], 
					['label' => $scheme, 'url' => ''] 
				] , 
				'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'schemes'] 
			]
		] );
		break;

	case 'stub:edit' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		if ( $domain === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		$cfg = new Config( Paths::config() ); $real = Paths::real( $domain );
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$stub_title = trim( (string) ( $_POST[ 'stub_title' ] ?? '' ) ); $stub_text = trim( (string) ( $_POST[ 'stub_text' ] ?? '' ) );
			Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $stub_title , $stub_text ) { $stub = $cfg->getKeys( [ 'domains_stub' , $real ] , [] ); $stub['title'] = $stub_title; $stub['text'] = $stub_text; $cfg->setKeys( [ 'domains_stub' , $real ] , $stub ); } );
			AuditLog::action_ok( 'stub:edit' , [ 'domain' => $real , 'title' => $stub_title ] );
			header( 'Location: ?p=stub:edit&domain=' . urlencode($domain) . '&saved=1' ); exit;
		}
		$stub = $cfg->getKeys( [ 'domains_stub' , $real ] , [] );
		View::render( 'stub_edit' , [
			'domain' => $domain , 'real' => $real , 'stub' => $stub , 'saved' => ! empty($_GET['saved']) ,
			'meta' => [ 'title' => 'Заглушка домена' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Заглушка', 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'stub'] ]
		] );
		break;

	case 'stubs:list' :
		$stubs_cfg = new Config( PROJECT_ROOT . '/config/site.stubs.config' );
		$stubs = $stubs_cfg->get( 'stubs' ) ?? [];
		View::render( 'stubs_list' , [
			'stubs' => $stubs,
			'meta' => [ 'title' => 'Библиотека заглушек' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Заглушки', 'url' => ''] ] ]
		] );
		break;
	
	case 'stubs:edit' :
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$stubs_data = $_POST[ 'stubs' ] ?? [];
			try {
				Config::mutate( PROJECT_ROOT . '/config/site.stubs.config' , function ( Config $cfg ) use ( $stubs_data ) {
					$cfg->setKeys( [ 'stubs' ] , $stubs_data );
				} );
				header( 'Location: ?p=stubs:list&saved=1' ); exit;
			} catch ( Throwable $e ) {}
		}
		break;
	
	default :
		http_response_code( 404 );
		echo '<h1>404</h1><p>Страница не найдена: ' . htmlspecialchars( $p ) . '</p>';
}
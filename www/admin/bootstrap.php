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

switch ( $p ) {

	case 'dashboard' :
		$cfg = new Config( Paths::config() );
		$domains = (array) $cfg->get( 'domains_real' );
		$real_domains = [ ]; $aliases = [ ];
		foreach ( $domains as $d => $real ) {
			if ( $d === $real ) $real_domains[ $d ] = true;
			else $aliases[ $real ][ ] = $d;
		}
		$rows = [ ];
		foreach ( array_keys( $real_domains ) as $real ) {
			$alliases = [ ];
			foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) {
				if ( $a === '' ) continue; $alliases[ ] = $a;
			}
			$rows[ ] = [
				'domain' => $real , 'ns' => $cfg->getKeys( [ 'domains_namespace' , $real ] ) ?? '—' ,
				'avail' => ! empty( $cfg->getKeys( [ 'domains_available' , $real ] ) ) ,
				'fs' => is_dir( Paths::domain( $real ) ) ,
				'stub' => ! empty( $cfg->getKeys( [ 'domains_stub' , $real , 'enabled' ] ) ) ,
				'alliases' => $alliases , 'aliases' => $aliases[ $real ] ?? [ ] ,
			];
		}
		View::render( 'dashboard' , [
			'domains' => $rows,
			'meta' => [
				'title' => 'Домены',
				'actions' => '<a href="?p=stubs:list" class="btn-edit">Библиотека заглушек</a> <a href="?p=domain:new" class="btn-primary">+ Добавить домен</a>'
			]
		] );
		break;

	case 'domain:new' :
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/DomainNew.php';
			$result = DomainNew::run( [ 'name' => $_POST[ 'domain' ] ?? '' , 'register' => ! empty( $_POST[ 'register' ] ) ] );
			AuditLog::action_ok( 'domain:new' , [ 'domain' => $_POST[ 'domain' ] ?? '' , 'register' => ! empty( $_POST[ 'register' ] ) , 'ok' => $result->ok ] );
			View::render( 'result' , [ 'result' => $result , 'title' => 'Создание домена' , 'back_url' => '?p=dashboard' , 'back_label' => 'К списку домен' ] );
		} else {
			View::render( 'domain_new' , [ 'meta' => [ 'title' => 'Создать домен' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Создать домен', 'url' => ''] ] ] ] );
		}
		break;

	case 'allias:new' :
		$cfg = new Config( Paths::config() );
		$domains_raw = (array) $cfg->get( 'domains_real' );
		$domains = [ ];
		foreach ( $domains_raw as $d => $real ) { if ( $d === $real ) $domains[ ] = $d; }
		$prefill_domain = (string) ( $_GET[ 'domain' ] ?? '' );
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/AlliasNew.php';
			$post_domain = (string) ( $_POST[ 'domain' ] ?? '' );
			$result = AlliasNew::run( [ 'name' => $_POST[ 'name' ] ?? '' , 'domain' => $post_domain , 'register' => ! empty( $_POST[ 'register' ] ) ] );
			AuditLog::action_ok( 'allias:new' , [ 'domain' => $post_domain , 'name' => $_POST[ 'name' ] ?? '' , 'ok' => $result->ok ] );
			View::render( 'result' , [ 'result' => $result , 'title' => 'Создание аллиаса' , 'back_url' => '?p=allias:list&domain=' . urlencode( $post_domain ) , 'back_label' => 'К аллиасам' ] );
		} else {
			View::render( 'allias_new' , [
				'domains' => $domains , 'prefill_domain' => $prefill_domain,
				'meta' => [ 'title' => 'Создать аллиас' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Создать аллиас', 'url' => ''] ] ]
			] );
		}
		break;

	case 'domain:edit' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		if ( Validate::domain( $domain ) !== null ) { header( 'Location: ?p=dashboard' ); exit; }
		$cfg = new Config( Paths::config() ); $real = Paths::real( $domain );
		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) { header( 'Location: ?p=dashboard' ); exit; }
		$stubs_cfg = new Config( PROJECT_ROOT . '/config/site.stubs.config' );
		$stubs = $stubs_cfg->get( 'stubs' ) ?? [];
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$title = trim( (string) ( $_POST[ 'title' ] ?? '' ) ); $errors = [];
			if ( $title === '' ) { $errors[] = 'Title не может быть пустым'; }
			$domain_available = ! empty( $_POST[ 'domain_available' ] );
			$stub_current = trim( (string) ( $_POST[ 'stub_current' ] ?? 'maintenance' ) );
			$stub_until_date = trim( (string) ( $_POST[ 'stub_until_date' ] ?? '' ) );
			$stub_until = $stub_until_date !== '' ? strtotime( $stub_until_date ) : 0;
			if ( empty( $errors ) ) {
				try {
					Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $title , $domain_available , $stub_current , $stub_until ) {
						$cfg->setKeys( [ 'domains_titles' , $real ] , $title );
						$cfg->setKeys( [ 'domains_available' , $real ] , $domain_available );
						$cfg->setKeys( [ 'domains_stub' , $real , 'current' ] , $stub_current );
						$cfg->setKeys( [ 'domains_stub' , $real , 'until' ] , $stub_until );
					} );
					AuditLog::action_ok( 'domain:edit' , [ 'domain' => $real , 'title' => $title ] );
					header( 'Location: ?p=domain:edit&domain=' . urlencode( $domain ) . '&saved=1' ); exit;
				} catch ( Throwable $e ) { $errors[] = 'Ошибка сохранения: ' . $e->getMessage(); }
			}
			$current = [ 'title' => $title, 'avail' => $domain_available, 'stub' => $stub_current, 'until' => $stub_until ];
			View::render( 'domain_edit' , [ 'domain' => $domain , 'current' => $current , 'stubs' => $stubs , 'saved' => false , 'errors' => $errors , 'meta' => [ 'title' => 'Настройки домена' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'main'] ] ] );
			break;
		}
		$title = (string) ( $cfg->getKeys( [ 'domains_titles' , $real ] ) ?? $cfg->get( 'title' , '' ) );
		$current = [ 'title' => $title, 'avail' => ! empty( $cfg->getKeys( [ 'domains_available' , $real ] ) ), 'stub' => $cfg->getKeys( [ 'domains_stub' , $real , 'current' ] , 'maintenance' ), 'until' => $cfg->getKeys( [ 'domains_stub' , $real , 'until' ] , 0 ) ];
		View::render( 'domain_edit' , [ 'domain' => $domain , 'current' => $current , 'stubs' => $stubs , 'saved' => ! empty( $_GET[ 'saved' ] ) , 'errors' => [] , 'meta' => [ 'title' => 'Настройки домена' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'main'] ] ] );
		break;

	case 'allias:toggle' :
		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) { http_response_code( 405 ); exit( 'Method not allowed' ); }
		$domain = (string) ( $_POST[ 'domain' ] ?? '' ); $name = (string) ( $_POST[ 'name' ] ?? '' ); $value = (string) ( $_POST[ 'value' ] ?? '' );
		if ( $domain === '' || $name === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		require_once CS_CMD . '/AlliasToggle.php';
		$result = AlliasToggle::run( [ 'domain' => $domain , 'name' => $name , 'value' => $value ] );
		AuditLog::action_ok( 'allias:toggle' , [ 'domain' => $domain , 'name' => $name , 'value' => $value , 'ok' => $result->ok ] );
		header( 'Location: ?p=allias:list&domain=' . urlencode( $domain ) );
		exit;

	case 'domain:toggle' :
		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) { http_response_code( 405 ); exit( 'Method not allowed' ); }
		$domain = (string) ( $_POST[ 'domain' ] ?? '' ); $value = (string) ( $_POST[ 'value' ] ?? '' );
		if ( $domain === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		require_once CS_CMD . '/DomainToggle.php';
		$result = DomainToggle::run( [ 'domain' => $domain , 'value' => $value ] );
		AuditLog::action_ok( 'domain:toggle' , [ 'domain' => $domain , 'value' => $value , 'ok' => $result->ok ] );
		header( 'Location: ?p=dashboard' );
		exit;

	case 'domain:remove' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		if ( $domain === '' || Validate::domain( $domain ) !== null ) { header( 'Location: ?p=dashboard' ); exit; }
		$cfg = new Config( Paths::config() );
		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) { header( 'Location: ?p=dashboard' ); exit; }
		$real = Paths::real( $domain ); $alliases = [ ];
		foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) { if ( $a === '' ) continue; $alliases[ ] = $a; }
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$confirm_name = trim( (string) ( $_POST[ 'confirm_name' ] ?? '' ) ); $confirm_checked = ! empty( $_POST[ 'confirm_checked' ] ); $errors = [ ];
			if ( ! $confirm_checked ) { $errors[ ] = 'Подтвердите, что понимаете необратимость'; }
			if ( $confirm_name !== $real ) { $errors[ ] = "Имя real-домена введено неверно (нужно: {$real})"; }
			if ( empty( $errors ) ) {
				require_once CS_CMD . '/DomainRemove.php';
				$result = DomainRemove::run( [ 'name' => $real , 'force' => true ] );
				AuditLog::action_ok( 'domain:remove' , [ 'domain' => $real , 'ok' => $result->ok ] );
				View::render( 'result' , [ 'result' => $result , 'title' => 'Удаление домена: ' . $real , 'back_url' => '?p=dashboard' , 'back_label' => 'К списку домен' ] );
				break;
			}
			View::render( 'domain_remove' , [ 'domain' => $domain , 'real' => $real , 'errors' => $errors , 'alliases' => $alliases , 'meta' => [ 'title' => 'Удалить домен' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Удалить домен', 'url' => ''] ] ] ] );
			break;
		}
		View::render( 'domain_remove' , [ 'domain' => $domain , 'real' => $real , 'errors' => [ ] , 'alliases' => $alliases , 'meta' => [ 'title' => 'Удалить домен' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Удалить домен', 'url' => ''] ] ] ] );
		break;

	case 'allias:remove' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $name = (string) ( $_GET[ 'name' ] ?? '' );
		if ( $domain === '' || $name === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		if ( Validate::domain( $domain ) !== null || Validate::allias_name( $name ) !== null ) { header( 'Location: ?p=dashboard' ); exit; }
		$cfg = new Config( Paths::config() ); $real = Paths::real( $domain ); $al = $cfg->getKeys( [ 'alliases_real' , $real , $name ] );
		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null || $al === null ) { header( 'Location: ?p=allias:list&domain=' . urlencode( $domain ) ); exit; }
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$confirm_checked = ! empty( $_POST[ 'confirm_checked' ] ); $confirm_name = trim( (string) ( $_POST[ 'confirm_name' ] ?? '' ) ); $errors = [ ];
			if ( ! $confirm_checked ) { $errors[ ] = 'Подтвердите, что понимаете необратимость'; }
			if ( $confirm_name !== $name ) { $errors[ ] = 'Имя аллиаса введено неверно'; }
			if ( empty( $errors ) ) {
				require_once CS_CMD . '/AlliasRemove.php';
				$result = AlliasRemove::run( [ 'name' => $name , 'domain' => $real , 'force' => true ] );
				AuditLog::action_ok( 'allias:remove' , [ 'domain' => $real , 'name' => $name , 'ok' => $result->ok ] );
				View::render( 'result' , [ 'result' => $result , 'title' => "Удаление аллиаса: {$real} / {$name}" , 'back_url' => '?p=allias:list&domain=' . urlencode( $domain ) , 'back_label' => 'К аллиасам' ] );
				break;
			}
			View::render( 'allias_remove' , [ 'domain' => $domain , 'name' => $name , 'errors' => $errors , 'meta' => [ 'title' => 'Удалить аллиас' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Удалить аллиас', 'url' => ''] ] ] ] );
			break;
		}
		View::render( 'allias_remove' , [ 'domain' => $domain , 'name' => $name , 'errors' => [ ] , 'meta' => [ 'title' => 'Удалить аллиас' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Удалить аллиас', 'url' => ''] ] ] ] );
		break;

	case 'pages:tree' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $allias = (string) ( $_GET[ 'allias' ] ?? '' );
		if ( $domain === '' || $allias === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		if ( Validate::domain( $domain ) !== null || Validate::allias_name( $allias ) !== null ) { header( 'Location: ?p=dashboard' ); exit; }
		$cfg = new Config( Paths::config() );
		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) { header( 'Location: ?p=dashboard' ); exit; }
		require_once ADMIN_ROOT . '/_lib/Scan.php';
		$base = Paths::algoritm( $domain , $allias ); $tree = is_dir( $base ) ? Scan::pageTree( $base ) : [ ];
		View::render( 'pages_tree' , [
			'domain' => $domain , 'allias' => $allias , 'logic' => Paths::logic( ) , 'tree' => $tree , 'has_dir' => is_dir( $base ) ,
			'meta' => [
				'title' => 'Страницы' ,
				'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Страницы', 'url' => ''] ] ,
				'actions' => '<a href="?p=page:new&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '" class="btn-primary">+ Новая страница</a>' ,
				'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages']
			]
		] );
		break;

	case 'page:visual' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $allias = (string) ( $_GET[ 'allias' ] ?? '' ); $path = trim( (string) ( $_GET[ 'path' ] ?? '' ) , '/' );
		$base = Paths::algoritm( $domain , $allias ); $cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/' , '_' , $path ) ); $sub_dir = $path === '' ? $base : $base . '/' . $path; $json_file = $sub_dir . '/' . $cls_short . '.json';
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$json_code = (string) ( $_POST[ 'json_code' ] ?? '' );
			try { if ( $json_code !== '' ) Fs::writeAtomic( $json_file , $json_code ); AuditLog::action_ok( 'page:visual' , [ 'domain' => $domain , 'allias' => $allias , 'path' => $path ] ); } catch ( Throwable $e ) {}
			header( 'Location: ?p=page:visual&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&saved=1' ); exit;
		}
		$json_data = is_file( $json_file ) ? file_get_contents( $json_file ) : '{"tag":"div","class":"landing-container","children":[]}';
		$lang_cfg = is_file(PROJECT_ROOT . '/config/lang.config') ? json_decode(file_get_contents(PROJECT_ROOT . '/config/lang.config'), true) : [];
		$lang_default = $lang_cfg['default'] ?? 'ru-ru';
		$host = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'www.damoony2.ru');
		$url_path = ($allias === 'www' || $allias === '') ? '' : $allias . '/';
		$url_path .= ($path === '') ? $lang_default . '/index' : str_replace('lang', $lang_default, $path);
		$preview_url = rtrim($host . '/' . $url_path, '/') . '/';
		View::render( 'page_visual' , [
			'domain' => $domain , 'allias' => $allias , 'path' => $path , 'cls_short' => $cls_short , 'json_data' => $json_data , 'preview_url' => $preview_url ,
			'meta' => [ 'title' => 'Визуальный редактор' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Визуально: '.($path===''?'(корень)':$path), 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ]
		] );
		break;

	case 'page:code' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $allias = (string) ( $_GET[ 'allias' ] ?? '' ); $path = trim( (string) ( $_GET[ 'path' ] ?? '' ) , '/' ); $method_name = (string) ( $_GET[ 'method' ] ?? '' );
		$base = Paths::algoritm( $domain , $allias ); $cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/' , '_' , $path ) ); $sub_dir = $path === '' ? $base : $base . '/' . $path; $php_file = $sub_dir . '/' . $cls_short . '.php'; $json_file = $sub_dir . '/' . $cls_short . '.rules.json';
		if ( ! is_file( $php_file ) ) { header( 'Location: ?p=pages:tree&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) ); exit; }
		$parent_methods = [ 'algoritm' => "public function algoritm ( ) {\n\t\n}", 'guest' => "public function guest ( ) {\n\treturn false;\n}", 'logged' => "public function logged ( ) {\n\treturn false;\n}", 'ajax_guest' => "public function ajax_guest ( ) {\n\treturn false;\n}", 'ajax_logged' => "public function ajax_logged ( ) {\n\treturn false;\n}", 'default' => "public function default ( ) {\n\treturn parent::default();\n}" ];
		$extract_methods = function( $code ) {
			$regex = '/(public|protected|private)?\s*function\s+(\w+)\s*\([^)]*\)\s*(?::\s*\w+)?\s*\{/'; preg_match_all( $regex , $code , $matches , PREG_OFFSET_CAPTURE ); $methods = [];
			foreach ( $matches[0] as $index => $match ) {
				$start_pos = $match[1]; $name = $matches[2][$index][0]; $brace_level = 1; $pos = $start_pos + strlen($match[0]); $len = strlen($code);
				while ( $pos < $len && $brace_level > 0 ) { $char = $code[$pos]; if ( $char === '{' ) $brace_level++; elseif ( $char === '}' ) $brace_level--; $pos++; }
				$methods[$name] = [ 'code' => substr($code, $start_pos, $pos - $start_pos), 'start' => $start_pos, 'end' => $pos ];
			} return $methods;
		};
		$php_code = file_get_contents( $php_file );
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			if ( isset( $_POST[ 'save_json' ] ) ) { $json_code = (string) ( $_POST[ 'json_code' ] ?? '' ); try { if ( $json_code !== '' ) Fs::writeAtomic( $json_file , $json_code ); } catch ( Throwable $e ) {} header( 'Location: ?p=page:code&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&saved=1' ); exit; }
			if ( isset( $_POST[ 'save_method' ] ) ) {
				$new_method_code = (string) ( $_POST[ 'method_code' ] ?? '' ); $methods = $extract_methods( $php_code );
				if ( isset( $methods[ $method_name ] ) ) { $m = $methods[ $method_name ]; $php_code = substr($php_code, 0, $m['start']) . $new_method_code . substr($php_code, $m['end']); try { Fs::writeAtomic( $php_file , $php_code ); } catch ( Throwable $e ) {} }
				header( 'Location: ?p=page:code&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&method=' . urlencode($method_name) . '&saved=1' ); exit;
			}
			if ( isset( $_POST[ 'add_method' ] ) ) {
				$new_method = (string) $_POST[ 'add_method' ]; $methods = $extract_methods( $php_code );
				if ( isset( $parent_methods[ $new_method ] ) && !isset( $methods[ $new_method ] ) ) {
					$last_brace = strrpos( $php_code , '}' ); if ( $last_brace !== false ) { $stub = "\n\t" . $parent_methods[ $new_method ] . "\n\n"; $php_code = substr_replace( $php_code , $stub , $last_brace , 0 ); try { Fs::writeAtomic( $php_file , $php_code ); } catch ( Throwable $e ) {} }
				}
				header( 'Location: ?p=page:code&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '&method=' . urlencode($new_method) . '&saved=1' ); exit;
			}
		}
		$methods = $extract_methods( $php_code ); $current_method_code = $method_name && isset($methods[$method_name]) ? $methods[$method_name]['code'] : ''; $json_code = is_file( $json_file ) ? file_get_contents( $json_file ) : ''; $tab = $_GET['tab'] ?? 'logic';
		View::render( 'page_code' , [
			'domain' => $domain , 'allias' => $allias , 'path' => $path , 'cls_short' => $cls_short , 'methods' => $methods , 'parent_methods' => $parent_methods , 'method_name' => $method_name , 'method_code' => $current_method_code , 'json_code' => $json_code , 'tab' => $tab ,
			'meta' => [ 'title' => 'Редактор кода' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Код: '.($path===''?'(корень)':$path), 'url' => ''] ] , 'actions' => '<a href="?p=page:visual&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '" class="btn-edit">Визуально</a> <a href="?p=page:edit&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '" class="btn-edit">Настройки</a>' , 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ]
		] );
		break;

	case 'page:edit' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $allias = (string) ( $_GET[ 'allias' ] ?? '' ); $path = trim( (string) ( $_GET[ 'path' ] ?? '' ) , '/' ); $path_err = Validate::page_path( $path );
		if ( $path_err !== null ) { View::render( 'result' , [ 'result' => new Result( ) , 'title' => 'Некорректный путь: ' . $path_err , 'back_url' => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) , 'back_label' => 'К страницам' ] ); break; }
		if ( $domain === '' || $allias === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		$cfg = new Config( Paths::config() ); $real = $cfg->getKeys( [ 'domains_real' , $domain ] );
		if ( $real === null ) { header( 'Location: ?p=dashboard' ); exit; }
		$base = Paths::algoritm( $domain , $allias ); $cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/' , '_' , $path ) ); $sub_dir = $path === '' ? $base : $base . '/' . $path; $php_file = $sub_dir . '/' . $cls_short . '.php'; $rules_file = $sub_dir . '/' . $cls_short . '.rules.json';
		if ( ! is_file( $php_file ) ) { View::render( 'result' , [ 'result' => new Result( ) , 'title' => "Страница не найдена: {$path}" , 'back_url' => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) , 'back_label' => 'К страницам' ] ); break; }
		
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$available_raw = (string) ( $_POST[ 'available' ] ?? '' ); 
			$auth          = ! empty( $_POST[ 'auth' ] );
			
			$lines = preg_split( '/\r\n|\n|\r/' , $available_raw ); $available = [ ]; $seen = [ ];
			foreach ( $lines as $line ) { $line = trim( $line ); if ( isset( $seen[ $line ] ) ) continue; $seen[ $line ] = true; $available[ ] = $line; }
			while ( ! empty( $available ) && end( $available ) === '' ) { array_pop( $available ); }
			if ( empty( $available ) ) { $available = [ '' ]; }
			
			$data = [ 
				'available' => $available , 
				'auth'      => $auth 
			];
			
			try { 
				Json::writeAtomic( $rules_file , $data ); 
				AuditLog::action_ok( 'page:edit' , [ 'domain' => $domain , 'allias' => $allias , 'path' => $path ] ); 
			} catch ( Throwable $e ) { 
				View::render( 'result' , [ 'result' => new Result( ) , 'title' => 'Ошибка записи: ' . $e->getMessage( ) , 'back_url' => '?p=page:edit&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) . '&path=' . urlencode( $path ) , 'back_label' => 'Назад' ] ); break; 
			}
			header( 'Location: ?p=page:edit&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) . '&path=' . urlencode( $path ) . '&saved=1' ); exit;
		}
		
		$current = [ 'available' => [ ] , 'auth' => false ];
		$json = Json::read( $rules_file );
		if ( is_array( $json ) ) {
			$current[ 'available' ] = isset( $json[ 'available' ] ) && is_array( $json[ 'available' ] ) ? $json[ 'available' ] : [ ];
			$current[ 'auth' ]      = ! empty( $json[ 'auth' ] );
		}
		
		View::render( 'page_edit' , [
			'domain'    => $domain ,
			'allias'    => $allias ,
			'path'      => $path ,
			'cls_short' => $cls_short ,
			'json_file' => $rules_file ,
			'current'   => $current ,
			'saved'     => ! empty( $_GET[ 'saved' ] ) ,
			'meta'      => [ 'title' => 'Настройки страницы: '.($path===''?'(корень)':$path) , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Страницы', 'url' => '?p=pages:tree&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => $path===''?'(корень)':$path, 'url' => ''] ] , 'actions' => '<a href="?p=page:visual&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '" class="btn-edit">Визуально</a> <a href="?p=page:code&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&path=' . urlencode($path) . '" class="btn-edit">Код</a>' , 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ]
		] );
		break;

	case 'page:new' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $allias = (string) ( $_GET[ 'allias' ] ?? '' );
		if ( $domain === '' || Validate::domain( $domain ) !== null ) { header( 'Location: ?p=dashboard' ); exit; }
		$cfg = new Config( Paths::config() );
		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) { header( 'Location: ?p=dashboard' ); exit; }
		$real = Paths::real( $domain ); $alliases = [ ];
		foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) { if ( $a === '' ) continue; $alliases[ ] = $a; }
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$allias = (string) ( $_POST[ 'allias' ] ?? '' ); $name = trim( (string) ( $_POST[ 'name' ] ?? '' ) ); $parent = trim( (string) ( $_POST[ 'parent' ] ?? '' ) , '/' ); $path_err = Validate::page_path( $parent );
			if ( $path_err !== null ) { View::render( 'page_new' , [ 'domain' => $domain , 'allias' => $allias , 'parent' => $parent , 'alliases' => $alliases , 'error' => $path_err , 'meta' => [ 'title' => 'Создать страницу' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Страницы', 'url' => '?p=pages:tree&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Создать страницу', 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ] ] ); break; }
			require_once CS_CMD . '/PageNew.php';
			$result = PageNew::run( [ 'name' => $name , 'parent' => $parent , 'domain' => $domain , 'allias' => $allias ] );
			AuditLog::action_ok( 'page:new' , [ 'domain' => $domain , 'allias' => $allias , 'name' => $name , 'ok' => $result->ok ] );
			View::render( 'result' , [ 'result' => $result , 'title' => "Создание страницы: {$domain} / {$allias} / {$name}" , 'back_url' => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) , 'back_label' => 'К страницам' ] );
			break;
		}
		View::render( 'page_new' , [ 'domain' => $domain , 'allias' => $allias , 'parent' => (string) ( $_GET[ 'parent' ] ?? '' ) , 'alliases' => $alliases , 'meta' => [ 'title' => 'Создать страницу' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Страницы', 'url' => '?p=pages:tree&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Создать страницу', 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'pages'] ] ] );
		break;

	case 'allias:fillers' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $allias = (string) ( $_GET[ 'allias' ] ?? '' );
		if ( $domain === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		if ( $allias === '' ) { header( 'Location: ?p=allias:list&domain=' . urlencode( $domain ) ); exit; }
		require_once ADMIN_ROOT . '/_lib/Scan.php';
		$rows = Scan::fillersFor( $domain , $allias );
		View::render( 'allias_fillers' , [
			'domain' => $domain , 'allias' => $allias , 'rows' => $rows ,
			'meta' => [ 'title' => 'Филлеры' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Филлеры', 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'fillers'] ]
		] );
		break;

	case 'filler:edit' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); 
		$allias = (string) ( $_GET[ 'allias' ] ?? '' ); 
		$block  = (string) ( $_GET[ 'block' ] ?? '' );
		
		$f_dir  = Paths::block( $domain , $block ); 
		$php_f  = $f_dir . '/' . $block . '_' . $allias . '.php'; 
		$data_f = $f_dir . '/' . $block . '_' . $allias . '.data.php';
		
		if ( ! is_file( $php_f ) ) { 
			header( 'Location: ?p=allias:fillers&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) ); 
			exit; 
		}
		
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			Fs::writeAtomic( $php_f , $_POST[ 'php' ] ); 
			Fs::writeAtomic( $data_f , $_POST[ 'data' ] );
			header( 'Location: ?p=filler:edit&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&block=' . urlencode($block) . '&saved=1' ); 
			exit;
		}
		
		$php_content = file_get_contents( $php_f );
		$data_content = is_file( $data_f ) ? file_get_contents( $data_f ) : "<?PHP\nreturn [\n\t\n];\n";

		View::render( 'filler_edit' , [
			'domain' => $domain , 'allias' => $allias , 'block' => $block , 'php' => $php_content , 'file_data' => $data_content ,
			'meta' => [ 'title' => 'Редактор филлера' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Филлеры', 'url' => '?p=allias:fillers&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => $block, 'url' => ''] ] ]
		] );
		break;

	case 'filler:toggle' :
		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) { http_response_code( 405 ); exit; }
		$domain = (string) ( $_POST[ 'domain' ] ?? '' ); $allias = (string) ( $_POST[ 'allias' ] ?? '' ); $block = (string) ( $_POST[ 'block' ] ?? '' ); $value = (string) ( $_POST[ 'value' ] ?? '' );
		require_once CS_CMD . '/FillerToggle.php';
		$result = FillerToggle::run( [ 'domain' => $domain , 'allias' => $allias , 'block' => $block , 'value' => $value ] );
		AuditLog::action_ok( 'filler:toggle' , [ 'domain' => $domain , 'allias' => $allias , 'block' => $block , 'ok' => $result->ok ] );
		header( 'Location: ?p=allias:fillers&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) );
		exit;

	case 'filler:remove' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $allias = (string) ( $_GET[ 'allias' ] ?? '' ); $block = (string) ( $_GET[ 'block' ] ?? '' );
		require_once CS_CMD . '/FillerRemove.php';
		$result = FillerRemove::run( [ 'domain' => $domain , 'allias' => $allias , 'block' => $block ] );
		AuditLog::action_ok( 'filler:remove' , [ 'domain' => $domain , 'allias' => $allias , 'block' => $block , 'ok' => $result->ok ] );
		header( 'Location: ?p=allias:fillers&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) );
		exit;

	case 'block:new' :
		require_once CS_CMD . '/BlockNew.php';
		$domain = (string) ( $_GET[ 'domain' ] ?? $_POST[ 'domain' ] ?? '' );
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$result = BlockNew::run( [ 'name' => $_POST[ 'name' ] , 'domain' => $domain ] );
			View::render( 'result' , [ 'result' => $result , 'title' => 'Новый блок' , 'back_url' => '?p=blocks:list&domain=' . urlencode($domain) , 'back_label' => 'К блокам' ] );
		} else {
			View::render( 'block_new' , [ 'domain' => $domain , 'meta' => [ 'title' => 'Новый блок' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Блоки', 'url' => '?p=blocks:list&domain='.urlencode($domain)], ['label' => 'Новый блок', 'url' => ''] ] ] ] );
		}
		break;

	case 'block:edit' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); 
		$name   = (string) ( $_GET[ 'name' ] ?? '' );
		
		$php_file  = Paths::block( $domain , $name ) . '/' . $name . '.php'; 
		$data_file = Paths::block( $domain , $name ) . '/' . $name . '.data.php';
		
		if ( ! is_file( $php_file ) ) { 
			header( 'Location: ?p=blocks:list&domain=' . urlencode($domain) ); 
			exit; 
		}
		
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			Fs::writeAtomic( $php_file , $_POST[ 'php' ] ); 
			Fs::writeAtomic( $data_file , $_POST[ 'data' ] );
			header( 'Location: ?p=block:edit&domain=' . urlencode($domain) . '&name=' . urlencode($name) . '&saved=1' ); 
			exit;
		}
		
		$php_content = file_get_contents( $php_file );
		$data_content = is_file( $data_file ) ? file_get_contents( $data_file ) : "<?PHP\nreturn [\n\t\n];\n";

		View::render( 'block_edit' , [
			'domain' => $domain , 'name' => $name , 'php' => $php_content , 'file_data' => $data_content ,
			'meta' => [ 'title' => 'Редактор блока: '.$name , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Блоки', 'url' => '?p=blocks:list&domain='.urlencode($domain)], ['label' => $name, 'url' => ''] ] ]
		] );
		break;

	case 'block:remove' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $name = (string) ( $_GET[ 'name' ] ?? '' );
		require_once CS_CMD . '/BlockRemove.php';
		$result = BlockRemove::run( [ 'domain' => $domain , 'name' => $name ] );
		View::render( 'result' , [ 'result' => $result , 'title' => 'Удаление блока' , 'back_url' => '?p=blocks:list&domain=' . urlencode($domain) , 'back_label' => 'К блокам' ] );
		break;

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

	case 'blocks:list' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		if ( $domain === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		require_once ADMIN_ROOT . '/_lib/Scan.php';
		$blocks = Scan::blocksDetailsFor( $domain ); $layouts = Scan::layoutsFor( $domain ); $cfg = new Config( Paths::config() ); $alliases = array_values(array_unique((array)$cfg->getKeys(['alliases_real', Paths::real($domain)], [])));
		View::render( 'blocks_list' , [
			'domain' => $domain , 'blocks' => $blocks , 'layouts' => $layouts , 'alliases' => $alliases ,
			'meta' => [ 'title' => 'Блоки' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Блоки', 'url' => ''] ] , 'actions' => '<a href="?p=block:new&domain=' . urlencode($domain) . '" class="btn-primary">+ Создать блок</a>' , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'blocks'] ]
		] );
		break;

	case 'allias:edit' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); 
		$allias = (string) ( $_GET[ 'allias' ] ?? '' );
		if ( $domain === '' || $allias === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		
		$cfg  = new Config( Paths::config() ); 
		$real = Paths::real( $domain ); 
		
		$is_avail = ! empty( $cfg->getKeys( [ 'alliases_available' , $real , $allias ] ) );
		
		// Загружаем библиотеку заглушек
		$stubs_cfg = new Config( PROJECT_ROOT . '/config/site.stubs.config' );
		$stubs = $stubs_cfg->get( 'stubs' ) ?? [];
		$current_stub = $cfg->getKeys( [ 'alliases_stub' , $real , $allias , 'current' ] , 'maintenance' );

		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$avail = ! empty( $_POST[ 'available' ] );
			$stub_current = trim( (string) ( $_POST[ 'stub_current' ] ?? 'maintenance' ) );
			
			Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $allias , $avail , $stub_current ) { 
				$cfg->setKeys( [ 'alliases_available' , $real , $allias ] , $avail );
				$cfg->setKeys( [ 'alliases_stub' , $real , $allias , 'current' ] , $stub_current );
			} );
			
			AuditLog::action_ok( 'allias:edit' , [ 'domain' => $real , 'allias' => $allias , 'avail' => $avail , 'stub' => $stub_current ] );
			header( 'Location: ?p=allias:edit&domain=' . urlencode($domain) . '&allias=' . urlencode($allias) . '&saved=1' ); exit;
		}

		View::render( 'allias_edit' , [
			'domain' => $domain , 'allias' => $allias , 'avail' => $is_avail , 'saved' => ! empty($_GET['saved']) ,
			'stubs' => $stubs , 'current_stub' => $current_stub ,
			'meta' => [ 'title' => 'Настройки аллиаса' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'allias' => true, 'active_domain' => 'allias', 'active_allias' => 'main'] ]
		] );
		break;

	case 'allias:list' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		if ( $domain === '' ) { View::render( 'result' , [ 'result' => new Result( ) , 'title' => 'Домен не указан' , 'back_url' => '?p=dashboard' , 'back_label' => 'К списку домен' ] ); break; }
		$cfg = new Config( Paths::config() ); $real = $cfg->getKeys( [ 'domains_real' , $domain ] );
		if ( $real === null ) { View::render( 'result' , [ 'result' => new Result( ) , 'title' => "Домен не найден: {$domain}" , 'back_url' => '?p=dashboard' , 'back_label' => 'К списку домен' ] ); break; }
		$real = Paths::real( $domain ); $alliases = (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ); $available = (array) $cfg->getKeys( [ 'alliases_available' , $real ] , [ ] ); $fillers = (array) $cfg->getKeys( [ 'fillers' , $real ] , [ ] );
		$rows = [ ];
		foreach ( $alliases as $alias => $target ) {
			if ( $alias === '' ) continue; $fs_path = Paths::allias( $domain , $alias ); $f = (array) ( $fillers[ $alias ] ?? [ ] ); $enabled = 0; foreach ( $f as $v ) if ( $v ) $enabled++;
			$rows[ ] = [ 'name' => $alias , 'avail' => ! empty( $available[ $alias ] ) , 'fs' => is_dir( $fs_path ) , 'fillers_enabled' => $enabled , 'fillers_total' => count( $f ) ];
		}
		View::render( 'allias_list' , [
			'domain' => $domain , 'rows' => $rows ,
			'meta' => [ 'title' => 'Аллиасы' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Аллиасы', 'url' => ''] ] , 'actions' => '<a href="?p=allias:new&domain=' . urlencode($domain) . '" class="btn-primary">+ Добавить аллиас</a>' , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'allias'] ]
		] );
		break;

	case 'page:toggle' :
		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) { http_response_code( 405 ); exit( 'Method not allowed' ); }
		$domain = (string) ( $_POST[ 'domain' ] ?? '' ); $allias = (string) ( $_POST[ 'allias' ] ?? '' ); $path = trim( (string) ( $_POST[ 'path' ] ?? '' ) , '/' ); $key = (string) ( $_POST[ 'key' ] ?? '' ); $value = (string) ( $_POST[ 'value' ] ?? '' );
		if ( $domain === '' || $allias === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		require_once CS_CMD . '/PageToggle.php';
		$result = PageToggle::run( [ 'domain' => $domain , 'allias' => $allias , 'path' => $path , 'key' => $key , 'value' => $value ] );
		AuditLog::action_ok( 'page:toggle' , [ 'domain' => $domain , 'allias' => $allias , 'path' => $path , 'key' => $key , 'ok' => $result->ok ] );
		header( 'Location: ?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) );
		exit;

	case 'filler:new' :
		require_once ADMIN_ROOT . '/_lib/Scan.php';
		$domain = (string) ( $_GET[ 'domain' ] ?? $_POST[ 'domain' ] ?? '' ); $allias = (string) ( $_GET[ 'allias' ] ?? $_POST[ 'allias' ] ?? '' ); $block = (string) ( $_GET[ 'block' ] ?? '' );
		if ( $domain === '' ) { header( 'Location: ?p=dashboard' ); exit; }
		$cfg = new Config( Paths::config() ); $real = $cfg->getKeys( [ 'domains_real' , $domain ] );
		if ( $real === null ) { header( 'Location: ?p=dashboard' ); exit; }
		$alliases = [ ]; foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) { if ( $a === '' ) continue; $alliases[ ] = $a; }
		$blocks = Scan::blocksFor( $domain );
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			$allias = (string) ( $_POST[ 'allias' ] ?? '' ); $block = (string) ( $_POST[ 'block' ] ?? '' ); $enable = ! empty( $_POST[ 'enable' ] ); $result = new Result( );
			$block_path = Paths::block( $domain , $block ); $filler_php = $block_path . '/' . $block . '_' . $allias . '.php';
			if ( is_file( $filler_php ) ) { $result->step( 'Filler уже существует' , true , Fs::rel( $filler_php ) ); }
			else { require_once CS_CMD . '/FillerNew.php'; $result = FillerNew::run( [ 'name' => $allias , 'block' => $block , 'domain' => $domain , 'allias' => $allias ] ); }
			if ( $result->ok && $enable ) {
				try { Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $allias , $block ) { $cur = $cfg->getKeys( [ 'fillers' , $real , $allias , $block ] ); if ( $cur !== true ) { $cfg->setKeys( [ 'fillers' , $real , $allias , $block ] , true ); } } ); $result->step( 'Включено в site.config' , true , "fillers[{$real}][{$allias}][{$block}] = true" ); AuditLog::action_ok( 'filler:new' , [ 'domain' => $real , 'allias' => $allias , 'block' => $block ] ); }
				catch ( Throwable $e ) { $result->warn( 'Не удалось включить в конфиге: ' . $e->getMessage( ) ); }
			}
			View::render( 'result' , [ 'result' => $result , 'title' => "Filler: {$domain} / {$allias} / {$block}" , 'back_url' => '?p=allias:list&domain=' . urlencode( $domain ) , 'back_label' => 'К аллиасам' ] );
			break;
		}
		View::render( 'filler_new' , [ 'domain' => $domain , 'allias' => $allias , 'alliases' => $alliases , 'blocks' => $blocks , 'meta' => [ 'title' => 'Создать филлер' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => $allias, 'url' => '?p=allias:edit&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Филлеры', 'url' => '?p=allias:fillers&domain='.urlencode($domain).'&allias='.urlencode($allias)], ['label' => 'Новый', 'url' => ''] ] ] ] );
		break;

	case 'media:list' :
		$domain = (string) ( $_GET[ 'domain' ] ?? '' ); $real = Paths::real( $domain ); $media_dir = PROJECT_ROOT . '/www/media/' . $real; $files = [];
		if ( is_dir( $media_dir ) ) { $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $media_dir , FilesystemIterator::SKIP_DOTS ) ); foreach ( $it as $file ) { if ( $file->isFile() ) { $files[ ] = [ 'name' => substr( $file->getPathname() , strlen( $media_dir ) + 1 ) , 'size' => $file->getSize() , 'time' => $file->getMTime() ]; } } }
		usort($files, fn($a, $b) => strcmp($a['name'], $b['name']));
		View::render( 'media_list' , [
			'domain' => $domain , 'files' => $files ,
			'meta' => [ 'title' => 'Медиа' , 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => $domain, 'url' => '?p=domain:edit&domain='.urlencode($domain)], ['label' => 'Медиа', 'url' => ''] ] , 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'media'] ]
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
	
	case 'settings' :
		require_once ADMIN_ROOT . '/_lib/Scan.php';
		$allowed = [ 'logic' , 'connect_type' , 'ui_ajax_forms' ];
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/SettingsSet.php'; $params = [ ]; foreach ( $allowed as $k ) { if ( array_key_exists( $k , $_POST ) ) { $params[ $k ] = $_POST[ $k ]; } }
			$result = SettingsSet::run( $params ); AuditLog::action_ok( 'settings:set' , [ 'params' => $params , 'ok' => $result->ok ] );
			if ( $result->ok ) { header( 'Location: ?p=settings&saved=1' ); exit; }
			View::render( 'result' , [ 'result' => $result , 'title' => 'Настройки — ошибка' , 'back_url' => '?p=settings' , 'back_label' => 'К настройкам' ] ); break;
		}
		
		// Читаем настройки движка из engine.config, а структуру доменов из site.config
		$engine_cfg = new Config( Paths::engine() );
		$site_cfg   = new Config( Paths::config() );

		View::render( 'settings' , [
			'meta' => [ 'title' => 'Настройки' ] , 'title' => 'Настройки' , 'saved' => ! empty( $_GET[ 'saved' ] ) ,
			'current' => [ 
				'logic'        => (string) ( $engine_cfg->get( 'logic' ) ?? '' ) , 
				'ui_ajax_forms'=> ! empty( $engine_cfg->get( 'ui_ajax_forms' ) ) , 
				'connect_type' => (string) ( $engine_cfg->get( 'connect_type' ) ?? 'https://' ) 
			] ,
			'skins' => Scan::skinsFor( array_key_first((array)$site_cfg->get('domains_real', [])) ?? 'damoony.ru' ) , 'logics' => Scan::logics( ) , 'connectTypes' => Scan::connectTypes( )
		] );
		break;

	default :
		http_response_code( 404 );
		echo '<h1>404</h1><p>Страница не найдена: ' . htmlspecialchars( $p ) . '</p>';
}
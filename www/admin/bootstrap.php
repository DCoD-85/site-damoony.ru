<?PHP

# ============================================================
#   www/admin/index.php — точка входа админки
#   Изолирована от main.php / visuo. Подключает только
#   corescripts для доступа к commands и _lib.
# ============================================================

$admin_cfg = @json_decode( @file_get_contents( PROJECT_ROOT . '/config/admin.config' ) , true ) ?: [ ];
$admin_session_name = (string) ( $admin_cfg[ 'session_name' ] ?? 'admin_sid' );
session_name( $admin_session_name );

session_set_cookie_params( [
	'lifetime' => 0 ,
	'path'     => '/' ,
	'domain'   => '' ,
	'secure'   => ( ( $_SERVER[ 'HTTPS' ] ?? '' ) === 'on' ) ,
	'httponly' => true ,
	'samesite' => 'Lax' ,
] );
session_start( );

define( 'ADMIN_ROOT'   , __DIR__ );
define( 'PROJECT_ROOT' , dirname( dirname( ADMIN_ROOT ) ) );

# --- security headers ---
header( 'X-Content-Type-Options: nosniff' );
header( 'X-Frame-Options: DENY' );
header( 'Referrer-Policy: same-origin' );
header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );

if ( ( $_SERVER[ 'HTTPS' ] ?? '' ) === 'on' ) {
	header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
}

header(
	"Content-Security-Policy: default-src 'self'; "
	. "script-src 'self' 'unsafe-inline'; "
	. "style-src 'self' 'unsafe-inline'; "
	. "img-src 'self' data:; "
	. "font-src 'self' data:; "
	. "connect-src 'self'; "
	. "frame-ancestors 'none'"
);

$cs = PROJECT_ROOT . '/corescripts';

define( 'CS_ROOT' , $cs );
define( 'CS_LIB'  , $cs . '/_lib' );
define( 'CS_CMD'  , $cs . '/commands' );
define( 'CS_TPL'  , $cs . '/templates' );

# corescripts _lib
require_once CS_LIB . '/Fs.php';
require_once CS_LIB . '/Json.php';
require_once CS_LIB . '/Paths.php';
require_once CS_LIB . '/Blocks.php';
require_once CS_LIB . '/Config.php';
require_once CS_LIB . '/Template.php';
require_once CS_LIB . '/Validate.php';
require_once CS_LIB . '/Result.php';
require_once CS_LIB . '/Context.php';

# admin _lib
require_once ADMIN_ROOT . '/_lib/Auth.php';
require_once ADMIN_ROOT . '/_lib/View.php';
require_once ADMIN_ROOT . '/_lib/WebRenderer.php';
require_once ADMIN_ROOT . '/_lib/Csrf.php';
require_once ADMIN_ROOT . '/_lib/AuditLog.php';

$auth = new Auth( );

$p = (string) ( $_GET[ 'p' ] ?? 'dashboard' );

# rate limit на login
if ( $p === 'login' && $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
	require_once PROJECT_ROOT . '/engine/middleware.php';
	middleware::rateLimit( 'admin:login' , 10 , 60 , 1800 );
}

# --- login / logout ---
if ( $p === 'login' ) {

	# уже авторизован → сразу на dashboard
	if ( $auth->check( ) ) {
		header( 'Location: ?p=dashboard' );
		exit;
	}

	$error = null;

	if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

		# CSRF-защита (до обработки логина)
		if ( ! Csrf::check( ) ) {
			http_response_code( 403 );
			exit( 'CSRF token invalid' );
		}

		$login = (string) ( $_POST[ 'login' ]    ?? '' );
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

# --- защита ---
if ( ! $auth->check( ) ) {
	header( 'Location: ?p=login' );
	exit;
}

# --- CSRF guard для всех POST (кроме login) ---
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' && ! Csrf::check( ) ) {
	http_response_code( 403 );
	exit( 'CSRF token invalid' );
}

# --- dispatch ---
switch ( $p ) {

	case 'dashboard' :
		$cfg     = new Config( PROJECT_ROOT . '/config/site.config' );
		$domains = (array) $cfg->get( 'domains_real' );

		# Собрать real-домены и их алиасы
		$real_domains = [ ];
		$aliases      = [ ];

		foreach ( $domains as $d => $real ) {
			if ( $d === $real ) {
				$real_domains[ $d ] = true;
			} else {
				$aliases[ $real ][ ] = $d;
			}
		}

		$rows = [ ];
		foreach ( array_keys( $real_domains ) as $real ) {
			$alliases = [ ];
			foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) {
				if ( $a === '' ) continue;
				$alliases[ ] = $a;
			}

			$rows[ ] = [
				'domain'   => $real ,
				'ns'       => $cfg->getKeys( [ 'domains_namespace' , $real ] ) ?? '—' ,
				'avail'    => ! empty( $cfg->getKeys( [ 'domains_available' , $real ] ) ) ,
				'fs'       => is_dir( Paths::domain( $real ) ) ,
				'alliases' => $alliases ,
				'aliases'  => $aliases[ $real ] ?? [ ] ,
			];
		}

		View::render( 'dashboard' , [ 'domains' => $rows ] );
		break;
	
	case 'domain:new' :
	
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/DomainNew.php';
			$result = DomainNew::run( [
				'name'     => $_POST[ 'domain' ] ?? '' ,
				'register' => ! empty( $_POST[ 'register' ] ) ,
			] );
			AuditLog::action_ok( 'domain:new' , [
				'domain'   => $_POST[ 'domain' ] ?? '' ,
				'register' => ! empty( $_POST[ 'register' ] ) ,
				'ok'       => $result->ok ,
			] );
			View::render( 'result' , [
				'result'     => $result ,
				'title'      => 'Создание домена' ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
		} else {
			View::render( 'domain_new' , [ ] );
		}
		break;

	case 'allias:new' :

		$cfg         = new Config( Paths::config() );
		$domains_raw = (array) $cfg->get( 'domains_real' );

		# только real-домены (key === value): аллиасы принадлежат real
		$domains = [ ];
		foreach ( $domains_raw as $d => $real ) {
			if ( $d === $real ) $domains[ ] = $d;
		}

		$prefill_domain = (string) ( $_GET[ 'domain' ] ?? '' );

		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/AlliasNew.php';
			$post_domain = (string) ( $_POST[ 'domain' ] ?? '' );
			$result = AlliasNew::run( [
				'name'     => $_POST[ 'name' ]   ?? '' ,
				'domain'   => $post_domain ,
				'register' => ! empty( $_POST[ 'register' ] ) ,
			] );
			AuditLog::action_ok( 'allias:new' , [
				'domain' => $post_domain ,
				'name'   => $_POST[ 'name' ] ?? '' ,
				'ok'     => $result->ok ,
			] );
			View::render( 'result' , [
				'result'     => $result ,
				'title'      => 'Создание аллиаса' ,
				'back_url'   => '?p=allias:list&domain=' . urlencode( $post_domain ) ,
				'back_label' => 'К аллиасам' ,
			] );
		} else {
			View::render( 'allias_new' , [
				'domains'        => $domains ,
				'prefill_domain' => $prefill_domain ,
			] );
		}
		break;

	case 'domain:edit' :

		require_once ADMIN_ROOT . '/_lib/Scan.php';

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );

		if ( Validate::domain( $domain ) !== null ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => 'Некорректный домен' ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
			break;
		}

		$cfg  = new Config( Paths::config() );
		$real = Paths::real( $domain );

		# --- валидация домена ---
		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => 'Домен не найден' ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
			break;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$title = trim( (string) ( $_POST[ 'title' ] ?? '' ) );
			$skin  = trim( (string) ( $_POST[ 'skin' ]  ?? '' ) );

			$errors = [ ];

			if ( $title === '' ) {
				$errors[ ] = 'Title не может быть пустым';
			}

			# skin: валидация имени + проверка папки
			if ( $skin !== '' ) {
				$skin_err = Validate::skin( $skin );
				if ( $skin_err !== null ) {
					$errors[ ] = $skin_err;
				} else {
					$skin_dir = Paths::skins( $domain ) . '/' . $skin;
					if ( ! is_dir( $skin_dir ) ) {
						$errors[ ] = "Тема '{$skin}' не найдена в media/" . Paths::real( $domain ) . "/skin/";
					}
				}
			}

			if ( empty( $errors ) ) {
				try {
					Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $title , $skin ) {
						$cfg->setKeys( [ 'domains_titles' , $real ] , $title );
						if ( $skin !== '' ) {
							$cfg->setKeys( [ 'domains_skins' , $real ] , $skin );
						}
					} );
					AuditLog::action_ok( 'domain:edit' , [ 'domain' => $real , 'title' => $title , 'skin' => $skin ] );
					header( 'Location: ?p=domain:edit&domain=' . urlencode( $domain ) . '&saved=1' );
					exit;
				} catch ( Throwable $e ) {
					$errors[ ] = 'Не сохранить: ' . $e->getMessage( );
				}
			}

			# ошибки — показать форму заново с введённым
			View::render( 'domain_edit' , [
				'domain'  => $domain ,
				'saved'   => false ,
				'errors'  => $errors ,
				'current' => [ 'title' => $title , 'skin' => $skin ] ,
				'skins'   => Scan::skinsFor( $domain ) ,
			] );
			break;
		}

		# --- GET ---
		$title = (string) ( $cfg->getKeys( [ 'domains_titles' , $real ] ) ?? $cfg->get( 'title' , '' ) );
		$skin  = (string) ( $cfg->getKeys( [ 'domains_skins'  , $real ] ) ?? $cfg->get( 'skin'  , 'default' ) );

		View::render( 'domain_edit' , [
			'domain'  => $domain ,
			'saved'   => ! empty( $_GET[ 'saved' ] ) ,
			'errors'  => [ ] ,
			'current' => [ 'title' => $title , 'skin' => $skin ] ,
			'skins'   => Scan::skinsFor( $domain ) ,
		] );
		break;

	case 'allias:toggle' :

		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
			http_response_code( 405 );
			exit( 'Method not allowed' );
		}

		$domain = (string) ( $_POST[ 'domain' ] ?? '' );
		$name   = (string) ( $_POST[ 'name' ]   ?? '' );
		$value  = (string) ( $_POST[ 'value' ]  ?? '' );

		if ( $domain === '' || $name === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		require_once CS_CMD . '/AlliasToggle.php';

		$result = AlliasToggle::run( [
			'domain' => $domain ,
			'name'   => $name ,
			'value'  => $value ,
		] );
		AuditLog::action_ok( 'allias:toggle' , [ 'domain' => $domain , 'name' => $name , 'value' => $value , 'ok' => $result->ok ] );

		header( 'Location: ?p=allias:list&domain=' . urlencode( $domain ) );
		exit;

	case 'domain:toggle' :

		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
			http_response_code( 405 );
			exit( 'Method not allowed' );
		}

		$domain = (string) ( $_POST[ 'domain' ] ?? '' );
		$value  = (string) ( $_POST[ 'value' ]  ?? '' );

		if ( $domain === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		require_once CS_CMD . '/DomainToggle.php';

		$result = DomainToggle::run( [
			'domain' => $domain ,
			'value'  => $value ,
		] );
		AuditLog::action_ok( 'domain:toggle' , [ 'domain' => $domain , 'value' => $value , 'ok' => $result->ok ] );

		header( 'Location: ?p=dashboard' );
		exit;

	case 'domain:remove' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );

		if ( $domain === '' || Validate::domain( $domain ) !== null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg = new Config( Paths::config() );

		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$real = Paths::real( $domain );

		# список аллиасов real-домена
		$alliases = [ ];
		foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) {
			if ( $a === '' ) continue;
			$alliases[ ] = $a;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$confirm_name    = trim( (string) ( $_POST[ 'confirm_name' ]    ?? '' ) );
			$confirm_checked = ! empty( $_POST[ 'confirm_checked' ] );

			$errors = [ ];

			if ( ! $confirm_checked ) {
				$errors[ ] = 'Подтвердите, что понимаете необратимость';
			}
			if ( $confirm_name !== $real ) {
				$errors[ ] = "Имя real-домена введено неверно (нужно: {$real})";
			}

			if ( empty( $errors ) ) {
				require_once CS_CMD . '/DomainRemove.php';

				$result = DomainRemove::run( [
					'name'  => $real ,
					'force' => true ,
				] );

				AuditLog::action_ok( 'domain:remove' , [
					'domain' => $real ,
					'ok'     => $result->ok ,
				] );

				View::render( 'result' , [
					'result'     => $result ,
					'title'      => 'Удаление домена: ' . $real ,
					'back_url'   => '?p=dashboard' ,
					'back_label' => 'К списку домен' ,
				] );
				break;
			}

			View::render( 'domain_remove' , [
				'domain'   => $domain ,
				'real'     => $real ,
				'errors'   => $errors ,
				'alliases' => $alliases ,
			] );
			break;
		}

		# --- GET ---
		View::render( 'domain_remove' , [
			'domain'   => $domain ,
			'real'     => $real ,
			'errors'   => [ ] ,
			'alliases' => $alliases ,
		] );
		break;

	case 'allias:remove' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$name   = (string) ( $_GET[ 'name' ]   ?? '' );

		if ( $domain === '' || $name === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		if ( Validate::domain( $domain ) !== null || Validate::allias_name( $name ) !== null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( Paths::config() );
		$real = Paths::real( $domain );

		$al = $cfg->getKeys( [ 'alliases_real' , $real , $name ] );

		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null || $al === null ) {
			header( 'Location: ?p=allias:list&domain=' . urlencode( $domain ) );
			exit;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$confirm_checked = ! empty( $_POST[ 'confirm_checked' ] );
			$confirm_name    = trim( (string) ( $_POST[ 'confirm_name' ] ?? '' ) );

			$errors = [ ];

			if ( ! $confirm_checked ) {
				$errors[ ] = 'Подтвердите, что понимаете необратимость';
			}
			if ( $confirm_name !== $name ) {
				$errors[ ] = 'Имя аллиаса введено неверно';
			}

			if ( empty( $errors ) ) {
				require_once CS_CMD . '/AlliasRemove.php';

				$result = AlliasRemove::run( [
					'name'   => $name ,
					'domain' => $real ,
					'force'  => true ,
				] );

				AuditLog::action_ok( 'allias:remove' , [
					'domain' => $real ,
					'name'   => $name ,
					'ok'     => $result->ok ,
				] );

				View::render( 'result' , [
					'result'     => $result ,
					'title'      => "Удаление аллиаса: {$real} / {$name}" ,
					'back_url'   => '?p=allias:list&domain=' . urlencode( $domain ) ,
					'back_label' => 'К аллиасам' ,
				] );
				break;
			}

			View::render( 'allias_remove' , [
				'domain' => $domain ,
				'name'   => $name ,
				'errors' => $errors ,
			] );
			break;
		}

		# --- GET ---
		View::render( 'allias_remove' , [
			'domain' => $domain ,
			'name'   => $name ,
			'errors' => [ ] ,
		] );
		break;

	case 'pages:tree' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$allias = (string) ( $_GET[ 'allias' ] ?? '' );

		if ( $domain === '' || $allias === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		if ( Validate::domain( $domain ) !== null || Validate::allias_name( $allias ) !== null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg = new Config( Paths::config() );

		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		require_once ADMIN_ROOT . '/_lib/Scan.php';

		$base = Paths::algoritm( $domain , $allias );
		$tree = is_dir( $base ) ? Scan::pageTree( $base ) : [ ];

		View::render( 'pages_tree' , [
			'domain'  => $domain ,
			'allias'  => $allias ,
			'logic'   => Paths::logic( ) ,
			'tree'    => $tree ,
			'has_dir' => is_dir( $base ) ,
		] );
		break;

	case 'page:edit' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$allias = (string) ( $_GET[ 'allias' ] ?? '' );
		$path   = trim( (string) ( $_GET[ 'path' ] ?? '' ) , '/' );

		$path_err = Validate::page_path( $path );
		if ( $path_err !== null ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => 'Некорректный путь: ' . $path_err ,
				'back_url'   => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) ,
				'back_label' => 'К страницам' ,
			] );
			break;
		}

		if ( $domain === '' || $allias === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( Paths::config() );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$base = Paths::algoritm( $domain , $allias );

		# --- вычисляем файлы: path относительно algoritm/ ---
		$cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/' , '_' , $path ) );
		$sub_dir   = $path === '' ? $base : $base . '/' . $path;
		$php_file  = $sub_dir . '/' . $cls_short . '.php';
		$json_file = $sub_dir . '/' . $cls_short . '.data.json';

		if ( ! is_file( $php_file ) ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => "Страница не найдена: {$path}" ,
				'back_url'   => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) ,
				'back_label' => 'К страницам' ,
			] );
			break;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$available_raw = (string) ( $_POST[ 'available' ] ?? '' );
			$auth          = ! empty( $_POST[ 'auth' ] );
			$search        = ! empty( $_POST[ 'search' ] );
			$default       = ! empty( $_POST[ 'default' ] );

			# разбить на строки + trim + дедупликация
			$lines     = preg_split( '/\r\n|\n|\r/' , $available_raw );
			$available = [ ];
			$seen      = [ ];

			foreach ( $lines as $line ) {
				$line = trim( $line );

				if ( isset( $seen[ $line ] ) ) continue;
				$seen[ $line ] = true;

				$available[ ] = $line;
			}

			# убрать только trailing пустые (артефакт textarea)
			while ( ! empty( $available ) && end( $available ) === '' ) {
				array_pop( $available );
			}

			# если после чистки вообще ничего нет — оставить ['']
			if ( empty( $available ) ) {
				$available = [ '' ];
			}

			# валидация только при search=true (иначе regex не используется в роутере)
			if ( $search ) {
				$bad = [ ];
				foreach ( $available as $line ) {
					if ( $line === '' ) continue;

					if ( strlen( $line ) > 64 ) {
						$bad[ ] = $line . ' — длина > 64';
						continue;
					}

					if ( preg_match( '/^[\.\*\+\?]+$/' , $line ) ) {
						$bad[ ] = $line . ' — catch-all regex';
						continue;
					}

					if ( preg_match( '/[#\s\/]/' , $line ) ) {
						$bad[ ] = $line . ' — запрещённый символ';
						continue;
					}
				}

				if ( ! empty( $bad ) ) {
					# вернуть форму с ошибкой, чтобы пользователь мог исправить
					View::render( 'page_edit' , [
						'domain'    => $domain ,
						'allias'    => $allias ,
						'path'      => $path ,
						'cls_short' => $cls_short ,
						'json_file' => $json_file ,
						'saved'     => false ,
						'errors'    => array_merge( [ 'Недопустимые правила available:' ] , $bad ) ,
						'current'   => [
							'available' => $available ,
							'auth'      => $auth ,
							'search'    => $search ,
							'default'   => $default ,
						] ,
					] );
					break;
				}
			}

			$data = [
				'default' => [
					'available' => $available ,
					'auth'      => $auth ,
					'search'    => $search ,
					'default'   => $default ,
				] ,
			];

			try {
				Json::writeAtomic( $json_file , $data );
				AuditLog::action_ok( 'page:edit' , [ 'domain' => $domain , 'allias' => $allias , 'path' => $path ] );
			} catch ( Throwable $e ) {
				View::render( 'result' , [
					'result'     => new Result( ) ,
					'title'      => 'Ошибка записи: ' . $e->getMessage( ) ,
					'back_url'   => '?p=page:edit&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) . '&path=' . urlencode( $path ) ,
					'back_label' => 'Назад' ,
				] );
				break;
			}

			header( 'Location: ?p=page:edit&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) . '&path=' . urlencode( $path ) . '&saved=1' );
			exit;
		}

		# --- GET ---
		$current = [
			'available' => [ ] ,
			'auth'      => false ,
			'search'    => true ,
			'default'   => false ,
		];

		$json = Json::read( $json_file );
		if ( is_array( $json ) && isset( $json[ 'default' ] ) && is_array( $json[ 'default' ] ) ) {
			$d = $json[ 'default' ];
			$current[ 'available' ] = isset( $d[ 'available' ] ) && is_array( $d[ 'available' ] ) ? $d[ 'available' ] : [ ];
			$current[ 'auth' ]      = ! empty( $d[ 'auth' ] );
			$current[ 'search' ]    = ! isset( $d[ 'search' ] ) || ! empty( $d[ 'search' ] );
			$current[ 'default' ]   = ! empty( $d[ 'default' ] );
		}

		View::render( 'page_edit' , [
			'domain'    => $domain ,
			'allias'    => $allias ,
			'path'      => $path ,
			'cls_short' => $cls_short ,
			'json_file' => $json_file ,
			'current'   => $current ,
			'saved'     => ! empty( $_GET[ 'saved' ] ) ,
		] );
		break;
	
	case 'page:new' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$allias = (string) ( $_GET[ 'allias' ] ?? '' );

		if ( $domain === '' || Validate::domain( $domain ) !== null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg = new Config( Paths::config() );

		if ( $cfg->getKeys( [ 'domains_real' , $domain ] ) === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$real = Paths::real( $domain );

		# список аллиасов real-домена
		$alliases = [ ];
		foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) {
			if ( $a === '' ) continue;
			$alliases[ ] = $a;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$allias = (string) ( $_POST[ 'allias' ] ?? '' );
			$name   = trim( (string) ( $_POST[ 'name' ] ?? '' ) );
			$parent   = trim( (string) ( $_POST[ 'parent' ] ?? '' ) , '/' );
			$path_err = Validate::page_path( $parent );
			if ( $path_err !== null ) {
				View::render( 'page_new' , [
					'domain'   => $domain ,
					'allias'   => $allias ,
					'parent'   => $parent ,
					'alliases' => $alliases ,
					'error'    => $path_err ,
				] );
				break;
			}

			require_once CS_CMD . '/PageNew.php';

			$result = PageNew::run( [
				'name'   => $name ,
				'parent' => $parent ,
				'domain' => $domain ,
				'allias' => $allias ,
			] );
			AuditLog::action_ok( 'page:new' , [ 'domain' => $domain , 'allias' => $allias , 'name' => $name , 'ok' => $result->ok ] );

			View::render( 'result' , [
				'result'     => $result ,
				'title'      => "Создание страницы: {$domain} / {$allias} / {$name}" ,
				'back_url'   => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) ,
				'back_label' => 'К страницам' ,
			] );
			break;
		}

		# --- GET ---
		View::render( 'page_new' , [
			'domain'   => $domain ,
			'allias'   => $allias ,
			'parent'   => (string) ( $_GET[ 'parent' ] ?? '' ) ,
			'alliases' => $alliases ,
		] );
		break;

	case 'allias:list' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );

		if ( $domain === '' ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => 'Домен не указан' ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
			break;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => "Домен не найден: {$domain}" ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
			break;
		}

		$real      = Paths::real( $domain );
		$alliases  = (array)  $cfg->getKeys( [ 'alliases_real'      , $real ] , [ ] );
		$available = (array)  $cfg->getKeys( [ 'alliases_available' , $real ] , [ ] );
		$fillers   = (array)  $cfg->getKeys( [ 'fillers'            , $real ] , [ ] );

		$rows = [ ];
		foreach ( $alliases as $alias => $target ) {
			if ( $alias === '' ) continue;

			$fs_path = Paths::allias( $domain , $alias );
			$f       = (array) ( $fillers[ $alias ] ?? [ ] );

			$enabled = 0;
			foreach ( $f as $v ) if ( $v ) $enabled++;

			$rows[ ] = [
				'name'            => $alias ,
				'avail'           => ! empty( $available[ $alias ] ) ,
				'fs'              => is_dir( $fs_path ) ,
				'fillers_enabled' => $enabled ,
				'fillers_total'   => count( $f ) ,
			];
		}

		View::render( 'allias_list' , [
			'domain' => $domain ,
			'rows'   => $rows ,
		] );
		break;

	case 'page:toggle' :

		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
			http_response_code( 405 );
			exit( 'Method not allowed' );
		}

		$domain = (string) ( $_POST[ 'domain' ] ?? '' );
		$allias = (string) ( $_POST[ 'allias' ] ?? '' );
		$path   = trim( (string) ( $_POST[ 'path' ] ?? '' ) , '/' );
		$key    = (string) ( $_POST[ 'key' ]    ?? '' );
		$value  = (string) ( $_POST[ 'value' ]  ?? '' );

		if ( $domain === '' || $allias === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		require_once CS_CMD . '/PageToggle.php';

		$result = PageToggle::run( [
			'domain' => $domain ,
			'allias' => $allias ,
			'path'   => $path ,
			'key'    => $key ,
			'value'  => $value ,
		] );
		AuditLog::action_ok( 'page:toggle' , [ 'domain' => $domain , 'allias' => $allias , 'path' => $path , 'key' => $key , 'ok' => $result->ok ] );

		header( 'Location: ?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) );
		exit;

	case 'filler:new' :

		require_once ADMIN_ROOT . '/_lib/Scan.php';

		$domain = (string) ( $_GET[ 'domain' ] ?? $_POST[ 'domain' ] ?? '' );
		$allias = (string) ( $_GET[ 'allias' ] ?? $_POST[ 'allias' ] ?? '' );

		if ( $domain === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( Paths::config() );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		# список аллиасов real-домена
		$alliases = [ ];
		foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) {
			if ( $a === '' ) continue;
			$alliases[ ] = $a;
		}

		$blocks = Scan::blocksFor( $domain );

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$allias = (string) ( $_POST[ 'allias' ] ?? '' );
			$block  = (string) ( $_POST[ 'block' ]  ?? '' );
			$enable = ! empty( $_POST[ 'enable' ] );

			$result = new Result( );

			# путь к будущему filler'у
			$block_path = Paths::block( $domain , $block );
			$filler_php = $block_path . '/' . $block . '_' . $allias . '.php';

			if ( is_file( $filler_php ) ) {
				# уже есть — просто отметить
				$result->step( 'Filler уже существует' , true , Fs::rel( $filler_php ) );
			} else {
				require_once CS_CMD . '/FillerNew.php';
				$result = FillerNew::run( [
					'name'   => $allias ,
					'block'  => $block ,
					'domain' => $domain ,
					'allias' => $allias ,
				] );
			}

			# включить в конфиге, если попросили (по real)
			if ( $result->ok && $enable ) {
				try {
					Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $allias , $block ) {
						$cur = $cfg->getKeys( [ 'fillers' , $real , $allias , $block ] );
						if ( $cur !== true ) {
							$cfg->setKeys( [ 'fillers' , $real , $allias , $block ] , true );
						}
					} );
					$result->step( 'Включено в site.config' , true , "fillers[{$real}][{$allias}][{$block}] = true" );
					AuditLog::action_ok( 'filler:new' , [ 'domain' => $real , 'allias' => $allias , 'block' => $block ] );
				} catch ( Throwable $e ) {
					$result->warn( 'Не удалось включить в конфиге: ' . $e->getMessage( ) );
				}
			}

			View::render( 'result' , [
				'result'     => $result ,
				'title'      => "Filler: {$domain} / {$allias} / {$block}" ,
				'back_url'   => '?p=allias:list&domain=' . urlencode( $domain ) ,
				'back_label' => 'К аллиасам' ,
			] );
			break;
		}

		# --- GET ---
		View::render( 'filler_new' , [
			'domain'   => $domain ,
			'allias'   => $allias ,
			'alliases' => $alliases ,
			'blocks'   => $blocks ,
		] );
		break;
	
	case 'settings' :

		require_once ADMIN_ROOT . '/_lib/Scan.php';

		# SettingsSet падает на неизвестном ключе → фильтруем $_POST
		$allowed = [ 'logic' , 'connect_type' ];

		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/SettingsSet.php';

			$params = [ ];
			foreach ( $allowed as $k ) {
				if ( array_key_exists( $k , $_POST ) ) {
					$params[ $k ] = $_POST[ $k ];
				}
			}

			$result = SettingsSet::run( $params );
			AuditLog::action_ok( 'settings:set' , [ 'params' => $params , 'ok' => $result->ok ] );

			if ( $result->ok ) {
				header( 'Location: ?p=settings&saved=1' );
				exit;
			}

			View::render( 'result' , [
				'result'     => $result ,
				'title'      => 'Настройки — ошибка' ,
				'back_url'   => '?p=settings' ,
				'back_label' => 'К настройкам' ,
			] );
			break;
		}

		# GET
		$cfg = new Config( PROJECT_ROOT . '/config/site.config' );

		View::render( 'settings' , [
			'title'        => 'Настройки' ,
			'saved'        => ! empty( $_GET[ 'saved' ] ) ,
			'current'      => [
				'logic'        => (string) ( $cfg->get( 'logic' )        ?? '' ) ,
				'connect_type' => (string) ( $cfg->get( 'connect_type' ) ?? 'https://' ) ,
			] ,
			'logics'       => Scan::logics( ) ,
			'connectTypes' => Scan::connectTypes( ) ,
		] );
		break;

	default :
		http_response_code( 404 );
		echo '<h1>404</h1><p>Страница не найдена: ' . htmlspecialchars( $p ) . '</p>';
}
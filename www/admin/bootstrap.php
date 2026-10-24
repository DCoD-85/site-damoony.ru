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
require_once CS_LIB . '/DataGateway.php';
require_once CS_LIB . '/ResourceManager.php';


require_once ADMIN_ROOT . '/_lib/Auth.php';
require_once ADMIN_ROOT . '/_lib/Security.php';
require_once ADMIN_ROOT . '/_lib/View.php';
require_once ADMIN_ROOT . '/_lib/WebRenderer.php';
require_once ADMIN_ROOT . '/_lib/Csrf.php';
require_once ADMIN_ROOT . '/_lib/AuditLog.php';

$auth = new Auth( );
$p = (string) ( $_GET[ 'p' ] ?? 'dashboard' );

# --- Автовход по fingerprint-cookie ---
if ( empty( $_SESSION[ 'admin_ok' ] ) ) {
	$fp_raw = $_COOKIE[ 'admin_fp' ] ?? '';
	if ( $fp_raw !== '' ) {
		$match = Security::find_device( json_decode( $fp_raw , true ) ?: [] );
		if ( $match !== null ) {
			session_regenerate_id( true );
			$_SESSION[ 'admin_ok' ]    = true;
			$_SESSION[ 'admin_login' ] = $match[ 'login' ];
			$_SESSION[ 'admin_role' ]  = $match[ 'role' ];
			Security::touch_device( $match[ 'login' ] , $match[ 'device' ] , (string) ( $_SERVER[ 'REMOTE_ADDR' ] ?? '' ) );
			Security::log( 'autologin:ok' , [ 'login' => $match[ 'login' ] , 'device' => $match[ 'device' ] ] );
		}
	}
}

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

# --- Приглашение: регистрация или привязка устройства ---
if ( $p === 'invite' || ( isset( $_GET[ 'token' ] ) && (string) $_GET[ 'token' ] !== '' ) ) {
	$token = (string) ( $_GET[ 'token' ] ?? '' );
	$invite = $token !== '' ? Security::validate_invite( $token ) : null;
	
	if ( $invite === null ) { http_response_code( 404 ); exit( 'Приглашение недействительно или истекло' ); }

	$is_bind = ( $invite['role'] === 'bind_only' );
	$error = null;

	if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
		if ( ! Csrf::check() ) { http_response_code( 403 ); exit( 'CSRF token invalid' ); }
		$pass = (string) ( $_POST[ 'password' ] ?? '' );
		$fp = json_decode( (string) ( $_POST[ 'fingerprint' ] ?? '' ) , true ) ?: [];
		$ua = (string) ( $_SERVER[ 'HTTP_USER_AGENT' ] ?? '' );
		$ip = (string) ( $_SERVER[ 'REMOTE_ADDR' ] ?? '' );

		if ( empty( $fp['canvas'] ) ) {
			$error = 'Не удалось получить отпечаток устройства. Включите JavaScript.';
		} elseif ( $is_bind ) {
			if ( Security::bind_device_from_invite( $token , $pass , $fp , $ua , $ip ) ) {
				$admins = Security::get_data();
				$_SESSION['admin_ok'] = true;
				$_SESSION['admin_login'] = $invite['target_user'];
				$_SESSION['admin_role'] = $admins['users'][$invite['target_user']]['role'] ?? 'admin';
				Security::log( 'device:bound_invite' , [ 'login' => $invite['target_user'] ] );
				header('Location: ?p=dashboard'); exit;
			}
			$error = 'Неверный пароль';
		} else {
			$login = trim((string)($_POST['login'] ?? ''));
			if ( $login === '' || !preg_match('/^[a-z0-9_]+$/i', $login) ) {
				$error = 'Некорректный логин (только a-z, 0-9, _)';
			} elseif ( Security::create_user_from_invite( $token , $login , $pass , $invite['role'] , $fp , $ua , $ip ) ) {
				$_SESSION['admin_ok'] = true; $_SESSION['admin_login'] = $login; $_SESSION['admin_role'] = $invite['role'];
				Security::log( 'user:created_invite' , [ 'login' => $login ] );
				header('Location: ?p=dashboard'); exit;
			} else {
				$error = 'Логин уже существует или ошибка сохранения';
			}
		}
	}

	View::render( 'invite_accept' , [ 'token' => $token , 'invite' => $invite , 'is_bind' => $is_bind , 'error' => $error ] );
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
if ( ! AdminRouter::dispatch( $p ) ) {
    http_response_code( 404 );
    View::render( 'result', [
        'result' => new Result(),
        'title' => '404 — Страница не найдена',
        'back_url' => '?p=dashboard',
        'back_label' => 'На главную'
    ] );
    exit;
}
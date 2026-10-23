<?PHP

# 0: Стартуем скрипт
$start    = microtime( true );
$start_hr = hrtime( true );

# для debug_attach() в visuorelease и visuolog_row
$GLOBALS[ 'start' ]    = $start;
$GLOBALS[ 'start_hr' ] = $start_hr;

# --- РОУТИНГ /admin/ — до session_start, до header ---
$path = trim( parse_url( $_SERVER[ 'REQUEST_URI' ] ?? '/' , PHP_URL_PATH ) ?? '/' , '/' );

# Отрезаем языковой префикс (ru-ru, en-en и т.д.), чтобы админка открывалась по любому пути
$path_parts = explode( '/' , $path );
if ( isset( $path_parts[ 0 ] ) && preg_match( '/^[a-z]{2}-[a-z]{2}$/' , $path_parts[ 0 ] ) ) {
        array_shift( $path_parts );
        $path = implode( '/' , $path_parts );
}

# --- конфиг: один раз (admin IP + host whitelist) ---
$cfg_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'site.config';
$cfg = is_file( $cfg_path ) ? @json_decode( @file_get_contents( $cfg_path ) , true ) : null;
if ( ! is_array( $cfg ) ) $cfg = [ ];

// 1. Определяем хост и IP
$raw_host = $_SERVER[ 'HTTP_HOST' ] ?? '';
$host     = strtolower( preg_replace( '/:\d+$/', '', $raw_host ) );
$is_ip = filter_var( $host , FILTER_VALIDATE_IP ) !== false;
$is_local_ip = in_array( $host , [ '127.0.0.1' , 'localhost' , '::1' ] ) || ( $is_ip && ! filter_var( $host , FILTER_VALIDATE_IP , FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) );

// 2. Если это локальный IP и НЕ админка — перекидываем на нормальный домен
if ( $is_local_ip && $path !== 'admin' && !str_starts_with( $path , 'admin/' ) ) {
        http_response_code( 403 );
        exit( 'Access via IP is forbidden. Please use the domain name.' );
}

// 3. Проверка доступа к админке
if ( $path === 'admin' || str_starts_with( $path , 'admin/' ) ) {

    if ( ! defined( 'PROJECT_ROOT' ) ) {
        define( 'PROJECT_ROOT' , dirname( __DIR__ ) );
    }
    require_once __DIR__ . '/admin/_lib/Security.php';

    // 3.0. Окружение: APP_ENV (env → engine.config[env] → 'prod').
    //      ⚠️ APP_ENV=dev разрешает доступ к админке с любого IP — только для локальной разработки.
    $app_env = getenv( 'APP_ENV' );
    if ( $app_env === false || $app_env === '' ) {
        $ec_path = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'engine.config';
        $ec      = is_file( $ec_path ) ? @json_decode( @file_get_contents( $ec_path ) , true ) : null;
        $app_env = is_array( $ec ) ? (string) ( $ec[ 'env' ] ?? 'prod' ) : 'prod';
    }
    $is_dev = ( $app_env === 'dev' );

    $ip = $_SERVER[ 'REMOTE_ADDR' ] ?? '';

    // 3.1. IP в белом списке (в dev-окружении — любой IP)
    $is_whitelisted = Security::is_whitelisted( $ip ) || $is_dev;

    if ( $is_whitelisted ) {
        $admin_bootstrap = __DIR__ . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'bootstrap.php';
        if ( ! is_file( $admin_bootstrap ) ) {
            http_response_code( 500 );
            exit( 'Admin bootstrap not found' );
        }
        require_once $admin_bootstrap;
        exit;
    }

    // 3.2. Совпадение отпечатка устройства (Cookie) — PHP cookie уже urldecode'нуты
    $fp_json = $_COOKIE[ 'admin_fp' ] ?? '';
    $fp = json_decode( $fp_json , true ) ?: [];
    if ( ! empty( $fp ) && Security::find_device( $fp ) !== null ) {
        $admin_bootstrap = __DIR__ . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'bootstrap.php';
        require_once $admin_bootstrap;
        exit;
    }

    // 3.3. Наличие invite_token (для привязки нового устройства или пользователя)
    $token = $_GET[ 'token' ] ?? '';
    if ( $token !== '' && Security::validate_invite( $token ) !== null ) {
        $admin_bootstrap = __DIR__ . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'bootstrap.php';
        require_once $admin_bootstrap;
        exit;
    }

    // 3.4. Ничего не совпало — скрываем админку (404)
    http_response_code( 404 );
    header( 'Content-Type: text/html; charset=UTF-8' );
    echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>';
    exit;
}

# host — валидация по config/site.config:domains_real (C2+C4+H2)
$raw_host = $_SERVER[ 'HTTP_HOST' ] ?? '';
$host     = strtolower( preg_replace( '/:\d+$/', '', $raw_host ) );
$is_ip = filter_var( $host , FILTER_VALIDATE_IP ) !== false;
$is_local_ip = in_array( $host , [ '127.0.0.1' , 'localhost' , '::1' ] ) || ( $is_ip && ! filter_var( $host , FILTER_VALIDATE_IP , FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) );

if ( empty( $cfg[ 'domains_real' ] ) || ! is_array( $cfg[ 'domains_real' ] ) ) {
        http_response_code( 500 );
        exit( 'Configuration error' );
}
$host_valid = false;
foreach ( array_keys( $cfg[ 'domains_real' ] ) as $known ) {
        # точное совпадение: damoony2.ru, damoony.ru, ...
        if ( $host === $known ) { $host_valid = true; break; }

        # <allias>.<known>: visuo.damoony2.ru, www.damoony.ru, ...
        $suffix = '.' . $known;
        if ( strlen( $host ) > strlen( $suffix ) && substr( $host , -strlen( $suffix ) ) === $suffix ) {
                $prefix = substr( $host , 0 , -strlen( $suffix ) );
                $real   = $cfg[ 'domains_real' ][ $known ];
                if ( isset( $cfg[ 'alliases_real' ][ $real ][ $prefix ] )
                  || ! empty( $cfg[ 'alliases_available' ][ $real ][ $prefix ] ) ) {
                        $host_valid = true;
                        break;
                }
        }
}
if ( ! $host_valid && ! $is_local_ip ) {
        http_response_code( 400 );
        exit( 'Bad Host' );
}

# Content-Type
header( 'Content-Type: text/html; charset=UTF-8' );

# --- is_https (C3): X-Forwarded-Proto только от доверенных прокси ---
$is_https = ( ( $_SERVER[ 'HTTPS' ] ?? '' ) === 'on' );

# Прокси — только из конфига (trusted_proxies), иначе XFP игнорируется.
$trusted_proxies = $cfg[ 'trusted_proxies' ] ?? [ ];
if ( is_array( $trusted_proxies ) && ! empty( $trusted_proxies ) ) {
    $remote = $_SERVER[ 'REMOTE_ADDR' ] ?? '';
    if ( in_array( $remote , $trusted_proxies , true ) ) {
        if ( strtolower( $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] ?? '' ) === 'https' ) {
            $is_https = true;
        }
    }
}

# --- middleware pipeline ---
require_once __DIR__ . '/../engine/middleware.php';

try {
    ( new middleware( ) )
        ->add( fn( ) => middleware::securityHeaders( $is_https ) , 'security' )
        ->add( fn( ) => middleware::rateLimit( 'site' , 120 , 60 , 900 ) , 'rate-limit' )
        ->run( );
} catch ( StopMiddleware $e ) {
    http_response_code( 429 );
    die( 'Too Many Requests' );
}

# --- SSO-ready: cookie domain для поддоменов из domains_real (8/N) ---
$real_domain  = $cfg[ 'domains_real' ][ $host ] ?? ( $is_local_ip ? $host : null );
$cookie_domain = '';
if ( $real_domain !== null && filter_var( $host , FILTER_VALIDATE_IP ) === false ) {
    $cookie_domain = '.' . $real_domain;
}

session_set_cookie_params( [
    'lifetime' => 0 ,
    'path'     => '/' ,
    'domain'   => $cookie_domain ,
    'secure'   => $is_https ,
    'httponly' => true ,
    'samesite' => 'Lax' ,
] );
session_start( );

# 2 : Показывать / Не показывать ошибки и предупреждения
# Error_Reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED );

# 3 : Константы времени
define( 'SCRIPTSTART_MT' , $start );
define( 'SCRIPTSTART_HR' , $start_hr );

# 4 : Определяем константу, по которой будем определять подлинность запроса
if ( ! defined( 'GDEF_SECURE_CONSTANT' ) ) define( 'GDEF_SECURE_CONSTANT' , 'TRUE' );
if ( ! defined( 'DS' ) ) define( 'DS' , DIRECTORY_SEPARATOR );
if ( ! defined( 'DSA' ) ) define( 'DSA' , '/' );
if ( ! defined( 'BR' ) ) define( 'BR' , PHP_EOL );
if ( ! defined( 'SIMPLECMS' ) ) define( 'SIMPLECMS' , true );

function error ( string $title , string $message , string $file = '' , string $line = '' ) {
        $log = date( 'Y-m-d H:i:s' ) . ' | ' . $title . ' | ' . $message;
        if ( $file !== '' ) $log .= ' | ' . $file;
        if ( $line !== '' ) $log .= ':' . $line;
        @error_log( $log );

        if ( defined( 'GL_DEBUG' ) && GL_DEBUG === true ) {
                if ( ! headers_sent( ) ) {
                        http_response_code( 500 );
                        header( 'Content-Type: text/html; charset=UTF-8' );
                }
                echo '<!doctype html><html><head><meta charset="utf-8"><title>' . htmlspecialchars( $title ) . '</title></head><body>';
                echo '<h1>' . htmlspecialchars( $title ) . '</h1>';
                echo '<p>' . htmlspecialchars( $message ) . '</p>';
                if ( $file !== '' ) echo '<p><small>' . htmlspecialchars( $file ) . ':' . (int) $line . '</small></p>';
                echo '</body></html>';
                exit;
        }

        http_response_code( 500 );
        exit( 'Internal Server Error' );
}

function error_file ( string $path , string $file , string $line ) {
        error( 'Не удалось подключить файл' , 'Не удалось подключить файл: ' . $path . '. Проверьте целостность структуры сайта.' , $file , $line );
}

function warning_file ( string $path , string $file , string $line ) { }

function error_class ( string $classname , string $file , string $line ) {
        error( 'Отсутствует класс' , 'Отсутствует класс: ' . $classname . '. Проверьте целостность структуры сайта.' , $file , $line );
}

# --- глобальные обработчики (H4) ---
set_error_handler( static function ( int $errno , string $errstr , string $errfile , int $errline ) : bool {
        # типы, которые не превращаем в исключения (шум / уже маскируются)
        if ( in_array( $errno , [ E_DEPRECATED , E_NOTICE , E_USER_DEPRECATED , E_USER_NOTICE ] , true ) ) return false;
        # если error_reporting уже исключил тип — не мешаем
        if ( ! ( error_reporting( ) & $errno ) ) return false;
        throw new \ErrorException( $errstr , 0 , $errno , $errfile , $errline );
} );

set_exception_handler( static function ( \Throwable $e ) : void {
        error(
                'Unhandled: ' . get_class( $e ) ,
                $e->getMessage( ) . ' @ ' . $e->getFile( ) . ':' . $e->getLine( ) ,
                $e->getFile( ) ,
                (string) $e->getLine( )
        );
} );

register_shutdown_function( static function ( ) : void {
        $err = error_get_last( );
        if ( $err === null ) return;
        if ( ! in_array( $err[ 'type' ] , [ E_ERROR , E_PARSE , E_CORE_ERROR , E_COMPILE_ERROR , E_USER_ERROR ] , true ) ) return;

        # уже что-то выведено — не дублируем HTML, только лог
        if ( headers_sent( ) ) {
                @error_log( 'FATAL: ' . $err[ 'message' ] . ' @ ' . $err[ 'file' ] . ':' . $err[ 'line' ] );
                return;
        }
        error( 'Fatal' , $err[ 'message' ] , $err[ 'file' ] , (string) $err[ 'line' ] );
} );

$engine = __DIR__ . DS . '..' . DS . 'engine';

( include_once $path = $engine . DS . 'constants.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $engine . DS . 'included.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = $engine . DS . 'main.php' ) or error_file( $path , __FILE__ , __LINE__ );

$url = null;

$main_settings = new main_settings( );

$main = new main( $main_settings );
$main->main( );
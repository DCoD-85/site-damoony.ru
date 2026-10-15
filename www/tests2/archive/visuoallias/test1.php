<?PHP
# ВРЕМЕННЫЙ тест dataprovider visuo. УДАЛИТЬ после проверки.
# URL: http://visuo.damoony2.ru/tests/visuoallias/test1.php

error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/plain; charset=utf-8' );

# --- минимальные константы (как в index.php) ---
if ( ! defined( 'DS' ) )  define( 'DS'  , DIRECTORY_SEPARATOR );
if ( ! defined( 'DSA' ) ) define( 'DSA' , '/' );
if ( ! defined( 'BR' ) )  define( 'BR'  , PHP_EOL );
if ( ! defined( 'SIMPLECMS' ) ) define( 'SIMPLECMS' , true );
if ( ! defined( 'GDEF_SECURE_CONSTANT' ) ) define( 'GDEF_SECURE_CONSTANT' , 'TRUE' );

$start    = microtime( true );
$start_hr = hrtime( true );
if ( ! defined( 'SCRIPTSTART_MT' ) ) define( 'SCRIPTSTART_MT' , $start );
if ( ! defined( 'SCRIPTSTART_HR' ) ) define( 'SCRIPTSTART_HR' , $start_hr );

# --- заглушки error-функций (index.php определяет свои, но те не должны подгружаться) ---
if ( ! function_exists( 'error' ) ) {
	function error ( $title , $message , $file = '' , $line = '' ) {
		die( "ERROR: {$title} | {$message} ({$file}:{$line})\n" );
	}
}
if ( ! function_exists( 'error_file' ) ) {
	function error_file ( $path , $file , $line ) {
		error( 'File' , $path , $file , $line );
	}
}
if ( ! function_exists( 'warning_file' ) ) {
	function warning_file ( $path , $file , $line ) { }
}
if ( ! function_exists( 'error_class' ) ) {
	function error_class ( $classname , $file , $line ) {
		error( 'Class' , $classname , $file , $line );
	}
}

# __DIR__ = .../www/tests/visuoallias/
# dirname(__DIR__, 2) = .../www/
$root_dir = dirname( __DIR__ , 2 );

# --- bootstrap (повтор шагов 1–4 из www/index.php, без $main->main()) ---
require_once $root_dir . '/constants.php';
require_once $root_dir . '/included.php';
require_once $root_dir . '/main.php';

$main_settings = new main_settings( );
$main = new main( $main_settings );
# --- создать $main->db вручную (в index.php это делает main() позже) ---
if ( ! isset( $main->db ) ) {
	if ( ! class_exists( 'db' ) ) {
		require_once dirname( $root_dir ) . '/sources/db.php';
	}
	$main->db = new db( $main );
}

# --- dataprovider visuo: подключаем и создаём вручную ---
$dp_file = $root_dir . '/logic/alpha.0.0/domain/damoony.ru/allias/visuo/dataprovider/dataprovider.php';

if ( ! class_exists( 'damoony\\visuo\\dataprovider' ) ) {
	if ( ! is_file( $dp_file ) ) {
		echo "dataprovider.php not found:\n  {$dp_file}\n";
		echo "проверь путь — возможно, структура другая.\n";
		exit( 1 );
	}
	require_once $dp_file;
}

$dp = new \damoony\visuo\dataprovider( $main );

echo "=== dataprovider visuo: smoke test ===\n";
echo 'root_dir : ' . $root_dir . "\n";
echo 'main     : ' . get_class( $main ) . "\n";
echo 'facade   : ' . get_class( $dp ) . "\n";
echo 'user  set: ' . ( $dp->user  ? get_class( $dp->user  ) : '(null)' ) . "\n";
echo 'doc   set: ' . ( $dp->doc   ? get_class( $dp->doc   ) : '(null)' ) . "\n";
echo 'news  set: ' . ( $dp->news  ? get_class( $dp->news  ) : '(null)' ) . "\n";
echo 'files set: ' . ( $dp->files ? get_class( $dp->files ) : '(null)' ) . "\n";
echo "\n";

# 1. news->recent
echo "-- news->recent( 10 , 'ru-ru' ) --\n";
$news = $dp->news->recent( 10 , 'ru-ru' );
foreach ( $news as $n ) {
	echo "  #{$n['id']} {$n['slug']} — {$n['title']}\n";
}
echo '  всего: ' . count( $news ) . "\n\n";

# 2. news->by_slug
echo "-- news->by_slug( 'visuo-start' , 'ru-ru' ) --\n";
$n = $dp->news->by_slug( 'visuo-start' , 'ru-ru' );
echo '  title   : ' . ( $n['title']  ?? '(none)' ) . "\n";
echo '  status  : ' . ( $n['status'] ?? '(none)' ) . "\n";
echo '  lang    : ' . ( $n['lang']   ?? '(none)' ) . "\n\n";

# 3. news->by_id
echo "-- news->by_id( 1 ) --\n";
$n = $dp->news->by_id( 1 );
echo '  slug    : ' . ( $n['slug'] ?? '(none)' ) . "\n\n";

# 4. files->all_active
echo "-- files->all_active() --\n";
$files = $dp->files->all_active( );
foreach ( $files as $f ) {
	echo "  #{$f['id']} {$f['slug']} — {$f['title']} ({$f['filename']}, {$f['filesize']} b)\n";
}
echo '  всего: ' . count( $files ) . "\n\n";

# 5. files->by_slug
echo "-- files->by_slug( 'template' ) --\n";
$f = $dp->files->by_slug( 'template' );
echo '  title   : ' . ( $f['title']   ?? '(none)' ) . "\n";
echo '  version : ' . ( $f['version'] ?? '(none)' ) . "\n\n";

# 6. user->by_login
echo "-- user->by_login( 'admin' ) --\n";
$u = $dp->user->by_login( 'admin' );
echo '  id      : ' . ( $u['id']    ?? '(none)' ) . "\n";
echo '  login   : ' . ( $u['login'] ?? '(none)' ) . "\n";
echo '  email   : ' . ( $u['email'] ?? '(none)' ) . "\n\n";

# 7. user->has_role
echo "-- user->has_role( admin_id , 'admin' ) --\n";
$has = $dp->user->has_role( (int) ( $u['id'] ?? 0 ) , 'admin' );
echo '  has admin role: ' . ( $has ? 'yes' : 'no' ) . "\n\n";

# 8. user->check_bruteforce
echo "-- user->check_bruteforce( 'admin' , '127.0.0.1' ) --\n";
$ok = $dp->user->check_bruteforce( 'admin' , '127.0.0.1' );
echo '  allowed: ' . ( $ok ? 'yes' : 'no (blocked)' ) . "\n\n";

# 9. user->profile_full
echo "-- user->profile_full( admin_id ) --\n";
$p = $dp->user->profile_full( (int) ( $u['id'] ?? 0 ) );
echo '  timezone: ' . ( $p['timezone'] ?? '(none)' ) . "\n";
echo '  lang    : ' . ( $p['lang']     ?? '(none)' ) . "\n\n";

# 10. Пустые результаты
echo "-- проверка пустых результатов --\n";
$empty_news  = $dp->news->by_slug( 'nonexistent-slug' , 'ru-ru' );
$empty_files = $dp->files->by_slug( 'nonexistent-slug' );
echo '  news->by_slug( nonexistent )  = ' . ( empty( $empty_news )  ? 'empty []' : 'NOT EMPTY' ) . "\n";
echo '  files->by_slug( nonexistent ) = ' . ( empty( $empty_files ) ? 'empty []' : 'NOT EMPTY' ) . "\n\n";

# 11. Проверка счётчиков files (без инкремента)
echo "-- files->downloads_total( 1 ) --\n";
echo '  total: ' . $dp->files->downloads_total( 1 ) . "\n\n";

# --- ЯЗЫКИ ---
echo "-- language test --\n";

$lp = dirname( $root_dir ) . '/sources/language.php';
echo "path   : {$lp}\n";
echo 'exists : ' . ( is_file( $lp ) ? 'yes' : 'no' ) . "\n";

require_once $lp;
echo 'class  : ' . ( class_exists( 'language' ) ? 'yes' : 'no' ) . "\n\n";

foreach ( [ 'ru-ru' , 'en-en' , 'ch-ch' ] as $code ) {
	$lang = new \language( $main );
	$lang->set_language( $code );

	echo "  [{$code}]\n";
	echo '    VISUO_TITLE       : ' . $lang->get( 'VISUO_TITLE'        , [ ]        , '(missing)' ) . "\n";
	echo '    MENU_DOCS         : ' . $lang->get( 'MENU_DOCS'          , [ ]        , '(missing)' ) . "\n";
	echo '    FORM_SUBMIT_LOGIN : ' . $lang->get( 'FORM_SUBMIT_LOGIN'  , [ ]        , '(missing)' ) . "\n";
	echo '    OK_LOGIN          : ' . $lang->get( 'OK_LOGIN'           , [ 'Tester' ] , '(missing)' ) . "\n";
	echo "\n";
}

# --- ПОЛНАЯ ПРОВЕРКА ЯЗЫКОВ ---
echo "-- full language key check --\n";

$keys_to_check = [
	'LOGIN_GUEST_WELCOME' , 'HELLO_TEXT' , 'LOGOUT_TEXT_TIME' , 'LOGOUT_TEXT' ,
	'LOGIN_GUEST_NEED_AUTH' , 'LOGIN_GUEST_ALSO_AUTH' ,
	'TOP_PROJECTS_DAMOONY' , 'TOP_PROJECTS_ASPTOUR' ,
	'TOP_SKINS_LIGHT' , 'TOP_SKINS_DARK' ,
	'TOP_LOGIN_PROFILE' , 'TOP_LOGIN_SETTINGS' , 'TOP_LOGIN_LOGOUT' ,
	'TOP_LOGIN_LOGIN' , 'TOP_LOGIN_REGISTRATION' ,
	'TOP_LANGUAGES_RUS' , 'TOP_LANGUAGES_ENG' , 'TOP_LANGUAGES_CHINA' ,
	'TOP_NAVIGATE_LOGIN' , 'TOP_NAVIGATE_REGISTRATION' , 'TOP_NAVIGATE_PROFILE' ,
	'TOP_NAVIGATE_SETTINGS' , 'TOP_NAVIGATE_LOGOUT' ,
	'VISUO_TITLE' , 'VISUO_INTRO' , 'VISUO_TAGLINE' , 'VISUO_FOOTER' , 'VISUO_POLICY' ,
	'PAGE_HOME' , 'PAGE_DOCS' , 'PAGE_NEWS' , 'PAGE_DOWNLOADS' , 'PAGE_LOGIN' ,
	'PAGE_REGISTRATION' , 'PAGE_PROFILE' , 'PAGE_LOGOUT' ,
	'MENU_HOME' , 'MENU_DOCS' , 'MENU_NEWS' , 'MENU_DOWNLOADS' ,
	'MENU_LOGIN' , 'MENU_REGISTRATION' , 'MENU_LOGOUT' , 'MENU_PROFILE' ,
	'BTN_SAVE' , 'BTN_CANCEL' , 'BTN_DOWNLOAD' , 'BTN_BACK' ,
	'FORM_LOGIN_LABEL' , 'FORM_PASSWORD_LABEL' , 'FORM_PASSWORD_CONFIRM_LABEL' ,
	'FORM_EMAIL_LABEL' , 'FORM_DISPLAY_NAME_LABEL' ,
	'FORM_SUBMIT_LOGIN' , 'FORM_SUBMIT_REGISTER' , 'FORM_SUBMIT_LOGOUT' , 'FORM_REMEMBER_ME' ,
	'PROFILE_TITLE' , 'PROFILE_TIMEZONE' , 'PROFILE_LANG' , 'PROFILE_LOGIN' ,
	'PROFILE_EMAIL' , 'PROFILE_DISPLAY_NAME' , 'PROFILE_CREATED_AT' , 'PROFILE_LAST_LOGIN' , 'PROFILE_SAVED' ,
	'DOCS_TITLE' , 'DOCS_SECTIONS' , 'DOCS_EMPTY' , 'DOCS_ARTICLE_NOT_FOUND' , 'DOCS_TRANSLATION_PENDING' ,
	'NEWS_TITLE' , 'NEWS_EMPTY' , 'NEWS_READ_MORE' , 'NEWS_PUBLISHED_AT' , 'NEWS_NOT_FOUND' ,
	'DOWNLOADS_TITLE' , 'DOWNLOADS_EMPTY' , 'DOWNLOADS_EMPTY' ,
	'DOWNLOAD_SIZE' , 'DOWNLOAD_VERSION' , 'DOWNLOAD_COUNT' , 'DOWNLOAD_BUTTON' , 'DOWNLOAD_FILE_NOT_FOUND' ,
	'ERROR_404_TITLE' , 'ERROR_404_TEXT' , 'ERROR_403_TITLE' , 'ERROR_403_TEXT' ,
	'ERROR_500_TITLE' , 'ERROR_500_TEXT' , 'ERROR_CSRF' ,
	'ERROR_LOGIN_REQUIRED' , 'ERROR_LOGIN_FAILED' , 'ERROR_LOGIN_BLOCKED' ,
	'ERROR_REGISTER_LOGIN_TAKEN' , 'ERROR_REGISTER_EMAIL_TAKEN' ,
	'ERROR_REGISTER_LOGIN_FORMAT' , 'ERROR_REGISTER_EMAIL_FORMAT' ,
	'ERROR_REGISTER_PASSWORD_SHORT' , 'ERROR_REGISTER_PASSWORD_MISMATCH' ,
	'OK_LOGIN' , 'OK_LOGOUT' , 'OK_REGISTER' , 'OK_PROFILE_SAVED' ,
	'LANG_RU' , 'LANG_EN' , 'LANG_CH' ,
];

foreach ( [ 'ru-ru' , 'en-en' , 'ch-ch' ] as $code ) {
	$lang = new \language( $main );
	$lang->set_language( $code );

	$missing = [];
	foreach ( $keys_to_check as $k ) {
		$v = $lang->get( $k , [ 'x' ] , '' );
		if ( $v === '' && $k !== 'TOP_SKINS_LIGHT_COMMENT' && $k !== 'TOP_SKINS_DARK_COMMENT'
			&& ! str_ends_with( $k , '_COMMENT' ) ) {
			$missing[ ] = $k;
		}
	}
	echo "  [{$code}] keys checked: " . count( $keys_to_check ) . ' , missing: ' . count( $missing ) . "\n";
	if ( $missing ) {
		echo '    missing: ' . implode( ', ' , $missing ) . "\n";
	}
}
echo "\n";

echo "=== done ===\n";
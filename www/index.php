<?PHP
# ini_set('session.cookie_domain', '.damoony2.ru');
# session_start();
# 
# 
# 
# 0: Стартуем скрипт
$start = microtime( true );
$start_hr = hrtime( true );

$host = explode( '.' , strrev( $_SERVER[ 'HTTP_HOST' ] ) , 3 );
$host = strrev( implode( '.' , [ $host[ 0 ] , $host[ 1 ] ] ) );

# 
header( 'Content-Type: text/html; charset=UTF-8' );
# header( 'Content-Type: application/json; charset=UTF-8' );

# version 1 : start
# session_set_cookie_params( 0 , domain : '.' . $host );
# if ( isset( $_COOKIE[ 'PHPSESSID' ] ) ){
# 	setcookie( 'PHPSESSID' , $_COOKIE[ 'PHPSESSID' ] , 0 , '/' , '.' . $host );
# 	# session_name( $_COOKIE[ 'PHPSESSID' ] );
# } else session_start();
# version 1 : end


# version 2 : start

session_set_cookie_params( [
	'lifetime'=>0 ,
	'path'=>'/' ,
	'domain'=>'.' . $host ,
	'secure'=>true ,
	'httponly'=>true ,
	# 'samesite'=>'Lax' ,
	'samesite'=>'Lax'
] ); 

if ( isset( $_COOKIE[ 'PHPSESSID' ] ) ){
	# setcookie( 'PHPSESSID' , $_COOKIE[ 'PHPSESSID' ] , 0 , '/' , '.' . $host , true , true );
	setcookie( 'PHPSESSID' , $_COOKIE[ 'PHPSESSID' ] , [
		'path'=>'/' ,
		'domain'=>'.' . $host ,
		'secure'=>true ,
		'httponly'=>true ,
		# 'samesite'=>'Lax' ,
		'samesite'=>'Lax'
	] );
	session_start( );
	# session_name( $_COOKIE[ 'PHPSESSID' ] );
} else session_start();
# version 2 : end

# 1 : Стартуем сессию:
# session_start( );

# 2 : Показывать / Не показывать ошибки и предупреждения
# Error_Reporting( E_ALL );
# ini_set( 'display_errors' , '1' );
Error_Reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED );

# 3 : Константы времени
define( 'SCRIPTSTART_MT' , $start );
define( 'SCRIPTSTART_HR' , $start_hr );

# 4 : Определяем константу, по которой будем определять подлинность запроса
if( ! defined( TRUE ) ) define( 'GDEF_SECURE_CONSTANT' , 'TRUE' );
if( ! defined( 'DS' ) ) define( 'DS' , DIRECTORY_SEPARATOR );				// Разделитель файловой системы
if( ! defined( 'DSA' ) ) define( 'DSA' , '/' );													// Разделитель адресной строки
if( ! defined( 'BR' ) ) define( 'BR' , PHP_EOL );											// 
if( ! defined( 'SIMPLECMS' ) ) define( 'SIMPLECMS' , true );									// Альтернативная CMS

function error ( string $title , string $message , string $file = '' , string $line = '' ) {
	$title = $title ?: 'Проверьте целостность сайта.';
	$message = $message ?: 'Проверьте целостность сайта.';
	$mail_href = 'mailto:noadmin@damoony2.ru';
	$mail_title = 'написать письмо';
	
	$search = str_replace( DSA , DS , $_SERVER[ 'DOCUMENT_ROOT' ] );
	$replace = '..';
	$file = str_replace( $search , $replace , $file );
	die( '<! doctype>
<html>
	<head>
		<title>' . $title . '</title>
		<link href="' . DSA . 'media' . DSA . 'css' . DSA . 'main.css?rand=' . rand( 111 , 999 ) . '" type="text/css" rel="stylesheet" />
	</head>
	<body class="errorbase badinclude">
		<div class="messages">
			<div class="message">Message: ' . $message . '</div>
		</div>
		' . ( $file && $line ? '<div class="file">from file: ' . $file . ':' . $line . '</div>' : '' ) . '
		<div class="mailtoadmin">Свяжитесь с администратором: <a href="' . $mail_href . '">' . $mail_title . '</a>.</div>
		' . ( $_GET[ 'phpinfo' ] == 'phpinfo' ? '<div>' . phpinfo( ) . '</div>' : '' ) . '
	</body>
</html>' );

}

function error_file ( string $path , string $file , string $line ) {
	error( 'Не удалось подключить файл' , 'Не удалось подключить файл: ' . $path . '. Проверьте целостность структуры сайта.' , $file , $line );
}

function warning_file ( string $path , string $file , string $line ) {
	# error( 'Не удалось подключить файл' , 'Не удалось подключить файл: ' . $path . '. Проверьте целостность структуры сайта.' , $file , $line );
}

function error_class ( string $classname , string $file , string $line ) {
	error( 'Отсутствует класс' , 'Отсутствует класс: ' . $classname . '. Проверьте целостность структуры сайта.' , $file , $line );
}

( include_once $path = __DIR__ . DS . 'constants.php' ) or error_file( $path , __FILE__ , __LINE__ );
( include_once $path = __DIR__ . DS . 'included.php' ) or error_file( $path , __FILE__ , __LINE__ );

( include_once $path = __DIR__ . DS . 'main.php' ) or error_file( $path , __FILE__ , __LINE__ );

$url = null;// 'https://login.damoony2.ru/ru-ru/form';

$main_settings = new main_settings( );

$main = new main( $main_settings );
$main->main( );

?>
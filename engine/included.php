<?PHP

if( ! defined( 'DS' ) ) define( 'DS' , DIRECTORY_SEPARATOR );
if( ! defined( 'DSA' ) ) define( 'DSA' , '/' );

function fo( array $fo , string $file_sep_start = ' [ ' , string $file_sep_end = ' ] ' ){
	$file = $fo[ 0 ] . ':' . $fo[ 1 ];
	$method = $fo[ 2 ];
	
	return $method ? $method . $file_sep_start . $file . $file_sep_end : $file;
}
function bad ( array $fo , string $title = 'Ошибка' , string $message = 'Ошибка' ){
	# if( class_exists( 'visuotag' ) ){
	# 	$meta = new visuometa( null );
	# 	$meta->add_title( )->data( $title );
	# 	$meta->add_link( )->data( DSA . 'bad' . DSA . 'main.css' , rand: true );
	# 	$vt = new visuotag( null , null , 'root' , true , true );
	# 	$vt = $vt->tag( '! doctype'  , false , false )
	# 		->tag( 'head' )->meta( $meta )
	# 		->root( )->tag( 'body' , class: 'badinclude' )
	# 			->tag( 'div' , class: 'messages' )->tag( 'div' )->content( $message )
	# 			->div( )->tag( 'div' )->content( 'Свяжитесь с администратором: <a href="mailto:noadmin@damoony2.ru">написать письмо</a>.' );
	# 	die( $vt->final( ) );
	# } else {
	$string = '<! doctype>
<html>
	<head>
		<title>' . $title . '</title>
		<link href="' . DSA . 'bad' . DSA . 'main.css?rand=' . rand( 111 , 999 ) . '" type="text/css" rel="stylesheet" />
	</head>
	<body class="badinclude">
		<div class="messages">
			<div>' . $message . '</div>
			<div>' . $fo[ 0 ] . ':' . $fo[ 1 ] . '</div>
			<div>Свяжитесь с администратором: <a href="mailto:noadmin@damoony2.ru">написать письмо</a>.</div>
		</div>
	</body>
</html>';
		die( $string );
	# }
}
function include_bad( array $fo , string $file ){
	bad( $fo , 'Не возможно подключить файл' , 'Не возможно подключить файл: <span>' . $file . '</span>' );
}

function include_my( array $fo , string $file ){
	if( ! include_once $file ) include_bad( $fo , $file );
}

function include_array( array $fo , array $files , array $classes = [ ] , string $release_path = '' ) {
	if( ! empty( $files ) ) foreach( $files as $file ) include_my( $fo , $file );
	else bad( [ __FILE__ , __LINE__ , __METHOD__ ] , 'Ошибка.' , 'Отсутствуют файлы для подключения.<br />Вызван из: ' . $fo[ 0 ] . ':' . $fo[ 1 ] . '<br />' );
	
	$error_classes = [ ];
	if( ! empty( $classes ) ) foreach( $classes as $class ) if( ! class_exists( $class ) ) $error_classes[ ] = $class;
	
	$error_classes_string = '';
	if( ! empty( $error_classes ) ) foreach( $error_classes as $class ) $error_classes_string .= $class . ', ';
	if( ! empty( $error_classes ) ) bad( [ __FILE__ , __LINE__ , __METHOD__ ] , 'Ошибка.' , 'Отсутствуют классы: ' . $error_classes_string . '.' );
}

?>
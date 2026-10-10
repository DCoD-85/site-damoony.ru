<?PHP

namespace <domain_ns>\<name>;

# ---------------------------------------------------------------
#   helpers — утилиты visuo
#   Подключается из include.php, до алгоритмов.
# ---------------------------------------------------------------

# --- escape ---
function h ( ?string $s ) : string {
	return htmlspecialchars( (string) $s , ENT_QUOTES , 'UTF-8' );
}

# --- CSRF ---
function csrf_token ( ) : string {
	if ( session_status( ) !== PHP_SESSION_ACTIVE ) {
		@session_start( );
	}
	if ( empty( $_SESSION[ 'visuo_csrf' ] ) ) {
		$_SESSION[ 'visuo_csrf' ] = bin2hex( random_bytes( 32 ) );
	}
	return (string) $_SESSION[ 'visuo_csrf' ];
}

function csrf_field ( ) : string {
	return '<input type="hidden" name="_csrf" value="' . h( csrf_token( ) ) . '">';
}

function csrf_check ( ) : bool {
	if ( ( $_SERVER[ 'REQUEST_METHOD' ] ?? 'GET' ) !== 'POST' ) return true;
	$t = $_SESSION[ 'visuo_csrf' ] ?? '';
	$r = $_POST[ '_csrf' ] ?? '';
	if ( $t === '' || $r === '' ) return false;
	return hash_equals( $t , $r );
}

# --- slug ---
function slugify ( string $text ) : string {
	$text = mb_strtolower( $text , 'UTF-8' );

	$translit = [
		'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y',
		'к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f',
		'х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
	];
	$text = strtr( $text , $translit );

	$text = preg_replace( '/[^a-z0-9]+/' , '-' , $text );
	$text = trim( $text , '-' );

	return $text !== '' ? $text : 'item-' . substr( md5( microtime( true ) . random_int( 0 , 9999 ) ) , 0 , 8 );
}

# --- datetime ---
function time_local ( string $utc , string $tz = 'Europe/Moscow' ) : string {
	if ( ! in_array( $tz , timezone_identifiers_list( ) , true ) ) {
		$tz = 'Europe/Moscow';
	}
	try {
		$dt = new \DateTime( $utc , new \DateTimeZone( 'UTC' ) );
		$dt->setTimezone( new \DateTimeZone( $tz ) );
		return $dt->format( 'd.m.Y H:i' );
	} catch ( \Exception $e ) {
		return $utc;
	}
}

function now_utc ( ) : string {
	return gmdate( 'Y-m-d H:i:s' );
}

# --- redirect / json ---
function redirect ( string $url , int $code = 307 ) : void {
	http_response_code( $code );
	header( 'Location: ' . $url );
	exit;
}

function json_response ( array $data , int $code = 200 ) : void {
	http_response_code( $code );
	header( 'Content-Type: application/json; charset=utf-8' );
	echo json_encode( $data , JSON_UNESCAPED_UNICODE );
	exit;
}

# --- languages ---
function langs_available ( ) : array {
	return [ 'ru-ru' , 'en-en' , 'ch-ch' ];
}

function lang_default ( ) : string {
	return 'ru-ru';
}
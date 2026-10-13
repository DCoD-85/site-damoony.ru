<?PHP

class Csrf {

	public static function token ( ) : string {
		if ( empty( $_SESSION[ 'csrf' ] ) ) {
			$_SESSION[ 'csrf' ] = bin2hex( random_bytes( 32 ) );
		}
		return $_SESSION[ 'csrf' ];
	}

	public static function field ( ) : string {
		return '<input type="hidden" name="_csrf" value="'
			. htmlspecialchars( self::token( ) , ENT_QUOTES ) . '">';
	}

	public static function check ( ) : bool {
		$t = (string) ( $_POST[ '_csrf' ] ?? '' );
		return $t !== '' && hash_equals( self::token( ) , $t );
	}
}
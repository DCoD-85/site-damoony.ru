<?PHP

class Validate {

	public static function allias_name ( string $name ) : ?string {
		if ( ! preg_match( '/^[a-z][a-z0-9_]*$/' , $name ) ) {
			return 'Имя аллиаса: только a-z, 0-9, _, начинается с буквы';
		}
		if ( strlen( $name ) > 32 ) {
			return 'Имя аллиаса: максимум 32 символа';
		}
		return null;
	}

	public static function domain ( string $domain ) : ?string {
		if ( ! preg_match( '/^[a-z0-9][a-z0-9.-]*\.[a-z]{2,}$/' , $domain ) ) {
			return 'Домен: например damoony.ru, idoam.online';
		}
		return null;
	}

	public static function namespace ( string $ns ) : ?string {
		if ( ! preg_match( '/^[a-z][a-z0-9_]*$/' , $ns ) ) {
			return 'Namespace: только a-z, 0-9, _, начинается с буквы';
		}
		return null;
	}

	# Автоматический namespace из домена: idoam.ru → idoam_ru
	public static function ns_from_domain ( string $domain ) : string {
		return str_replace( [ '.' , '-' ] , '_' , $domain );
	}
}
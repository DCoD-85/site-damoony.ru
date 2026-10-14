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
		# путь страницы внутри algoritm/ — '' или 'news/archive/2024'
	# допустимы только [a-z0-9_/], начинается с буквы, без '..' и двойных '//'
	public static function page_path ( string $path ) : ?string {
		if ( $path === '' ) return null;

		if ( strlen( $path ) > 128 ) {
			return 'Путь: максимум 128 символов';
		}
		if ( str_contains( $path , '..' ) ) {
			return 'Путь: недопустимая последовательность ".."';
		}
		if ( str_starts_with( $path , '/' ) || str_ends_with( $path , '/' ) ) {
			return 'Путь: не должен начинаться/заканчиваться на "/"';
		}
		if ( str_contains( $path , '//' ) ) {
			return 'Путь: двойные слэши недопустимы';
		}
		if ( ! preg_match( '#^[a-z][a-z0-9_]*(/[a-z][a-z0-9_]*)*$#i' , $path ) ) {
			return 'Путь: сегменты только [a-z][a-z0-9_]*, разделитель "/"';
		}
		return null;
	}

	# имя темы: одна папка, без слэшей и точек
	public static function skin ( string $skin ) : ?string {
		if ( $skin === '' ) {
			return 'Тема: имя не может быть пустым';
		}
		if ( strlen( $skin ) > 32 ) {
			return 'Тема: максимум 32 символа';
		}
		if ( ! preg_match( '/^[a-z][a-z0-9_-]*$/' , $skin ) ) {
			return 'Тема: только a-z, 0-9, _, -; начинается с буквы';
		}
		if ( basename( $skin ) !== $skin ) {
			return 'Тема: недопустимые символы пути';
		}
		return null;
	}
}
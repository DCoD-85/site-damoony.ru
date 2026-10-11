<?PHP

# ============================================================
#   Scan — сканеры доступных значений для форм админки
#   Пути — от PROJECT_ROOT (константа определена в bootstrap.php)
# ============================================================

class Scan {

	# www/media/damoony.ru/skin/* — только папки
	public static function skins ( ) : array {
		return self::listDirs( PROJECT_ROOT . '/www/media/damoony.ru/skin' );
	}
	
	# www/media/<domain>/skin/* — только папки
	public static function skinsFor ( string $domain ) : array {
		return self::listDirs( PROJECT_ROOT . '/www/media/' . $domain . '/skin' );
	}

	# www/logic/* — только папки, где есть include.php
	public static function logics ( ) : array {
		$dir = PROJECT_ROOT . '/www/logic';
		$out = [ ];
		foreach ( self::listDirs( $dir ) as $name ) {
			if ( is_file( $dir . '/' . $name . '/include.php' ) ) {
				$out[ ] = $name;
			}
		}
		return $out;
	}

	# допустимые значения connect_type — совпадают с SettingsSet::validate()
	public static function connectTypes ( ) : array {
		return [ 'http://' , 'https://' ];
	}

		# --- дерево страниц аллиаса ---

	# $base — абсолютный путь к algoritm/lang/
	# Возвращает массив узлов: [ name, path, has_file, available[], children[] ]
	public static function pageTree ( string $base ) : array {
		if ( ! is_dir( $base ) ) return [ ];

		$out = [ ];
		foreach ( scandir( $base ) as $name ) {
			if ( $name === '.' || $name === '..' ) continue;
			$dir = $base . '/' . $name;
			if ( ! is_dir( $dir ) ) continue;
			$out[ ] = self::pageNode( $name , $dir , $name , '' );
		}
		usort( $out , fn( $a , $b ) => strcmp( $a[ 'name' ] , $b[ 'name' ] ) );
		return $out;
	}

	private static function pageNode ( string $name , string $dir , string $path , string $prefix ) : array {
		$full_ns = $prefix === '' ? $name : $prefix . '_' . $name;
		$file    = $dir . '/algoritm_lang_' . $full_ns . '.php';
		$has     = is_file( $file );

		$children = [ ];
		foreach ( scandir( $dir ) as $sub ) {
			if ( $sub === '.' || $sub === '..' ) continue;
			$sub_dir = $dir . '/' . $sub;
			if ( ! is_dir( $sub_dir ) ) continue;
			$children[ ] = self::pageNode( $sub , $sub_dir , $path . '/' . $sub , $full_ns );
		}
		usort( $children , fn( $a , $b ) => strcmp( $a[ 'name' ] , $b[ 'name' ] ) );

		return [
			'name'      => $name ,
			'path'      => $path ,
			'has_file'  => $has ,
			'available' => $has ? self::parseAvailable( $file ) : [ ] ,
			'children'  => $children ,
		];
	}

	# парсит $available = [ '', 'regex|slug', ... ] из файла
	private static function parseAvailable ( string $file ) : array {
		$c = @file_get_contents( $file );
		if ( $c === false ) return [ ];

		if ( ! preg_match( '/public\s+array\s+\$available\s*=\s*\[([^\]]*)\]/s' , $c , $m ) ) {
			return [ ];
		}

		preg_match_all( "/'((?:[^'\\\\]|\\\\.)*)'/" , $m[ 1 ] , $mm );
		return array_map( fn( $s ) => str_replace( "\\'" , "'" , $s ) , $mm[ 1 ] );
	}

	# --- helper ---
	private static function listDirs ( string $dir ) : array {
		if ( ! is_dir( $dir ) ) return [ ];
		$out = [ ];
		foreach ( scandir( $dir ) as $name ) {
			if ( $name === '.' || $name === '..' ) continue;
			if ( ! is_dir( $dir . '/' . $name ) )  continue;
			$out[ ] = $name;
		}
		sort( $out );
		return $out;
	}
	
}
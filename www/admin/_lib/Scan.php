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
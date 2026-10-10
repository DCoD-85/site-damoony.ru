<?PHP

class SettingsList {

	public static function plan ( array $params ) : array { return [ ]; }

	public static function run ( array $params , ?Context $ctx = null ) : Result {
		$r = new Result( );

		try {
			$cfg = new Config( PROJECT_ROOT . '/config/site.config' );
		} catch ( Throwable $e ) {
			$r->error( 'Не открыть site.config: ' . $e->getMessage( ) );
			return $r;
		}

		$keys = [ 'title' , 'skin' , 'logic' , 'connect_type' ];

		Cli::out( '' );
		Cli::info( 'Текущие настройки:' );
		Cli::out( '' );

		foreach ( $keys as $key ) {
			$val = $cfg->get( $key , '(не задано)' );
			Cli::out( sprintf( '  %-15s = %s' , $key , $val ) );
		}

		Cli::out( '' );
		Cli::info( 'Доступные движки:' );
		foreach ( glob( PROJECT_ROOT . '/www/logic/*' , GLOB_ONLYDIR ) as $dir ) {
			Cli::out( '  - ' . basename( $dir ) );
		}

		Cli::out( '' );
		Cli::info( 'Доступные темы:' );
		$skins = [ ];
		foreach ( glob( PROJECT_ROOT . '/www/media/damoony.ru/skin/*' , GLOB_ONLYDIR ) as $dir ) {
			$skins[ ] = basename( $dir );
		}
		foreach ( $skins as $s ) Cli::out( '  - ' . $s );

		$r->step( 'Настройки выведены' , true , '' );
		return $r;
	}
}
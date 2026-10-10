<?PHP

class AlliasList {

	public static function plan ( array $params ) : array {
		return [ 'domain' => $params[ 'domain' ] ?? 'damoony.ru' ];
	}

	public static function run ( array $params , ?Context $ctx = null ) : Result {
		$r = new Result( );

		$domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );

		try {
			$cfg = new Config( PROJECT_ROOT . '/config/site.config' );
		} catch ( Throwable $e ) {
			$r->error( "Не открыть site.config: " . $e->getMessage( ) );
			return $r;
		}

		$alliases = (array) $cfg->getKeys( [ 'alliases_real' , $domain ] , [ ] );

		if ( empty( $alliases ) ) {
			$r->step( "Аллиасов нет для {$domain}" , true , '' );
			return $r;
		}

		Cli::out( '' );
		Cli::info( "Аллиасы домена {$domain}:" );
		Cli::out( '' );

		foreach ( $alliases as $alias => $target ) {
			if ( $alias === '' ) continue;

			$available = ! empty( $cfg->getKeys( [ 'alliases_available' , $domain , $alias ] ) ) ? '✓' : '✗';
			$path      = PROJECT_ROOT . '/www/logic/alpha.0.0/domain/' . $domain . '/allias/' . $alias;
			$fs        = is_dir( $path ) ? '✓' : '✗';

			$fillers  = (array) $cfg->getKeys( [ 'fillers' , $domain , $alias ] , [ ] );
			$f_true   = 0;
			foreach ( $fillers as $v ) if ( $v ) $f_true++;
			$f_str    = $f_true . '/' . count( $fillers );

			Cli::out( sprintf(
				'  %-20s  avail=%s  fs=%s  fillers=%s' ,
				$alias , $available , $fs , $f_str
			) );
		}

		$r->step( 'Список выведен' , true , count( $alliases ) . ' аллиасов' );
		return $r;
	}
}
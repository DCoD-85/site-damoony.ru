<?PHP

class DomainRemove {

	public static function plan ( array $params ) : array {
		return [ 'name' => $params[ 'name' ] ?? '' ];
	}

	public static function run ( array $params , ?Context $ctx = null ) : Result {
		$r = new Result( );

		$domain = (string) ( $params[ 'name' ] ?? '' );
		$force  = ! empty( $params[ 'force' ] );

		if ( $err = Validate::domain( $domain ) ) {
			$r->step( 'Валидация домена' , false , $err );
			return $r;
		}

		$domain_path = PROJECT_ROOT . '/www/logic/alpha.0.0/domain/' . $domain;

		# --- подтверждение ---
		if ( ! $force ) {
			Cli::out( '' );
			Cli::err( "Будет удалён ВЕСЬ домен: {$domain}" );
			Cli::out( '  - папка: ' . Fs::rel( $domain_path ) );
			Cli::out( '  - все аллиасы и filler-файлы' );
			Cli::out( '  - все записи в site.config' );
			Cli::out( '' );
			if ( ! Cli::confirm( 'Удалить домен полностью?' , false ) ) {
				$r->step( 'Отменено пользователем' , true , '' );
				return $r;
			}
			if ( ! Cli::confirm( 'Точно? Введите ещё раз:' , false ) ) {
				$r->step( 'Отменено пользователем' , true , '' );
				return $r;
			}
		}

		# --- 1. удалить папку ---
		if ( is_dir( $domain_path ) ) {
			Fs::rmrf( $domain_path );
			$r->step( 'Удалена папка домена' , true , Fs::rel( $domain_path ) );
			$ctx?->step( 'Удалена папка домена' , true , Fs::rel( $domain_path ) );
		}

		# --- 2. site.config ---
		try {
			$cfg = new Config( PROJECT_ROOT . '/config/site.config' );
			$data = $cfg->data( );

			unset( $data[ 'domains_real' ][ $domain ] );
			unset( $data[ 'domains_available' ][ $domain ] );
			unset( $data[ 'domains_namespace' ][ $domain ] );
			unset( $data[ 'alliases_real' ][ $domain ] );
			unset( $data[ 'alliases_available' ][ $domain ] );
			unset( $data[ 'fillers' ][ $domain ] );

			$ref  = new ReflectionClass( $cfg );
			$prop = $ref->getProperty( 'data' );
			$prop->setAccessible( true );
			$prop->setValue( $cfg , $data );

			$cfg->save( );
			$r->step( 'site.config обновлён' , true , $domain );
			$ctx?->step( 'site.config обновлён' , true , $domain );
		} catch ( Throwable $e ) {
			$r->error( "site.config: " . $e->getMessage( ) );
		}

		return $r;
	}
}
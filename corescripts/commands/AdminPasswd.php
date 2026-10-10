<?PHP

class AdminPasswd {

	public static function plan ( array $params ) : array {
		return [ 'login' => $params[ 'name' ] ?? 'admin' ];
	}

	public static function run ( array $params , ?Context $ctx = null ) : Result {
		$r = new Result( );

		$login = (string) ( $params[ 'name' ] ?? 'admin' );
		if ( $login === '' ) {
			$r->error( 'Логин не может быть пустым' );
			return $r;
		}

		# пароль — из --password=... или спросить
		$password = (string) ( $params[ 'password' ] ?? '' );
		if ( $password === '' ) {
			Cli::out( '' );
			$password = Cli::ask( 'Пароль (введите вручную)' );
			if ( $password === '' ) {
				$r->error( 'Пароль не может быть пустым' );
				return $r;
			}
		}

		$hash = password_hash( $password , PASSWORD_DEFAULT );
		if ( $hash === false ) {
			$r->error( 'Не удалось создать хеш пароля' );
			return $r;
		}

		# писать в site.config
		try {
			$cfg = new Config( PROJECT_ROOT . '/config/site.config' );
		} catch ( Throwable $e ) {
			$r->error( 'Не открыть site.config: ' . $e->getMessage( ) );
			return $r;
		}

		$cfg->setKeys( [ 'admin' , 'login' ] , $login );
		$cfg->setKeys( [ 'admin' , 'password_hash' ] , $hash );
		$cfg->save( );

		$r->step( 'Логин' , true , $login );
		$r->step( 'Хеш пароля сохранён в site.config.admin' , true , '' );
		$ctx?->step( 'Пароль установлен' , true , $login );

		return $r;
	}
}
<?PHP

class Auth {

	private string $login = 'admin';
	private string $hash  = '';
	private string $path;

	public function __construct ( ) {
		$this->path = PROJECT_ROOT . '/config/site.config';
		if ( is_file( $this->path ) ) {
			$json = file_get_contents( $this->path );
			$data = json_decode( $json , true );
			if ( isset( $data[ 'admin' ][ 'login' ] ) )         $this->login = (string) $data[ 'admin' ][ 'login' ];
			if ( isset( $data[ 'admin' ][ 'password_hash' ] ) ) $this->hash  = (string) $data[ 'admin' ][ 'password_hash' ];
		}
	}

	public function check ( ) : bool {
		return ! empty( $_SESSION[ 'admin_ok' ] );
	}

	public function login ( string $login , string $password ) : bool {
		if ( $this->hash === '' ) return false;
		if ( ! hash_equals( $this->login , $login ) ) return false;
		if ( ! password_verify( $password , $this->hash ) ) return false;

		# защита от session fixation
		if ( session_status( ) === PHP_SESSION_ACTIVE ) {
			session_regenerate_id( true );
		}
		$_SESSION[ 'admin_ok' ]    = true;
		$_SESSION[ 'admin_login' ] = $this->login;
		return true;
	}

	public function logout ( ) : void {
		$_SESSION = [ ];

		if ( ini_get( 'session.use_cookies' ) ) {
			$params = session_get_cookie_params( );
			setcookie(
				session_name( ) ,
				'' ,
				time( ) - 42000 ,
				$params[ 'path' ] ,
				$params[ 'domain' ] ,
				$params[ 'secure' ] ,
				$params[ 'httponly' ]
			);
		}

		session_destroy( );
	}

	public function login_name ( ) : string {
		return (string) ( $_SESSION[ 'admin_login' ] ?? '' );
	}
}
<?PHP

namespace damoony_ru\visuo;

# ---------------------------------------------------------------
#   user — visuo-пользователь.
#   extends \user — чтобы влезть в типизированное main::$user.
#   Работает с visuo_damoony.users / .sessions.
# ---------------------------------------------------------------

class user extends \user {

	# --- state ---
	public null|dataprovider_user $dp = null;
	private ?array $current = null;
	private array  $roles   = [ ];
	private string $role    = '';
	private bool   $booted  = false;

	# ---------------------------------------------------------------
	public function __construct ( \main $main ) {
		parent::__construct( $main );
		$this->profile->data = [ 'login' => '' ];
	}

	# ---------------------------------------------------------------
	#   boot — читает cookie visuo_sid, поднимает сессию.
	#   Идемпотентен.
	# ---------------------------------------------------------------
	public function boot ( ) : void {
		if ( $this->booted ) return;

		if ( $this->dp === null ) {
			$cur = $this->main->dataprovider->current ?? null;
			if ( $cur instanceof dataprovider ) {
				$this->dp = $cur->user;
			}
		}
		if ( $this->dp === null ) { $this->booted = true; return; }

		$token = (string) ( $_COOKIE[ 'visuo_sid' ] ?? '' );
		if ( $token === '' ) { $this->booted = true; return; }

		$sess = $this->dp->session_find( $token );
		if ( empty( $sess ) ) {
			$this->clear_cookie( );
			$this->booted = true;
			return;
		}

		$u = $this->dp->profile_full( (int) $sess[ 'user_id' ] );
		if ( empty( $u ) ) {
			$this->clear_cookie( );
			$this->booted = true;
			return;
		}

		$this->current       = $u;
		$this->profile->data = $u;
		$this->roles         = $this->dp->roles( (int) $u[ 'id' ] );
		$this->role          = $this->pick_role( $this->roles );
		$this->booted        = true;
	}

	private function pick_role ( array $roles ) : string {
		$out = '';
		foreach ( $roles as $r ) {
			$c = (string) ( $r[ 'code' ] ?? '' );
			if ( $c === 'admin' ) return 'admin';
			if ( $c === 'editor' && $out === '' ) $out = 'editor';
			if ( $c === 'reader' && $out === '' ) $out = 'reader';
		}
		return $out;
	}

	# ---------------------------------------------------------------
	#   API
	# ---------------------------------------------------------------
	public function is_guest ( ) : bool { $this->boot( ); return $this->current === null; }
	public function is_auth  ( ) : bool { $this->boot( ); return $this->current !== null; }
	public function id ( )      : int    { return (int) ( $this->current[ 'id' ]    ?? 0 ); }
	public function login ( )   : string { return (string) ( $this->current[ 'login' ] ?? '' ); }
		public function data ( ) : array {
		$this->boot( );
		return $this->current ?? [ ];
	}
	public function role ( )    : string { return $this->role; }

	public function tz ( ) : string {
		$tz = (string) ( $this->profile->data[ 'timezone' ] ?? 'Europe/Moscow' );
		return in_array( $tz , timezone_identifiers_list( ) , true ) ? $tz : 'Europe/Moscow';
	}

	public function lang ( ) : string {
		return (string) ( $this->profile->data[ 'lang' ] ?? 'ru-ru' );
	}

	public function has_role ( string $code ) : bool {
		foreach ( $this->roles as $r ) {
			if ( ( $r[ 'code' ] ?? '' ) === $code ) return true;
		}
		return false;
	}

	private const PERMISSIONS = [
		'admin'  => [ 'user.*' , 'doc.*' , 'news.*' , 'file.*' , 'settings.*' ] ,
		'editor' => [ 'doc.create' , 'doc.edit' , 'news.create' , 'news.edit' ] ,
		'reader' => [ 'doc.view' , 'news.view' ] ,
	];

	public function can ( string $action ) : bool {
		if ( $this->role === '' ) return false;
		foreach ( self::PERMISSIONS[ $this->role ] ?? [ ] as $p ) {
			if ( $p === $action ) return true;
			if ( str_ends_with( $p , '.*' ) ) {
				$prefix = substr( $p , 0 , -2 );
				if ( str_starts_with( $action , $prefix . '.' ) ) return true;
			}
		}
		return false;
	}

	# ---------------------------------------------------------------
	#   authorize
	# ---------------------------------------------------------------
	public function authorize ( string $login , string $password , bool $remember = false ) : bool {
		if ( $this->dp === null ) return false;

		$ip = (string) ( $_SERVER[ 'REMOTE_ADDR' ] ?? '' );
		if ( ! $this->dp->check_bruteforce( $login , $ip ) ) return false;

		$u = $this->dp->by_login( $login );
		if ( empty( $u ) ) {
			$this->dp->record_login_attempt( $login , $ip , false );
			return false;
		}
		if ( ! password_verify( $password , (string) $u[ 'password_hash' ] ) ) {
			$this->dp->record_login_attempt( $login , $ip , false );
			return false;
		}
		if ( ( $u[ 'status' ] ?? 'active' ) !== 'active' ) {
			$this->dp->record_login_attempt( $login , $ip , false );
			return false;
		}

		$token = bin2hex( random_bytes( 32 ) );
		$ttl   = $remember ? 86400 * 30 : 86400;
		$ua    = (string) ( $_SERVER[ 'HTTP_USER_AGENT' ] ?? '' );

		$this->dp->session_create( (int) $u[ 'id' ] , $token , $ip , $ua , $ttl );
		$this->dp->record_login_attempt( $login , $ip , true );
		$this->dp->touch_login( (int) $u[ 'id' ] );

		$this->set_cookie( $token , $remember ? time( ) + $ttl : 0 );

		$this->booted = false;
		$this->boot( );
		return true;
	}

	# ---------------------------------------------------------------
	#   register
	# ---------------------------------------------------------------
	public function register ( array $data ) : array {
		if ( $this->dp === null ) return [ 'error' => 'ERROR_500_TEXT' , 'user_id' => 0 ];

		$login = trim( (string) ( $data[ 'login' ]            ?? '' ) );
		$email = trim( (string) ( $data[ 'email' ]            ?? '' ) );
		$pwd   = (string) ( $data[ 'password' ]               ?? '' );
		$pwd2  = (string) ( $data[ 'password_confirm' ]       ?? '' );
		$disp  = trim( (string) ( $data[ 'display_name' ]     ?? '' ) );

		if ( ! preg_match( '/^[a-zA-Z0-9_]{3,32}$/' , $login ) ) return [ 'error' => 'ERROR_REGISTER_LOGIN_FORMAT' , 'user_id' => 0 ];
		if ( ! filter_var( $email , FILTER_VALIDATE_EMAIL ) )     return [ 'error' => 'ERROR_REGISTER_EMAIL_FORMAT' , 'user_id' => 0 ];
		if ( strlen( $pwd ) < 8 )                                 return [ 'error' => 'ERROR_REGISTER_PASSWORD_SHORT' , 'user_id' => 0 ];
		if ( $pwd !== $pwd2 )                                     return [ 'error' => 'ERROR_REGISTER_PASSWORD_MISMATCH' , 'user_id' => 0 ];
		if ( $this->dp->exists( $login ) )                        return [ 'error' => 'ERROR_REGISTER_LOGIN_TAKEN' , 'user_id' => 0 ];

		$hash = password_hash( $pwd , PASSWORD_DEFAULT );
		$id   = $this->dp->create( $login , $hash , $email , $disp );
		if ( $id <= 0 ) return [ 'error' => 'ERROR_500_TEXT' , 'user_id' => 0 ];

		$this->dp->assign_role( $id , 'reader' );
		return [ 'error' => null , 'user_id' => $id ];
	}

	# ---------------------------------------------------------------
	#   logout
	# ---------------------------------------------------------------
	public function logout ( ) : void {
		if ( $this->dp !== null ) {
			$token = (string) ( $_COOKIE[ 'visuo_sid' ] ?? '' );
			if ( $token !== '' ) $this->dp->session_drop( $token );
		}
		$this->clear_cookie( );
		$this->current       = null;
		$this->profile->data = [ 'login' => '' ];
		$this->roles         = [ ];
		$this->role          = '';
	}

	# ---------------------------------------------------------------
	#   profile updates
	# ---------------------------------------------------------------
	public function update_timezone ( string $tz ) : bool {
		if ( $this->dp === null || $this->is_guest( ) ) return false;
		if ( ! in_array( $tz , timezone_identifiers_list( ) , true ) ) return false;
		$this->dp->update_timezone( $this->id( ) , $tz );
		$this->profile->data[ 'timezone' ] = $tz;
		return true;
	}

	public function update_lang ( string $lang ) : bool {
		if ( $this->dp === null || $this->is_guest( ) ) return false;
		if ( ! in_array( $lang , [ 'ru-ru' , 'en-en' , 'ch-ch' ] , true ) ) return false;
		$this->dp->update_lang( $this->id( ) , $lang );
		$this->profile->data[ 'lang' ] = $lang;
		return true;
	}

	# ---------------------------------------------------------------
	#   cookie
	# ---------------------------------------------------------------
	private function set_cookie ( string $token , int $expires ) : void {
		if ( headers_sent( ) ) return;
		setcookie( 'visuo_sid' , $token , [
			'expires'  => $expires ,
			'path'     => '/' ,
			'httponly' => true ,
			'samesite' => 'Lax' ,
			'secure'   => ( ( $_SERVER[ 'HTTPS' ] ?? '' ) === 'on' ) ,
		] );
	}

	private function clear_cookie ( ) : void {
		$this->set_cookie( '' , time( ) - 3600 );
	}
}
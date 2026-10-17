<?PHP

class user {
	# --- refs ---
	public \main $main;
	public user_profile $profile;
	
	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->profile = new user_profile( $this );
	}
	
	public function is_guest ( ) : bool {
		return (bool) $this->profile->get( 'guest' );
	}
	
	public function authorized ( ) : void {
		$this->profile->init( );
	}
}


class user_profile {
	# --- refs ---
	public \main $main;
	public user $user;
	
	# --- state ---
	public bool   $guest = true;
	public string $role = '';
	public string $id = '';
	public string $status = '';
	public string $login = '';
	public string $password = '';
	public string $hash = '';
	public array  $data = [ ];
	
	# --- methods ---
	public function __construct ( user $user ) {
		$this->main = $user->main;
		$this->user = $user;
	}
	
	public function init ( ) : void {
		$main = $this->main;
		
		$cookies = [
			'login'    => $_COOKIE[ 'login' ]    ?? '' ,
			'password' => $_COOKIE[ 'password' ] ?? '' ,
			'hash'     => $_COOKIE[ 'hash' ]     ?? '' ,
		];
		
		if ( $cookies[ 'login' ] === '' && $cookies[ 'password' ] === '' && $cookies[ 'hash' ] === '' ) {
			return;
		}
		
		$row = $main->db->select(
			'SELECT * FROM toolsusers WHERE login=%s AND password=%s' ,
			[ $cookies[ 'login' ] , $cookies[ 'password' ] ]
		)->one( )->row( );
		
		if ( empty( $row ) ) {
			$this->guest = true;
			$this->data[ 'login' ] = 'Гость';
			return;
		}
		
		$this->data = $row;
		$this->guest = false;
		$this->id = (string) ( $row[ 'id' ] ?? '' );
		$this->login = (string) ( $row[ 'login' ] ?? '' );
		$this->password = (string) ( $row[ 'password' ] ?? '' );
		$this->status = (string) ( $row[ 'status' ] ?? '' );
	}
	
	public function login ( array $data , ?array $formcheck = null ) : void {
		$main = $this->main;
		$library = $main->library;
		$url = $library->url( );
		
		$id       = $data[ 'id' ]       ?? '' ;
		$login    = $data[ 'login' ]    ?? '' ;
		$password = $data[ 'password' ] ?? '' ;
		$hash     = md5( $login . $password . time( ) );
		
		$remember = false;
		if ( $formcheck !== null && isset( $formcheck[ 'remember' ][ 'value' ] ) ) {
			$remember = $formcheck[ 'remember' ][ 'value' ] == 'true';
		}
		
		if ( $remember ) {
			$expire_time = time( ) + 7 * 24 * 60 * 60;
			$cookie_expire = $expire_time;
		} else {
			$expire_time = time( ) + 60;
			$cookie_expire = 0;
		}
		$expire_timestamp = date( 'Y-m-d H:i:s' , $expire_time );
		
		$main->db->insert( 'toolsuserslogin' , [
			'status'                 => 'activated' ,
			'created_users_id'       => $id ,
			'login'                  => $login ,
			'hash'                   => $hash ,
			'hash_expired_time'      => $expire_time ,
			'hash_expired_timestamp' => $expire_timestamp ,
		] )->affected( );
		
		$host = explode( '.' , strrev( $_SERVER[ 'HTTP_HOST' ] ?? '' ) , 3 );
		$domain = '.' . strrev( implode( '.' , [ $host[ 0 ] ?? '' , $host[ 1 ] ?? '' ] ) );
		$path = '/';
		$secure = true;
		$httponly = true;
		
		setcookie( 'login'    , $login    , $cookie_expire , $path , $domain , $secure , $httponly );
		setcookie( 'password' , $password , $cookie_expire , $path , $domain , $secure , $httponly );
		setcookie( 'hash'     , $hash     , $cookie_expire , $path , $domain , $secure , $httponly );
	}
	
	public function logout ( ) : void {
		$main = $this->main;
		$url = $main->library->url( );
		$url_allias = $url->static_allias;
		
		$exp = time( ) - 3600;
		$path = '/';
		$domain = $url_allias[ 0 ] ?? '';
		
		setcookie( 'login'    , '' , $exp , $path , $domain );
		setcookie( 'password' , '' , $exp , $path , $domain );
		setcookie( 'hash'     , '' , $exp , $path , $domain );
	}
	
	public function set ( string $var , mixed $value ) : void {
		$this->{$var} = $value;
	}
	
	public function get ( string $var ) : mixed {
		return $this->{$var};
	}
	
	public function data ( string $var ) : mixed {
		return $this->data[ $var ] ?? null;
	}
}
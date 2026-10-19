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
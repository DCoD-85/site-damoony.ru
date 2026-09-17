<?PHP

class user {
	# vars
	public main $main;
	public array $settings;
	public user_profile $profile;
	public array $rows = [ ];
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;

		$this->profile = new user_profile( $this );
	}

	public function is_guest ( ) {
		return $this->profile->get( 'guest' );
	}
	
	public function authorized ( ) {
		$main = $this->main;
		$profile = $this->profile;
		$this->profile->init( );
	}
	
}

class user_profile {
	# vars
	public main $main;
	public user $user;
	public bool $guest = true;
	public string $role = '';
	public string $id = '';
	public string $status = '';
	public string $login = '';
	public string $password = '';
	public string $hash = '';
	public array $data = [ ];
	# methods
	public function __construct ( user $user ) {
		$this->main = $user->main;
		$this->user = $user;
	}
	public function init ( ) {
		$main = $this->main;
		$login = [
			'login'=>$_COOKIE[ 'login' ] ?: '' ,
			'password'=>$_COOKIE[ 'password' ] ?: '' ,
			'hash'=>$_COOKIE[ 'hash' ] ?: ''
		];

		if( ! ( empty( $login[ 'login' ] ) && empty( $login[ 'password' ] ) && empty( $login[ 'hash' ] ) ) ){
			
			$db = $this->main->db->db( );
			# print '<div>db status: ' . $db->log( ) . '</div>';
			$query = $db->query( );
			$query->select( 'select * from toolsusers tu WHERE tu.login=%s AND tu.password=%s' , [ $login[ 'login' ] , $login[ 'password' ] ] , limit : true , limitstep : 1 );
			$result = $query->result( )[ 0 ];
			#
			if( empty( $result ) ){
				$this->guest = true;
				$this->data[ 'login' ] = 'Гость';
			} else if( ! empty( $result ) ){
				# Данные
				$this->data = $result;
				$this->guest = false;
			}
		}
	}

	public function login ( array $data , array $formcheck ) {
		# таблица активной авторизации
		# 1. активные записи делаем не активными
		# 2. создаем активную запись
		# 3. создаем записи cookie
		
		$main = $this->main;
		$messages = $main->messages;

		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		# formcheck
		# # login , password , remember
		# result
		$_id = $data[ 'id' ];
		$_login = $data[ 'login' ];
		$_password = $data[ 'password' ];
		$_hash = md5( $_login . $_password . time( ) );
		
		$strres = '';
		foreach( $data as $key=>$value ){
			$strres .= $key . ' = ' . $value . '<br />';
		}
		$messages->add( 'success' , '' , $strres );

		if( $formcheck[ 'remember' ][ 'value' ] == 'true' ){
			$_hash_expired_time = time( ) + 7*24*60*60;
			$_hash_expired_timestamp = date("Y-m-d H:i:s" , $_hash_expired_time );
			$exp = $_hash_expired_time;
		} else{
			$_hash_expired_time = time( ) + ( 1 * 60);
			$_hash_expired_timestamp = date("Y-m-d H:i:s" , $_hash_expired_time );
			$exp = 0;
		}
		$path = '/';
		$domain = $url_allias[ 0 ];
		

		# Изменяем и добавляем запись в БД
		$insert = [
			'status'=>'activated' ,
			'created_users_id'=>$_id ,
			'login'=>$_login ,
			'hash'=>$_hash ,
			'hash_expired_time'=>$_hash_expired_time ,
			'hash_expired_timestamp'=>$_hash_expired_timestamp ,
		];

		$db = $main->db->db( );
		$query = $db->query( );
		$query->insert( 'toolsuserslogin' ,
			[ 'status' , 'created_users_id' , 'login' , 'hash' , 'hash_expired_time' , 'hash_expired_timestamp' ] ,
			[ $insert[ 'status' ] , $insert[ 'created_users_id' ] , $insert[ 'login' ] , $insert[ 'hash' ] , $insert[ 'hash_expired_time' ] , $insert[ 'hash_expired_timestamp' ] ] ,
			[ '%s' , '%d' , '%s' , '%s' , '%s' , '%s' ] );
		
		$path = '/';
		$host = explode( '.' , strrev( $_SERVER[ 'HTTP_HOST' ] ) , 3 );
		$domain = '.' . strrev( implode( '.' , [ $host[ 0 ] , $host[ 1 ] ] ) );
		$secure = true;
		$httponly = true;

		setcookie( 'login' , $_login , $exp , $path , $domain , $secure , $httponly );
		setcookie( 'password' , $_password , $exp , $path , $domain , $secure , $httponly );
		setcookie( 'hash' , $_hash , $exp , $path , $domain , $secure , $httponly );
	}

	public function logout ( ) {
		$main = $this->main;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		

		$_login = '';
		$_password = '';
		$_hash = '';
		$exp = time( ) - 3600;
		$path = '/';
		$domain = $url_allias[ 0 ];

		setcookie( 'login' , $_login , $exp , $path , $domain );
		setcookie( 'password' , $_password , $exp , $path , $domain );
		setcookie( 'hash' , $_hash , $exp , $path , $domain );
	}

	public function set ( string $var , mixed $value ) {
		$this->{$var} = $value;
	}
	
	public function get ( string $var ) {
		return $this->{$var};
	}
	public function data ( string $var ) {
		return $this->data[ $var ];
	}
	
	public function return ( ) {
		return [
			'status'=>$this->status ,
			'guest'=>$this->guest ,
			'login'=>$this->login ,
			'password'=>$this->password
		];
	}
}

?>
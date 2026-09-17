<?PHP

class algoritm_lang_redirect {
	# vars
	public algoritm $algoritm;
	public visuotag $vt;
	# public array $available = [ 'index' , '([a-z]{2}-[a-z]{2})|lang' ];
	public bool $search = true;
	public bool $default = false;
	public bool $auth = false;
	public array $args = [ ];
	public string $path_object;
	# methods
	public function __construct ( algoritm $algoritm , array $args = [ ] ) {
		$this->algoritm = $algoritm;
		$this->args = $args;
	}
	
	public function args ( array $args ) {
		$this->args = $args;
	}
	public function algoritm ( ) {
	}
	public function release ( ) {
		$this->available = [ 'index' , 'form' , 'login' , 'logout' , 'redirect' , 'development.admin.panel' ];
		$this->default = true;

		$this->algoritm->release_( $this );
	}
	
	public function default ( ) {
		$jsonencode = fn( $value ) => str_replace( '"' , '&quot;' , json_encode( $value ) );

		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_allias;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$center = $main->alliasglobal->center;

		$db = $this->algoritm->main->db->db( );
		$db->open( 'login_damoony' );

		
		# $query = 'INSERT INTO `toolsuserslogin` ( `status` , `created_users_id` , `login` , `hash` , `hash_expired_time` ) VALUES ( "activated" , 1 , "user_' . rand( 2 , 999999999 ) . '" , "qwe" , "1234567890" );';
		# print $query;
		# mysqli_query( $db->db_link , $query );
		$query = $db->query( );
		$query->select( 'SELECT * FROM toolsusers tu WHERE tu.login=%s AND tu.password=%s' , [ 'admin' , md5( 'admin' ) ] );
		# $result = $query->result( );
		
		$query = $db->query( );
		$query->select( 'SELECT * FROM toolsusers tu WHERE tu.login=%s AND tu.password=%s' , [ 'admin' , md5( 'admin' ) ] );
		# $result = $query->result( );
		
		$query = $db->query( );
		$query->select( 'SELECT * FROM toolsusers tu WHERE tu.login=%s AND tu.password=%s' , [ 'admin' , md5( 'admin' ) ] );
		$result = $query->result( );
		
		$res = '';
		if( ! empty( $result[ 0 ] ) ){
			# print '<div>result: </div>';
			foreach( $result[ 0 ] as $key=>$value ) {
				$res .= '<div>' . $key . ' = ' . $value . '</div>';
			}
		}

		
		$log = $db->log( );
		
		$vt_center = new visuotag( $this->visuorelease );
		$vt_center->tag( 'div' )->content( 'log: ' . $log )->content( 'res: ' . $res );

		$center->visuotag( )->append( $vt_center );
	}
	
}

?>
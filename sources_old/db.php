<?PHP

class db {
	# vars
	public null|main $main;
	public db_class $db_class;
	public string $db_type = 'mysqli';
	public db_config $db_config;
	public null|db_config_row $db_config_default = null;
	# methods
	public function __construct ( null|main $main ) {
		$this->main = $main;
		
		$this->init_config( );
		$this->init_class( );
	}
	public function init_config ( ) {
		$this->db_config = new db_config( $this );
		$dbData = [ ];
		// Путь к настройкам Базы Данных
		$path = dirname( __DIR__ ) . DS . 'secure' . DS . 'db.config';
		if( ! is_file( $path ) ) error_file( $path , __FILE__ , __LINE__ 	);
		else {
			$dom = simplexml_load_file( $path );
			foreach( $dom->base as $k=>$v ){
				$disabled = strtolower( $v[ 'disabled' ] );
				$default = strtolower( $v[ 'default' ] );
				
				
				if( $disabled == 'false' ){
					$disabled = (string)$v[ 'disabled' ];
					$default = (string)$v[ 'default' ];
					$localname = (string)$v->localname;
					$dbname = (string)$v->dbname;
					$dbhost = (string)$v->dbhost;
					$username = (string)$v->username;
					$password = (string)$v->password;
					$comment = (string)$v->comment;
					
					$f = $this->db_config->row( $localname );
					$f->add( $localname , $dbname , $dbhost , $username , $password , $comment );

					if( $default === 'true' ) $this->db_config_default = $f;
				}
				else if( $disabled == 'true' ){
				}
				else {
				}
			}

			if( $this->db_config_default === null ){
				foreach( $this->db_config->db_config_rows as $key=>$value ){
					$this->db_config_default = $value;
					break;
				}
			}
		}
	}
		
	public function init_class ( ) {
		$path_config = __DIR__ . DS . 'config' . DS;
		$path_db = __DIR__ . DS . 'db' . DS;
		
		if( $this->db_type == 'mysqli' ){
			$path_db_class = $path_db . 'db_mysqli.php';
			$path_db_query = $path_db . 'db_mysqli_query.php';
		}

		( include_once $path = $path_db_class ) or error_file( $path , __FILE__ , __LINE__ ); 
		( include_once $path = $path_db_query ) or error_file( $path , __FILE__ , __LINE__ ); 

		$this->db_class = new db_class( $this );
	}
	
	public function get ( string $localname = '' , bool $default = true ) {
		$return = $this->db_config->get( $localname ) ?: ( $default ? $this->db_config_default : error( '!' , '!' , __FILE__ , __LINE__ ) );
		
		return $return;
	}
	public function db ( ){
		return $this->db_class;
	}
	public function open ( string $localname = '' , bool $reconect = false ) {
		$this->db_class->open( $localname , $reconect );
		return $this->db_class;
	}
	public function list_as_tag ( ) {
		return $this->db_config->list_as_tag( );
	}
}

class db_config {
	# vars
	public db $db;
	public array $db_config_rows = [ ];
	# methods
	public function __construct ( db $db ) {
		$this->db = $db;
	}
	public function row ( string $localname ) {
		return $this->db_config_rows[ $localname ] = new db_config_row( $this );
	}
	public function get ( string $localname ) {
		return $this->db_config_rows[ $localname ] ?: null;
	}
	public function list_as_tag ( ) {
		$vt = new visuotag( );
		foreach( $this->db_config_rows as $key=>$value ){
			$vt->append( $value->list_as_tag( ) );
		}
		return $vt;
	}
}

class db_config_row {
	# vars
	public db_config $db_config;
	public string $localname;
	public string $dbname;
	public string $dbhost;
	public string $username;
	public string $password;
	public string $comment;
	# methods
	public function __construct ( db_config $db_config ) {
		$this->db_config = $db_config;
	}
	public function add ( string $localname ,
												string $dbname ,
												string $dbhost ,
												string $username ,
												string $password ,
												string $comment ) {
		$this->localname = $localname;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;
		$this->username = $username;
		$this->password = $password;
		$this->comment = $comment;
	}
	public function list_as_tag ( ) {
		$vt = new visuotag( );
		$vt->tag( 'div' )
			->tag( 'div' )->content( 'localname: ' . $this->localname )
			->parent( )->tag( 'div' )->content( 'dbname: ' . $this->dbname )
			->parent( )->tag( 'div' )->content( 'dbhost: ' . $this->dbhost )
			->parent( )->tag( 'div' )->content( 'username: ' . $this->username )
			->parent( )->tag( 'div' )->content( 'password: ' . $this->password )
			->parent( )->tag( 'div' )->content( 'comment: ' . $this->comment );
		
			return $vt;
	}
}

?>
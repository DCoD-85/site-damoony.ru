<?PHP

class db_mysqli implements db_driver_interface {
	# --- refs ---
	public \db $db;
	public \main $main;
	public db_config $config;
	
	# --- connection ---
	public null|\mysqli $link = null;
	public null|db_config_row $current = null;
	
	# --- last state ---
	public string $last_error = '';
	public int $last_affected = 0;
	public int $last_insert_id = 0;
	
	# --- methods ---
	public function __construct ( \db $db ) {
		$this->db = $db;
		$this->main = $db->main;
		$this->config = new db_config( $db );
	}
	
	# --- open connection ---
	public function open ( string $localname = '' , bool $reconnect = false ) : void {
		$row = $localname === '' ? $this->config->default( ) : $this->config->row( $localname );
		
		if ( $row === null ) {
			error( 'DB error' , 'Config not found: localname=' . $localname , __FILE__ , __LINE__ );
			return;
		}
		
		# already connected to same base, no reconnect needed
		if ( ! $reconnect && $this->link !== null && $this->current !== null
			&& $this->current->localname === $row->localname ) {
			return;
		}
		
		if ( $this->link !== null ) $this->close( );
		
		$this->link = @new \mysqli( $row->dbhost , $row->username , $row->password , $row->dbname );
		
		if ( $this->link->connect_errno ) {
			$this->last_error = $this->link->connect_error;
			error( 'DB connection failed' , $this->last_error , __FILE__ , __LINE__ );
			$this->link = null;
			return;
		}
		
		# utf8mb4
		$this->link->set_charset( 'utf8mb4' );
		$this->link->query( "SET collation_connection = 'utf8mb4_unicode_ci'" );
		
		$this->current = $row;
	}
	
	# --- close connection ---
	public function close ( ) : void {
		if ( $this->link !== null ) {
			@$this->link->close( );
			$this->link = null;
			$this->current = null;
		}
	}
	
	# --- get raw link ---
	public function link ( ) : mixed {
		return $this->link;
	}
	
	# --- status as string ---
	public function status ( ) : string {
		return $this->link !== null ? 'connected: ' . $this->current->localname : 'null';
	}
	
	# --- last error ---
	public function error ( ) : string {
		return $this->link !== null ? $this->link->error : $this->last_error;
	}
	
	# --- affected rows ---
	public function affected ( ) : int {
		return $this->link !== null ? (int) $this->link->affected_rows : 0;
	}
	
	# --- last insert id ---
	public function last_id ( ) : int {
		return $this->link !== null ? (int) $this->link->insert_id : 0;
	}
	
	# --- escape string ---
	public function escape ( string $string ) : string {
		return $this->link !== null ? $this->link->real_escape_string( $string ) : '';
	}	

	# --- legacy / convenience: return new query object ---
	public function query ( ) : db_mysqli_query {
		$class = 'db_' . $this->db->db_type . '_query';
		return new $class( $this );
	}
}
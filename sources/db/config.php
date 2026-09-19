<?PHP

class db_config {
	# --- refs ---
	public \db $db;
	public \main $main;
	
	# --- storage ---
	public array $rows = [ ];            // localname => db_config_row
	public null|db_config_row $default = null;
	
	# --- file path ---
	public string $path = '';
	
	# --- methods ---
	public function __construct ( \db $db ) {
		$this->db = $db;
		$this->main = $db->main;
		$this->path = dirname( __DIR__ , 2 ) . DS . 'secure' . DS . 'db.config';
		$this->init( );
	}
	
	# read XML config and build rows
	public function init ( ) : void {
		if ( ! is_file( $this->path ) ) {
			error_file( $this->path , __FILE__ , __LINE__ );
			return;
		}
		
		$xml = @simplexml_load_file( $this->path );
		if ( $xml === false ) {
			error( 'DB config error' , 'Cannot parse ' . $this->path , __FILE__ , __LINE__ );
			return;
		}
		
		foreach ( $xml->base as $base ) {
			$disabled = strtolower( (string) $base[ 'disabled' ] );
			if ( $disabled === 'true' ) continue;
			
			$row = new db_config_row( $this );
			$row->localname = (string) $base->localname;
			$row->dbname    = (string) $base->dbname;
			$row->dbhost    = (string) $base->dbhost;
			$row->username  = (string) $base->username;
			$row->password  = (string) $base->password;
			$row->comment   = (string) $base->comment;
			
			$this->rows[ $row->localname ] = $row;
			
			if ( strtolower( (string) $base[ 'default' ] ) === 'true' ) {
				$this->default = $row;
			}
		}
		
		# if no default declared - first row
		if ( $this->default === null && ! empty( $this->rows ) ) {
			$this->default = reset( $this->rows );
		}
	}
	
	# get row by localname, or null
	public function row ( string $localname ) : ?db_config_row {
		return $this->rows[ $localname ] ?? null;
	}
	
	# get default row
	public function default ( ) : ?db_config_row {
		return $this->default;
	}
	
	# get all rows
	public function all ( ) : array {
		return $this->rows;
	}
	
	# has row?
	public function has ( string $localname ) : bool {
		return isset( $this->rows[ $localname ] );
	}
}


class db_config_row {
	# --- refs ---
	public db_config $config;
	
	# --- data ---
	public string $localname = '';
	public string $dbname    = '';
	public string $dbhost    = '';
	public string $username  = '';
	public string $password  = '';
	public string $comment   = '';
	
	# --- methods ---
	public function __construct ( db_config $config ) {
		$this->config = $config;
	}
	
	# check if required fields filled
	public function is_valid ( ) : bool {
		return $this->dbname !== '' && $this->dbhost !== '' && $this->username !== '';
	}
}
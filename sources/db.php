<?PHP

class db {
	# --- refs ---
	public \main $main;
	public string $db_type = 'mysqli';
	public db_driver_interface $driver;
	public null|db_config $config = null;
	
	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->init( );
	}
	
	public function init ( ) : void {
		$this->read_db_type( );
		$this->load_files( );
		$this->create_driver( );
	}
	
	private function read_db_type ( ) : void {
		$path = dirname( __DIR__ ) . DS . 'secure' . DS . 'db.secure';
		if ( ! is_file( $path ) ) return;
		
		$json = @file_get_contents( $path );
		if ( $json === false ) return;
		
		$data = @json_decode( $json , true );
		if ( ! is_array( $data ) ) return;
		
		if ( isset( $data[ 'db_type' ] ) && is_string( $data[ 'db_type' ] ) && $data[ 'db_type' ] !== '' ) {
			$this->db_type = $data[ 'db_type' ];
		}
	}
	
	private function load_files ( ) : void {
		$db_dir = __DIR__ . DS . 'db';
		
		( include_once $db_dir . DS . 'driver.php' )    or error_file( $db_dir . DS . 'driver.php' , __FILE__ , __LINE__ );
		( include_once $db_dir . DS . 'query.php' )     or error_file( $db_dir . DS . 'query.php' , __FILE__ , __LINE__ );
		( include_once $db_dir . DS . 'exception.php' ) or error_file( $db_dir . DS . 'exception.php' , __FILE__ , __LINE__ );
		( include_once $db_dir . DS . 'config.php' )    or error_file( $db_dir . DS . 'config.php' , __FILE__ , __LINE__ );
		
		$driver_dir = $db_dir . DS . $this->db_type;
		( include_once $driver_dir . DS . 'driver.php' ) or error_file( $driver_dir . DS . 'driver.php' , __FILE__ , __LINE__ );
		( include_once $driver_dir . DS . 'query.php' )  or error_file( $driver_dir . DS . 'query.php' , __FILE__ , __LINE__ );
	}
	
	private function create_driver ( ) : void {
		$class = 'db_' . $this->db_type;
		
		if ( ! class_exists( $class ) ) {
			error( 'DB error' , 'Driver class not found: ' . $class , __FILE__ , __LINE__ );
			return;
		}
		
		$this->driver = new $class( $this );
		$this->config = $this->driver->config;
	}
	
	# ===============================================================
	#   CONVENIENCE / LEGACY
	# ===============================================================
	
	public function db ( ) : db_driver_interface {
		return $this->driver;
	}
	
	public function open ( string $localname = '' , bool $reconnect = false ) : db_driver_interface {
		$this->driver->open( $localname , $reconnect );
		return $this->driver;
	}
	
	public function get ( string $localname = '' ) : ?db_config_row {
		$row = $this->config->row( $localname );
		return $row ?: $this->config->default( );
	}
	
	public function config ( ) : db_config {
		return $this->config;
	}
	
	public function affected ( ) : int {
		return $this->driver->affected( );
	}
	
	public function last_id ( ) : int {
		return $this->driver->last_id( );
	}
	
	public function escape ( string $string ) : string {
		return $this->driver->escape( $string );
	}
	
    public function schema ( ) : db_schema {
		if ( ! class_exists( 'db_schema' ) ) {
			$path = __DIR__ . DS . 'db' . DS . 'schema.php';
			( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
		}
		return new db_schema( $this );
	}

	# ===============================================================
	#   NEW API
	# ===============================================================
	
	public function query ( string $sql , array $values = [ ] , array $types = [ ] ) : db_query_interface {
		$class = 'db_' . $this->db_type . '_query';
		$query = new $class( $this->driver );
		return $query->query( $sql , $values , $types );
	}
	
	public function select ( string $sql , array $values = [ ] , array $types = [ ] ) : db_query_interface {
		return $this->query( $sql , $values , $types );
	}
	
	public function insert ( string $table , array $data , array $types = [ ] ) : db_query_interface {
		$columns = array_keys( $data );
		$values  = array_values( $data );
		
		if ( empty( $types ) ) {
			foreach ( $values as $v ) {
				$types[ ] = is_int( $v ) ? '%d' : '%s';
			}
		}
		
		$cols = implode( ' , ' , array_map( fn( $c ) => '`' . $c . '`' , $columns ) );
		$placeholders = implode( ' , ' , $types );
		$sql = 'INSERT INTO `' . $table . '` ( ' . $cols . ' ) VALUES ( ' . $placeholders . ' )';
		
		return $this->query( $sql , $values , $types );
	}
	
	public function update ( string $table , array $data , array $where , array $types = [ ] ) : db_query_interface {
		$set       = [ ];
		$auto      = [ ];

		foreach ( $data as $col => $val ) {
			$ph     = is_int( $val ) ? '%d' : '%s';
			$set[ ] = '`' . $col . '`=' . $ph;
			$auto[ ] = $ph;
		}

		$where_sql = [ ];
		foreach ( $where as $col => $val ) {
			$ph          = is_int( $val ) ? '%d' : '%s';
			$where_sql[ ] = '`' . $col . '`=' . $ph;
			$auto[ ]      = $ph;
		}

		$sql = 'UPDATE `' . $table . '` SET ' . implode( ' , ' , $set ) . ' WHERE ' . implode( ' AND ' , $where_sql );
		$values = array_merge( array_values( $data ) , array_values( $where ) );

		# если $types не задан — используем auto (%s/%d по типу значения)
		$ph = empty( $types ) ? $auto : $types;

		return $this->query( $sql , $values , $ph );
	}

	public function delete ( string $table , array $where , array $types = [ ] ) : db_query_interface {
		$where_sql = [ ];
		$auto      = [];

		foreach ( $where as $col => $val ) {
			$ph           = is_int( $val ) ? '%d' : '%s';
			$where_sql[ ] = '`' . $col . '`=' . $ph;
			$auto[ ]      = $ph;
		}

		$sql = 'DELETE FROM `' . $table . '` WHERE ' . implode( ' AND ' , $where_sql );
		$values = array_values( $where );

		$ph = empty( $types ) ? $auto : $types;

		return $this->query( $sql , $values , $ph );
	}
}
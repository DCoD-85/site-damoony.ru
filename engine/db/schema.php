<?PHP

# ===============================================================
#   col() — helper for column definition (used with schema()->create)
#   Returns array that sql_create() understands.
#   Two ways to define columns in create():
#     - 'name' => 'SQL DEFINITION'      (string)
#     - col( name: 'name' , type: ... ) (array from col())
#   Both can be mixed in one array.
# ===============================================================
if ( ! function_exists( 'col' ) ) {
	function col (
		string $name ,
		string $type = 'string' ,
		int    $length = 0 ,
		bool   $nullable = true ,
		bool   $unsigned = false ,
		bool   $auto = false ,
		mixed  $default = null ,
		array  $values = [ ]
	) : array {
		return [
			'name'     => $name ,
			'type'     => $type ,
			'length'   => $length ,
			'nullable' => $nullable ,
			'unsigned' => $unsigned ,
			'auto'     => $auto ,
			'default'  => $default ,
			'values'   => $values ,
		];
	}
}


class db_schema {
	# --- refs ---
	public \db $db;
	public db_driver_interface $driver;
	
	# --- state ---
	private string $action = '';
	private string $table = '';
	private array  $columns = [ ];
	private string $primary = '';
	private array  $indexes = [ ];
	private array  $foreign = [ ];
	private string $engine = 'InnoDB';
	private string $charset = 'utf8mb4';
	private string $collate = 'utf8mb4_unicode_ci';
	private bool   $if_exists = false;
	private bool   $if_not_exists = false;
	private string $new_name = '';
	
	# --- methods ---
	public function __construct ( \db $db ) {
		$this->db = $db;
		$this->driver = $db->driver;
	}
	
	# ===============================================================
	#   CREATE TABLE
	# ===============================================================
	
	public function create (
		string       $table ,
		array        $columns ,
		string|array $primary       = 'id' ,
		array        $indexes       = [ ] ,
		array        $foreign       = [ ] ,
		string       $engine        = 'InnoDB' ,
		string       $charset       = 'utf8mb4' ,
		string       $collate       = 'utf8mb4_unicode_ci' ,
		bool         $if_not_exists = true
	) : self {
		$this->reset( );
		$this->action = 'create';
		$this->table = $table;
		$this->columns = $columns;
		$this->primary = is_array( $primary ) ? implode( ' , ' , $primary ) : $primary;
		$this->indexes = $indexes;
		$this->foreign = $foreign;
		$this->engine = $engine;
		$this->charset = $charset;
		$this->collate = $collate;
		$this->if_not_exists = $if_not_exists;
		return $this;
	}
	
	# ===============================================================
	#   DROP / TRUNCATE / RENAME
	# ===============================================================
	
	public function drop ( string $table , bool $if_exists = true ) : self {
		$this->reset( );
		$this->action = 'drop';
		$this->table = $table;
		$this->if_exists = $if_exists;
		return $this;
	}
	
	public function truncate ( string $table ) : self {
		$this->reset( );
		$this->action = 'truncate';
		$this->table = $table;
		return $this;
	}
	
	public function rename ( string $table , string $new_name ) : self {
		$this->reset( );
		$this->action = 'rename';
		$this->table = $table;
		$this->new_name = $new_name;
		return $this;
	}
	
	# ===============================================================
	#   INTROSPECTION
	# ===============================================================
	
	public function tables ( ) : array {
		$rows = $this->db->select( 'SHOW TABLES' )->result( false );
		return array_map( fn( $r ) => $r[ 0 ] , $rows );
	}
	
	public function exists ( string $table ) : bool {
		$row = $this->db->select(
			'SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE( ) AND table_name = %s' ,
			[ $table ]
		)->row( );
		return ! empty( $row ) && (int) $row[ 'c' ] > 0;
	}
	
	public function columns_of ( string $table ) : array {
		return $this->db->select( 'SHOW COLUMNS FROM `' . $table . '`' )->result( );
	}
	
	# ===============================================================
	#   BUILD / EXECUTE
	# ===============================================================
	
	public function sql ( ) : string {
		switch ( $this->action ) {
			case 'create'   : return $this->sql_create( );
			case 'drop'     : return $this->sql_drop( );
			case 'truncate' : return 'TRUNCATE TABLE `' . $this->table . '`';
			case 'rename'   : return 'RENAME TABLE `' . $this->table . '` TO `' . $this->new_name . '`';
		}
		return '';
	}
	
	public function run ( ) : self {
		$sql = $this->sql( );
		if ( $sql === '' ) return $this;

		$q = $this->db->select( $sql );
		$q->affected( );
		
		$err = $q->error( );
		if ( $err !== '' ) {
			throw new \db_exception( $err , $sql );
		}

		return $this;
	}
	
	public function log ( ) : string {
		return $this->sql( );
	}
	
	# ===============================================================
	#   INTERNAL
	# ===============================================================
	
	private function sql_create ( ) : string {
		$defs = [ ];
		
		foreach ( $this->columns as $key => $definition ) {
			if ( is_array( $definition ) ) {
				# from col() — name is inside the array
				$defs[ ] = $this->col_to_sql( $definition );
			} else {
				# raw SQL string — key is the column name
				$defs[ ] = '`' . $key . '` ' . $definition;
			}
		}
		
		if ( $this->primary !== '' ) {
			$pk = $this->primary;
			if ( strpos( $pk , ',' ) !== false ) {
				$pk = implode( ' , ' , array_map( fn( $c ) => '`' . trim( $c ) . '`' , explode( ',' , $pk ) ) );
			} else {
				$pk = '`' . $pk . '`';
			}
			$defs[ ] = 'PRIMARY KEY ( ' . $pk . ' )';
		}
		
		foreach ( $this->indexes as $idx ) {
			$cols = implode( ' , ' , array_map( fn( $c ) => '`' . $c . '`' , $idx[ 'columns' ] ) );
			$defs[ ] = ( ! empty( $idx[ 'unique' ] ) ? 'UNIQUE KEY' : 'KEY' ) . ' `' . $idx[ 'name' ] . '` ( ' . $cols . ' )';
		}
		
		foreach ( $this->foreign as $fk ) {
			$defs[ ] = 'FOREIGN KEY ( `' . $fk[ 'column' ] . '` ) REFERENCES `' . $fk[ 'table' ] . '` ( `' . $fk[ 'ref' ] . '` )';
		}
		
		$ine = $this->if_not_exists ? 'IF NOT EXISTS ' : '';
		$sql = 'CREATE TABLE ' . $ine . '`' . $this->table . '` ( ' . implode( ' , ' , $defs ) . ' )';
		$sql .= ' ENGINE=' . $this->engine;
		$sql .= ' DEFAULT CHARSET=' . $this->charset;
		$sql .= ' COLLATE=' . $this->collate;
		
		return $sql;
	}
	
	private function sql_drop ( ) : string {
		$ie = $this->if_exists ? 'IF EXISTS ' : '';
		return 'DROP TABLE ' . $ie . '`' . $this->table . '`';
	}
	
	# ===============================================================
	#   COLUMN BUILDER
	# ===============================================================
	
	private function col_to_sql ( array $c ) : string {
		$name     = $c[ 'name' ]     ?? '' ;
		$type     = strtolower( $c[ 'type' ] ?? 'string' );
		$length   = (int) ( $c[ 'length' ]   ?? 0 );
		$nullable = (bool) ( $c[ 'nullable' ] ?? true );
		$unsigned = (bool) ( $c[ 'unsigned' ] ?? false );
		$auto     = (bool) ( $c[ 'auto' ]     ?? false );
		$default  = $c[ 'default' ] ?? null;
		$values   = $c[ 'values' ]  ?? [ ];
		
		$sql_type = '';
		
		switch ( $type ) {
			case 'int' :
			case 'integer' :
			case 'tinyint' :
			case 'smallint' :
			case 'mediumint' :
			case 'bigint' :
				$sql_type = strtoupper( $type ) . ( $length > 0 ? '(' . $length . ')' : '' );
				break;
			case 'string' :
			case 'varchar' :
				$sql_type = 'VARCHAR(' . ( $length > 0 ? $length : 255 ) . ')';
				break;
			case 'char' :
				$sql_type = 'CHAR(' . ( $length > 0 ? $length : 1 ) . ')';
				break;
			case 'text' :
			case 'mediumtext' :
			case 'longtext' :
				$sql_type = strtoupper( $type );
				break;
			case 'enum' :
				$escaped = array_map( fn( $v ) => "'" . addslashes( (string) $v ) . "'" , $values );
				$sql_type = 'ENUM(' . implode( ',' , $escaped ) . ')';
				break;
			case 'bool' :
			case 'boolean' :
				$sql_type = 'TINYINT(1)';
				break;
			case 'timestamp' :
			case 'datetime' :
			case 'date' :
			case 'time' :
				$sql_type = strtoupper( $type );
				break;
			case 'decimal' :
			case 'float' :
			case 'double' :
				$sql_type = strtoupper( $type ) . ( $length > 0 ? '(' . $length . ')' : '' );
				break;
			case 'blob' :
			case 'mediumblob' :
			case 'longblob' :
				$sql_type = strtoupper( $type );
				break;
			case 'json' :
				$sql_type = 'JSON';
				break;
			default :
				$sql_type = strtoupper( $type );
		}
		
		if ( $unsigned ) $sql_type .= ' UNSIGNED';
		if ( $auto )     $sql_type .= ' AUTO_INCREMENT';
		$sql_type .= $nullable ? ' NULL' : ' NOT NULL';
		
		if ( $default !== null ) {
			if ( is_bool( $default ) ) {
				$sql_type .= ' DEFAULT ' . ( $default ? '1' : '0' );
			} else if ( is_int( $default ) || is_float( $default ) ) {
				$sql_type .= ' DEFAULT ' . $default;
			} else if ( is_string( $default ) && strtoupper( $default ) === 'CURRENT_TIMESTAMP' ) {
				$sql_type .= ' DEFAULT CURRENT_TIMESTAMP';
			} else {
				$sql_type .= " DEFAULT '" . addslashes( (string) $default ) . "'";
			}
		}
		
		return '`' . $name . '` ' . $sql_type;
	}
	
	private function reset ( ) : void {
		$this->action = '';
		$this->table = '';
		$this->columns = [ ];
		$this->primary = '';
		$this->indexes = [ ];
		$this->foreign = [ ];
		$this->engine = 'InnoDB';
		$this->charset = 'utf8mb4';
		$this->collate = 'utf8mb4_unicode_ci';
		$this->if_exists = false;
		$this->if_not_exists = false;
		$this->new_name = '';
	}
}
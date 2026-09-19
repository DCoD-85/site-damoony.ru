<?PHP

class db_mysqli_query implements db_query_interface {
	# --- refs ---
	public db_mysqli $driver;
	public \main $main;
	
	# --- query state ---
	public string $sql = '';                    // финальный SQL с ?
	public array  $values = [ ];                // значения для bind
	public array  $types = [ ];                 // типы из SQL (%s -> 's', %d -> 'd')
	public array  $explicit_types = [ ];        // явно указанные пользователем типы
	public string $query_type = '';             // SELECT / INSERT / UPDATE / DELETE / OTHER
	
	# --- result state ---
	public null|\mysqli_stmt $stmt = null;
	public null|\mysqli_result $result = null;
	public int $affected_rows = 0;
	public int $insert_id = 0;
	public string $last_error = '';
	public string $log_query = '';
	
	# --- limit state ---
	public bool   $no_limit    = false;
	public null|int $limit     = null;          // ->limit(5)
	public null|int $page      = null;          // ->page(2)
	public null|int $page_size = null;          // ->page(2, 20)
	public bool   $one         = false;         // ->one()
	
	# --- methods ---
	public function __construct ( db_mysqli $driver ) {
		$this->driver = $driver;
		$this->main = $driver->main;
	}
	
	# ===============================================================
	#   PUBLIC API
	# ===============================================================
	
	public function query ( string $sql , array $values = [ ] , array $types = [ ] ) : self {
		$this->sql = $sql;
		$this->values = array_values( $values );
		$this->explicit_types = array_values( $types );
		$this->query_type = $this->detect_type( $sql );
		return $this;
	}
	
	public function one ( ) : self {
		$this->one = true;
		return $this;
	}
	
	public function page ( int $page , ?int $size = null ) : self {
		$this->page = $page;
		$this->page_size = $size;
		return $this;
	}
	
	public function limit ( int $n ) : self {
		$this->limit = $n;
		return $this;
	}
	
	public function no_limit ( ) : self {
		$this->no_limit = true;
		return $this;
	}
	
	# ===============================================================
	#   EXECUTION
	# ===============================================================
	
	# lazy execution: any result method triggers the query
	private function execute ( ) : void {
		if ( $this->stmt !== null || $this->last_error !== '' ) return;
		
		# 1. parse original SQL for %s/%d
		[ $sql_prepared , $sql_types ] = $this->parse_sql( $this->sql );
		$this->types = $sql_types;
		
		# 2. apply limit — modifies sql_prepared by reference, appends types and values
		$this->apply_limit( $sql_prepared );
		
		# 3. validate
		$this->validate_types( );
		$this->log_query = $this->build_log( $sql_prepared );
		
		$link = $this->driver->link( );
		if ( $link === null ) {
			$this->last_error = 'Not connected';
			return;
		}
		
		# prepare
		$stmt = @$link->prepare( $sql_prepared );
		if ( $stmt === false ) {
			$this->last_error = $link->error;
			return;
		}
		
		# bind params
		if ( ! empty( $this->values ) ) {
			$bind_string = $this->build_bind_string( );
			$stmt->bind_param( $bind_string , ...$this->values );
		}
		
		# execute
		if ( ! $stmt->execute( ) ) {
			$this->last_error = $stmt->error;
			$stmt->close( );
			return;
		}
		
		$this->stmt = $stmt;
		$this->affected_rows = $stmt->affected_rows;
		$this->insert_id = $stmt->insert_id;
		
		# get result for SELECT
		if ( $this->query_type === 'SELECT' ) {
			$result = $stmt->get_result( );
			if ( $result !== false ) $this->result = $result;
		}
	}
	
	# ===============================================================
	#   RESULTS
	# ===============================================================
	
	public function result ( bool $assoc = true ) : array {
		$this->execute( );
		if ( $this->last_error !== '' ) return [ ];
		if ( $this->result === null ) return [ ];
		
		$mode = $assoc ? MYSQLI_ASSOC : MYSQLI_NUM;
		$rows = $this->result->fetch_all( $mode );
		return $rows ?: [ ];
	}
	
	public function row ( bool $assoc = true ) : ?array {
		$rows = $this->result( $assoc );
		return $rows[ 0 ] ?? null;
	}
	
	public function scalar ( ) : mixed {
		$row = $this->row( false );
		return $row[ 0 ] ?? null;
	}
	
	public function count_rows ( ) : int {
		$this->execute( );
		if ( $this->result === null ) return 0;
		return (int) $this->result->num_rows;
	}
	
	public function affected ( ) : int {
		$this->execute( );
		return $this->affected_rows;
	}
	
	public function insert_id ( ) : int {
		$this->execute( );
		return $this->insert_id;
	}
	
	public function error ( ) : string {
		return $this->last_error;
	}
	
	public function show ( ) : string {
		$this->execute( );
		return '<div>' . htmlspecialchars( $this->log_query , ENT_QUOTES , 'UTF-8' ) . '</div>';
	}	
	# ===============================================================
	#   LEGACY API (backward compatibility)
	# ===============================================================
	
	# legacy: select($sql, $values, $args, $limit, $limitpage, $limitstep)
	public function select ( string $sql , array $values = [ ] , array $args = [ ] , bool $limit = false , int $limitpage = 0 , int $limitstep = 10 ) : self {
		$this->query_type = 'SELECT';
		$this->query( $sql , $values );
		if ( $limit ) {
			if ( $limitpage === 0 && $limitstep === 1 ) {
				$this->one( );
			} else {
				$this->page( $limitpage + 1 , $limitstep );
			}
		}
		return $this;
	}
	
	# legacy: insert($table, $names, $values, $types)
	public function insert_legacy ( string $table , array $names , array $values , array $types = [ ] ) : self {
		$cols = implode( ' , ' , array_map( fn( $c ) => '`' . $c . '`' , $names ) );
		if ( empty( $types ) ) {
			foreach ( $values as $v ) {
				$types[ ] = is_int( $v ) ? '%d' : '%s';
			}
		}
		$placeholders = implode( ' , ' , $types );
		$sql = 'INSERT INTO `' . $table . '` ( ' . $cols . ' ) VALUES ( ' . $placeholders . ' )';
		$this->query_type = 'INSERT';
		$this->query( $sql , $values , $types );
		return $this;
	}
	
	# alias for legacy
	public function insert ( string $table , array $names , array $values , array $types = [ ] ) : self {
		return $this->insert_legacy( $table , $names , $values , $types );
	}
	
	# ===============================================================
	#   SQL PARSING
	# ===============================================================
	
	# %s / %d -> ? , collect types as ['s','d',...]
	private function parse_sql ( string $sql ) : array {
		$result = '';
		$types = [ ];
		$len = strlen( $sql );
		
		for ( $i = 0 ; $i < $len ; $i++ ) {
			if ( $sql[ $i ] === '%' && $i + 1 < $len ) {
				$next = $sql[ $i + 1 ];
				if ( $next === 's' || $next === 'd' ) {
					$types[ ] = $next;
					$result .= '?';
					$i++;
					continue;
				}
			}
			$result .= $sql[ $i ];
		}
		
		return [ $result , $types ];
	}
	
	private function detect_type ( string $sql ) : string {
		$trimmed = ltrim( $sql );
		$upper = mb_strtoupper( mb_substr( $trimmed , 0 , 10 ) );
		
		if ( mb_strpos( $upper , 'SELECT' ) === 0 ) return 'SELECT';
		if ( mb_strpos( $upper , 'INSERT' ) === 0 ) return 'INSERT';
		if ( mb_strpos( $upper , 'UPDATE' ) === 0 ) return 'UPDATE';
		if ( mb_strpos( $upper , 'DELETE' ) === 0 ) return 'DELETE';
		if ( mb_strpos( $upper , 'CREATE' ) === 0 ) return 'CREATE';
		if ( mb_strpos( $upper , 'DROP' )   === 0 ) return 'DROP';
		if ( mb_strpos( $upper , 'ALTER' )  === 0 ) return 'ALTER';
		return 'OTHER';
	}
	
	# ===============================================================
	#   TYPE VALIDATION
	# ===============================================================
	
	private function validate_types ( ) : void {
		# check that number of values matches number of placeholders
		if ( count( $this->values ) !== count( $this->types ) ) {
			$this->raise( 'Placeholder count mismatch: SQL has ' . count( $this->types ) . ' , values has ' . count( $this->values ) );
			return;
		}
		
		# explicit types given
		if ( ! empty( $this->explicit_types ) ) {
			if ( count( $this->explicit_types ) !== count( $this->types ) ) {
				$this->raise( 'Explicit types count mismatch' );
				return;
			}
			foreach ( $this->types as $i => $sql_type ) {
				$explicit = $this->explicit_types[ $i ];
				# normalize %s -> s
				$explicit = ltrim( $explicit , '%' );
				if ( $explicit !== $sql_type ) {
					$this->raise( 'Type mismatch: SQL wants %' . $sql_type . ' , types says %' . $explicit . ' at position ' . $i );
					return;
				}
				$this->coerce_value( $i , $sql_type );
			}
			return;
		}
		
		# auto-detect and coerce
		foreach ( $this->types as $i => $sql_type ) {
			$this->coerce_value( $i , $sql_type );
		}
	}
	
	private function coerce_value ( int $i , string $sql_type ) : void {
		$value = $this->values[ $i ];
		$php_type = gettype( $value );
		
		if ( $sql_type === 'd' ) {
			# SQL wants int
			if ( $php_type === 'integer' ) return;
			if ( $php_type === 'double' ) {
				$this->values[ $i ] = (int) $value;
				return;
			}
			if ( $php_type === 'string' ) {
				if ( $this->is_dev( ) ) {
					$this->raise( 'SQL wants %d , got string "' . $value . '" at position ' . $i );
					return;
				}
				$this->values[ $i ] = (int) $value;
				$this->log( 'cast string -> int : ' . $value );
				return;
			}
			$this->raise( 'SQL wants %d , got ' . $php_type . ' at position ' . $i );
			return;
		}
		
		if ( $sql_type === 's' ) {
			# SQL wants string
			if ( $php_type === 'string' ) return;
			if ( $php_type === 'integer' ) { $this->values[ $i ] = (string) $value; return; }
			if ( $php_type === 'double' )  { $this->values[ $i ] = (string) $value; return; }
			$this->raise( 'SQL wants %s , got ' . $php_type . ' at position ' . $i );
			return;
		}
	}
	
	private function build_bind_string ( ) : string {
		$bind = '';
		foreach ( $this->types as $t ) {
			$bind .= ( $t === 'd' ) ? 'i' : 's';
		}
		return $bind;
	}
	
	# ===============================================================
	#   LIMIT
	# ===============================================================
	
		private function apply_limit ( string &$sql ) : void {
		if ( $this->no_limit ) return;
		if ( $this->query_type !== 'SELECT' && $this->query_type !== 'UPDATE' && $this->query_type !== 'DELETE' ) return;
		if ( stripos( $sql , ' LIMIT ' ) !== false ) return;
		
		# one() - single row
		if ( $this->one ) {
			$sql .= ' LIMIT ?';
			$this->values[ ] = 1;
			$this->types[ ] = 'd';
			return;
		}
		
		# limit(n)
		if ( $this->limit !== null ) {
			$sql .= ' LIMIT ?';
			$this->values[ ] = $this->limit;
			$this->types[ ] = 'd';
			return;
		}
		
		# page(n, size) with GET overrides
		if ( $this->page !== null ) {
			$page = max( 0 , $this->page - 1 );
			$size = $this->page_size ?? ( defined( 'DB_LIMIT_PAGE' ) ? DB_LIMIT_PAGE : 10 );
			
			# GET overrides
			if ( isset( $_GET[ 'p' ] ) )     $page = max( 0 , (int) $_GET[ 'p' ] - 1 );
			if ( isset( $_GET[ 'p_max' ] ) ) $size = (int) $_GET[ 'p_max' ];
			if ( $size <= 0 ) $size = defined( 'DB_LIMIT_PAGE' ) ? DB_LIMIT_PAGE : 10;
			if ( defined( 'DB_LIMIT_MAX' ) && $size > DB_LIMIT_MAX ) $size = DB_LIMIT_MAX;
			
			$sql .= ' LIMIT ? , ?';
			$this->values[ ] = $page * $size;
			$this->values[ ] = $size;
			$this->types[ ] = 'd';
			$this->types[ ] = 'd';
			return;
		}
		
		# default hard limit for SELECT only
		if ( $this->query_type === 'SELECT' ) {
			$sql .= ' LIMIT ?';
			$this->values[ ] = defined( 'DB_LIMIT_MAX' ) ? DB_LIMIT_MAX : 1000;
			$this->types[ ] = 'd';
		}
	}
	
	# ===============================================================
	#   HELPERS
	# ===============================================================
	
	private function is_dev ( ) : bool {
		return defined( 'GL_DEBUG' ) && GL_DEBUG === true;
	}
	
	private function raise ( string $message ) : void {
		$this->last_error = $message;
		if ( $this->is_dev( ) ) {
			throw new db_exception( $message , $this->sql , $this->values );
		}
		error_log( 'DB: ' . $message );
	}
	
	private function log ( string $message ) : void {
		error_log( 'DB: ' . $message );
	}
	
	private function build_log ( string $sql_prepared ) : string {
		$log = $sql_prepared;
		if ( ! empty( $this->values ) ) {
			$log .= '  -- values: ' . json_encode( $this->values , JSON_UNESCAPED_UNICODE );
		}
		return $log;
	}
}
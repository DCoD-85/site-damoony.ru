<?PHP

class log {
	# --- refs ---
	public \main $main;
	
	# --- storage ---
	public array $rows = [ ];
	
	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
	}
	
	# --- add a log entry ---
	public function add ( string $message , string $method = '' , string $file = '' , int $line = 0 ) : void {
		$this->rows[ ] = new log_row( $this , $message , $method , $file , $line );
	}
	
	# --- read ---
	public function all ( ) : array {
		return $this->rows;
	}
	public function count ( ) : int {
		return count( $this->rows );
	}
	public function has ( ) : bool {
		return ! empty( $this->rows );
	}
	
	# --- reset ---
	public function clear ( ) : void {
		$this->rows = [ ];
	}
	
	# --- render for debug panel ---
	public function as_tag ( ) : \visuotag {
		$vt = new \visuotag( );
	
        if ( empty( $this->rows ) ) return $vt;   // ← явно: нечего рендерить

		foreach ( $this->rows as $row ) {
			$vt->append( $row->as_tag( ) );
		}
		
		return $vt;
	}

	# --- alias (old code) ---
	public function astag ( ) : \visuotag {
		return $this->as_tag( );
	}
}


class log_row {
	# --- refs ---
	public log $log;
	
	# --- data ---
	public string $message;
	public string $method;
	public string $file;
	public int    $line;
	
	# --- methods ---
	public function __construct ( log $log , string $message , string $method , string $file , int $line ) {
		$this->log     = $log;
		$this->message = $message;
		$this->method  = $method;
		$this->file    = $file;
		$this->line    = $line;
	}
	
	public function as_tag ( ) : \visuotag {
		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'log-row' )
			->tag( 'div' , class : 'log-line' )->content( (string) $this->line )->parent( )
			->tag( 'div' , class : 'log-message' )->content( $this->message )->parent( )
			->tag( 'div' , class : 'log-source' )->content( $this->method . ' ' . $this->file . ':' . $this->line );
		
		return $vt;
	}
	
	# --- alias (old code) ---
	public function astag ( ) : \visuotag {
		return $this->as_tag( );
	}
}
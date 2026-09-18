<?PHP

class log {
	# vars
	public main $main;
	public array $rows = [ ];
	# methods
	public function __construct ( main $main ) {
		
	}
	public function add ( string $message , $method , $file , $line ) {
		$rows[ ] = new log_row(  $this , $message , $method , $file , $line );
	}
}

class log_row {
	# vars
	public log $log;
	public string $message;
	public string $method;
	public string $file;
	public string $line;
	# methods
	public function __construct ( log $log , $message , $method , $file , $line ) {
		$this->log = $log;
		$this->message = $message;
		$this->method = $method;
		$this->file = $file;
		$this->line = $line;
	}
	public function astag ( ) {
		$vt = new visuotag( );
		$vt->tag( 'div' )
			->tag( 'div' )->content( $this->line )
			->parent( )->tag( 'div' )->content( $this->message )
			->parent( )->tag( 'div' )->content( $this->method . ' ' . $this->file . ':' . $this->line  );
		
			return $vt;
	}
}

?>
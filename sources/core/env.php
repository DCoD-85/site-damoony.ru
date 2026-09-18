<?PHP

class core_env {
	public core $core;
	public \main $main;
	
	public string $type = 'prod';
	
	public function __construct ( core $core ) {
		$this->core = $core;
		$this->main = $core->main;
		$this->apply( );
	}
	
	public function set_type ( string $type ) : void {
		$this->type = $type;
		$this->apply( );
	}
	
	public function is_prod () : bool { return $this->type === 'prod'; }
	public function is_dev ()  : bool { return $this->type === 'dev'; }
	
	public function is_cli () : bool {
		return PHP_SAPI === 'cli';
	}
	
	public function is_ajax () : bool {
		if ( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] )
			&& strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest' ) {
			return true;
		}
		return false;
	}
	
	public function php_version () : string { return PHP_VERSION; }
	
	public function apply ( ) {
		if ( $this->is_dev( ) ) {
			error_reporting( E_ALL );
			ini_set( 'display_errors' , '1' );
		} else {
			error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED );
			ini_set( 'display_errors' , '0' );
		}
	}
}
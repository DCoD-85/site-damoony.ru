<?PHP

class config_define {
	public config $config;
	public \main $main;
	
	public function __construct ( config $config ) {
		$this->config = $config;
		$this->main = $config->main;
	}
	
	# --- declare constants from XML ---
	public function from_xml ( string $file ) : int {
		$data = $this->config->loader->read_xml( $file );
		$count = 0;
		
		foreach ( $data as $name => $value ) {
			if ( defined( $name ) ) continue;
			
			define( $name , $value );
			$this->config->settings->set( $name , $value );
			$count++;
		}
		
		return $count;
	}
	
	public function has ( string $name ) : bool {
		return defined( $name );
	}
	public function get ( string $name ) {
		return defined( $name ) ? constant( $name ) : null;
	}
}
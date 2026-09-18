<?PHP

class config_settings {
	public config $config;
	public \main $main;
	
	public array $data = [ ];
	
	public function __construct ( config $config ) {
		$this->config = $config;
		$this->main = $config->main;
	}
	
	public function get ( string $key ) {
		return $this->data[ $key ] ?? null;
	}
	public function set ( string $key , mixed $value ) : void {
		$this->data[ $key ] = $value;
	}
	public function has ( string $key ) : bool {
		return isset( $this->data[ $key ] );
	}
	public function all ( ) : array {
		return $this->data;
	}
}
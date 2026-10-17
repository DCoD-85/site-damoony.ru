<?PHP

class core_path {
	public core $core;
	public \main $main;
	
	public string $sources = '';
	public string $root    = '';
	public string $www     = '';
	public string $secure  = '';
	public string $logic   = '';
	
	public function __construct ( core $core ) {
		$this->core = $core;
		$this->main = $core->main;
		$this->init( );
	}
	
	public function init ( ) {
		$this->sources = dirname( __DIR__ );
		$this->root    = dirname( $this->sources );
		$this->www     = $this->root  . DS . 'www';
		$this->secure  = $this->root  . DS . 'secure';
		$this->logic   = $this->www   . DS . 'logic';
	}
	
	public function get ( string $name ) : string {
		return $this->{$name} ?? '';
	}
}
<?PHP

class config {
	# --- main ref ---
	public \main $main;
	
	# --- load order ---
	public array $modules = [ 'loader' , 'define' , 'settings' ];
	
	# --- submodules ---
	public config_loader   $loader;
	public config_define   $define;
	public config_settings $settings;
	
	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->init_modules( );
	}
	
	public function init_modules ( ) {
		foreach ( $this->modules as $name ) {
			$file  = __DIR__ . DS . 'config' . DS . $name . '.php';
			$class = 'config_' . $name;
			( include_once $file ) or error_file( $file , __FILE__ , __LINE__ );
			$this->{$name} = new $class( $this );
		}
	}
	
	# --- init: load settings and declare constants ---
	public function init ( ) {
		$this->define->from_xml( 'settings-test.xml.inc' );
	}
	
	# --- access ---
	public function get ( string $key ) {
		return $this->settings->get( $key );
	}
	public function set ( string $key , mixed $value ) {
		$this->settings->set( $key , $value );
	}
	public function has ( string $key ) : bool {
		return $this->settings->has( $key );
	}
}
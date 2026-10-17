<?PHP

class core {
	# --- main ref ---
	public \main $main;
	
	# --- load order ---
	public array $modules = [ 'const' , 'env' , 'path' , 'time' ];
	
	# --- submodules (typed) ---
	public core_const $const;
	public core_env   $env;
	public core_path  $path;
	public core_time  $time;
	
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->init( );
	}
	
	public function init ( ) {
		foreach ( $this->modules as $name ) {
			$file  = __DIR__ . DS . 'core' . DS . $name . '.php';
			$class = 'core_' . $name;
			( include_once $file ) or error_file( $file , __FILE__ , __LINE__ );
			$this->{$name} = new $class( $this );
		}
	}
	
	public function get ( string $name ) {
		return $this->{$name} ?? null;
	}
	public function has ( string $name ) : bool {
		return isset( $this->{$name} );
	}
	
	public function is_ajax ()     : bool   { return $this->env->is_ajax( ); }
	public function is_cli ()      : bool   { return $this->env->is_cli( ); }
	public function root ()        : string { return $this->path->root; }
	public function elapsed_ms ()  : float  { return $this->time->elapsed_ms( ); }
}
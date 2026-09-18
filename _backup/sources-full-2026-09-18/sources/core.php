<?PHP

# 
# 
# 
# 

class core {
	# vars
	public main $main;
	public core__path $core__path;
	public core__settings $core__settings;
	## mods
	public core_var $var;
	public core_generate $generate;

	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
		
		$this->init( );
	}

	# init
	public function init ( ) {
		$main = $this->main;

		// core specify classes
		( $path = include_once __DIR__ . DS . 'core' . DS . 'core__path.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( $path = include_once __DIR__ . DS . 'core' . DS . 'core__settings.php' ) or error_file( $path , __FILE__ , __LINE__ );

		$this->core__path = new core__path( $main );
		$this->core__settings = new core__settings( $main );

		// 
		// 
		// other classes
		( $path = include_once __DIR__ . DS . 'core' . DS . 'var.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( $path = include_once __DIR__ . DS . 'core' . DS . 'generate.php' ) or error_file( $path , __FILE__ , __LINE__ );
		
		$this->var = new core_var( $main );
		$this->generate = new core_generate( $main );
	}
}

<?PHP

# 
# 
# 
# 

class mod {
	# vars
	public main $main;
	private string $mod_path = __DIR__ . DS . 'mod' . DS;
	public array $modules = [ ];
	# vars: modules list
	# public null|gmod_user $user = null;
	# methods
	public function __construct ( null|main $main = null , bool $autoload = true ) {
		$this->main = $main;
		
		$this->autoload( $autoload );
	}

	public function autoload ( bool $autoload ) {
		$main = $this->main;

		$dir = $main->library->dir( );

		$dir_scan = $dir->get( $this->mod_path , true , true , false );
		
		if( ! empty( $dir_scan ) ){
			foreach( $dir_scan as $key=>$value ){
				$gmod_name = $value;
				$gmod_path = $key . DS . $gmod_name . '.php';
				$gmod_class = 'mod_' . $gmod_name;

				( include_once $path = $gmod_path ) or error_file( $path , __FILE__ , __LINE__ );
				$this->modules[ $value ] = new $gmod_class( $this );
			}
		}
	}

	public function init ( ) {
		$main = $this->main;
		# $console = $main->console;
		$init = true;
		$modules = $this->modules;
		
		if( ! empty( $modules ) ) foreach( $modules as $key=>$value ) $value->init( ) ?: $init = false;

		if( $init == false ){
			# $console->add( '<div>Не все модули инициализированы!</div>' );
		}
	}

	public function get ( string $mod_name ) {
		return $this->modules[ $mod_name ] ?: null;
	}

	public function show_list ( ) {
		$modules = $this->modules;

		if( ! empty( $modules ) ){
			foreach( $modules as $key=>$value ){
				$return .= '<div>mod ' . $key . ', class: [ ' . get_class( $value ) . ' ]</div>';
			}
		} else {
			$return = '<div>Empty modules list</div>';
		}

		return $return;
	}
}

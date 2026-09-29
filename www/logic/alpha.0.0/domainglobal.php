<?PHP

class domainglobal {
	# vars
	public main $main;
	# vars : scheme
	public null|domainglobal_header $header = null;
	public null|domainglobal_scheme $scheme = null;
	# methods
	public function __construct ( main $main , bool $autoload = true ) {
		$this->main = $main;
		
		$this->autoload( );
	}
	
	public function autoload ( ) {
		$main = $this->main;
		
		# ( include_once $path = 'domainglobal' . DS . 'header.php' ) or error_file( $path , __FILE__ , __LINE__ );
		# ( include_once $path = 'domainglobal' . DS . 'scheme.php' ) or error_file( $path , __FILE__ , __LINE__ );
		
		# $this->header = new domainglobal_header( $main );
		# $this->scheme = new domainglobal_scheme( $main );
	}
}

?>
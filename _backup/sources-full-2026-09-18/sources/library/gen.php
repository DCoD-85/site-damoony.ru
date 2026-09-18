<?PHP
# 
# 
# 

class library_gen {
	
	private null|library $library = null;
	
	public array $id = [ ];
	
	public function __construct( null|library $library ) {
		$this->library = $library;
		# init
		$this->init( );
	}
	
	private function init ( ) {
	}

	public function about ( ){
		return '<div>This is module ' . __FILE__ . '</div>';
	}
	
	public function id ( ) {
		return $this->id[ ] = $new = 'id' . rand( 111111111 , 999999999 );
		# return $return;
	}
	
}

?>
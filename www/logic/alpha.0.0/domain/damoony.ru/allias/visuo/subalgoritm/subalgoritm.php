<?PHP

class subalgoritm {
	# vars
	public main $main;
	public subalgoritm_top $top;
	public subalgoritm_left $left;
	public subalgoritm_right $right;
	public subalgoritm_bottom $bottom;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
		$this->autoload( );
	}
	
	public function autoload ( bool $init = true ) {
		( include_once $path = 'top.php' ) or error_file( $file , __FILE__ , __LINE__ );
		( include_once $path = 'left.php' ) or error_file( $file , __FILE__ , __LINE__ );
		( include_once $path = 'right.php' ) or error_file( $file , __FILE__ , __LINE__ );
		( include_once $path = 'bottom.php' ) or error_file( $file , __FILE__ , __LINE__ );
		
		if( $init === true ) $this->init( );
	}
	
	public function init ( ) {
		$this->top = new subalgoritm_top( $this->main );
		$this->left = new subalgoritm_left( $this->main );
		$this->right = new subalgoritm_right( $this->main );
		$this->bottom = new subalgoritm_bottom( $this->main );
	}
	
}

?>
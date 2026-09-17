<?PHP

namespace damoony;

class alliaslocal {
	# vars
	public \main $main;
	public null|alliaslocal_top $top = null;
	public null|alliaslocal_left $left = null;
	public null|alliaslocal_center $center = null;
	public null|alliaslocal_right $right = null;
	public null|alliaslocal_bottom $bottom = null;
	# methods
	public function __construct ( \main $main ) {
		$this->main = $main;
		
		$this->autoload ( );
	}
	public function autoload ( bool $init = true ) {
		
		// 'www';
		#
		# ( include_once $path = 'alliasglobal' . DS . 'auth.php' ) or error_file( $path , __FILE__ , __LINE__ );
		# ( include_once $path = 'alliasglobal' . DS . 'blocklist.php' ) or error_file( $path , __FILE__ , __LINE__ );
		# ( include_once $path = 'alliasglobal' . DS . 'headers.php' ) or error_file( $path , __FILE__ , __LINE__ );
		# ( include_once $path = 'alliasglobal' . DS . 'scheme.php' ) or error_file( $path , __FILE__ , __LINE__ );
		# ( include_once $path = 'alliasglobal' . DS . 'menu.php' ) or error_file( $path , __FILE__ , __LINE__ );
		

		( include_once $path = 'alliaslocal' . DS . 'top.php' ) or warning_file( $path , __FILE__ , __LINE__ );
		# ( include_once $path = 'alliaslocal' . DS . 'left.php' ) or warning_file( $path , __FILE__ , __LINE__ );
		# ( include_once $path = 'alliaslocal' . DS . 'right.php' ) or warning_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliaslocal' . DS . 'bottom.php' ) or warning_file( $path , __FILE__ , __LINE__ );

		if( $init === true ) $this->init( );
	}
	
	public function init ( ) {
		$this->top = ( ! class_exists( 'alliaslocal_top' ) ? null : new alliaslocal_top( $this ) );
		$this->bottom = ( ! class_exists( 'alliaslocal_bottom' ) ? null : new alliaslocal_bottom( $this ) );
		# $this->left = new alliaslocal_left( $this->main );
		# $this->right = new alliaslocal_right( $this->main );
		# $this->bottom = new alliaslocal_bottom( $this->main );
		
	}
}

?>
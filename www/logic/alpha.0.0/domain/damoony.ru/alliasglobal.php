<?PHP

namespace damoony;

class alliasglobal {
	# vars
	public \main $main;
	public alliasglobal_auth $auth;
	public alliasglobal_headers $headers;
	public alliasglobal_scheme $scheme;
	public alliasglobal_menu $menu;
	
	public alliasglobal_top $top;
	public alliasglobal_left $left;
	public alliasglobal_center $center;
	public alliasglobal_right $right;
	public alliasglobal_bottom $bottom;
	# methods
	public function __construct ( \main $main ) {
		$this->main = $main;
		
		$this->autoload ( );
	}
	public function autoload ( bool $init = true ) {
		
		#
		( include_once $path = 'alliasglobal' . DS . 'auth.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'headers.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'scheme.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'menu.php' ) or error_file( $path , __FILE__ , __LINE__ );
		
		( include_once $path = 'alliasglobal' . DS . 'top' . DS . 'top.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'left' . DS . 'left.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'center' . DS . 'center.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'right' . DS . 'right.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'bottom' . DS . 'bottom.php' ) or error_file( $path , __FILE__ , __LINE__ );

		if( $init === true ) $this->init( );
		
	}
	
	public function init ( ) {
		$this->auth = new alliasglobal_auth( $this->main );
		$this->headers = new alliasglobal_headers( $this->main );
		$this->scheme = new alliasglobal_scheme( $this->main );
		# $this->menu = new alliasglobal_menu( $this );
		
		$this->top = new alliasglobal_top( $this );
		$this->left = new alliasglobal_left( $this );
		$this->center = new alliasglobal_center( $this->main );
		$this->right = new alliasglobal_right( $this );
		$this->bottom = new alliasglobal_bottom( $this );

	}

	public function append ( object $object , string $name , array $vts , array $vtd ) {
		# die( get_class( $this ) . ' | ' . get_class( $object ) );
		# $append
		if( $vts[ $name ] && $vtd[ $name ] ) $vts[ $name ]->append( $vtd[ $name ] );

	}
}

?>
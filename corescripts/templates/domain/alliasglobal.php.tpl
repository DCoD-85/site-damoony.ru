<?PHP

namespace <domain_ns>;

class alliasglobal {
	# --- refs ---
	public \main $main;

	# --- blocks ---
	public alliasglobal_top    $top;
	public alliasglobal_left   $left;
	public alliasglobal_center $center;
	public alliasglobal_right  $right;
	public alliasglobal_bottom $bottom;
	public alliasglobal_policy $policy;
	public alliasglobal_debug $debug;

	# --- scripts (typed properties) ---
	# --- scripts (свойства) ---
	public alliasglobal_script_headers $headers;
	public alliasglobal_script_head $head;

	# --- scripts (collection for iteration) ---
	public array $scripts = [ ];

	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->autoload( );
	}

	public function autoload ( bool $init = true ) : void {
		# 0. trait
		( include_once $path = 'alliasglobal' . DS . 'alliasglobal_filler_trait.php' ) or error_file( $path , __FILE__ , __LINE__ );

		# 1. base
		( include_once $path = 'alliasglobal' . DS . 'menu_row.php'           ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'menu.php'               ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'alliasblock.php'        ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'alliasglobalscript.php' ) or error_file( $path , __FILE__ , __LINE__ );

		# 2. blocks
		( include_once $path = 'alliasglobal' . DS . 'top'    . DS . 'top.php'    ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'left'   . DS . 'left.php'   ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'center' . DS . 'center.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'right'  . DS . 'right.php'  ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'bottom' . DS . 'bottom.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'policy' . DS . 'policy.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'debug' . DS . 'debug.php' ) or error_file( $path , __FILE__ , __LINE__ );

		# 3. scripts
		( include_once $path = 'alliasglobal' . DS . 'script' . DS . 'headers.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'script' . DS . 'head.php'    ) or error_file( $path , __FILE__ , __LINE__ );

		if ( $init === true ) $this->init( );
	}

	public function init ( ) : void {
		# scripts, которые могут редиректить — первыми
		$this->headers              = new alliasglobal_script_headers( $this );
		$this->scripts[ 'headers' ] = $this->headers;

		$this->head                 = new alliasglobal_script_head( $this );
		$this->scripts[ 'head' ]    = $this->head;

		# blocks
		$this->top    = new alliasglobal_top( $this );
		$this->left   = new alliasglobal_left( $this );
		$this->center = new alliasglobal_center( $this );
		$this->right  = new alliasglobal_right( $this );
		$this->bottom = new alliasglobal_bottom( $this );
		$this->policy = new alliasglobal_policy( $this );
		$this->debug = new alliasglobal_debug( $this );
	}

	# --- accessor (dynamic lookup by name) ---
	public function script ( string $name ) : null|alliasglobalscript {
		return $this->scripts[ $name ] ?? null;
	}
}
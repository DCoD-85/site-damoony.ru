<?PHP

namespace damoony;

class alliasglobal {
	# --- refs ---
	public \main $main;

	# --- state ---
	public alliasglobal_top    $top;
	public alliasglobal_left   $left;
	public alliasglobal_center $center;
	public alliasglobal_right  $right;
	public alliasglobal_bottom $bottom;

	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->autoload( );
	}

	public function autoload ( bool $init = true ) : void {
		# порядок: строки → меню → база блока → элементы
		( include_once $path = 'alliasglobal' . DS . 'menu_row.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'menu.php'     ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'alliasblock.php' ) or error_file( $path , __FILE__ , __LINE__ );

		( include_once $path = 'alliasglobal' . DS . 'top'    . DS . 'top.php'    ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'left'   . DS . 'left.php'   ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'center' . DS . 'center.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'right'  . DS . 'right.php'  ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = 'alliasglobal' . DS . 'bottom' . DS . 'bottom.php' ) or error_file( $path , __FILE__ , __LINE__ );

		if ( $init === true ) $this->init( );
	}

	public function init ( ) : void {
		$this->top    = new alliasglobal_top( $this );
		$this->left   = new alliasglobal_left( $this );
		$this->center = new alliasglobal_center( $this->main );
		$this->right  = new alliasglobal_right( $this );
		$this->bottom = new alliasglobal_bottom( $this );
	}
}
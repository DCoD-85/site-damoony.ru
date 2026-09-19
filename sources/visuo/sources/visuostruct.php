<?PHP

class visuostruct {
	# --- refs ---
	public null|\main $main = null;

	# --- state ---
	public null|visuostruct_min  $min  = null;
	public null|visuostruct_ajax $ajax = null;

	# --- methods ---
	public function __construct ( null|\main $main = null ) {
		$this->main = $main;
	}

	public function min ( ) : visuostruct_min {
		if ( $this->min === null ) {
			$path = __DIR__ . DS . 'visuostruct' . DS . 'visuostruct_min.php';
			( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
			$this->min = new visuostruct_min( $this );
		}
		return $this->min;
	}

	public function ajax ( ) : visuostruct_ajax {
		if ( $this->ajax === null ) {
			$path = __DIR__ . DS . 'visuostruct' . DS . 'visuostruct_ajax.php';
			( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
			$this->ajax = new visuostruct_ajax( $this );
		}
		return $this->ajax;
	}
}

?>
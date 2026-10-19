<?PHP

class visuostruct {
	# --- refs ---
	public mixed $main = null;

	# --- state ---
	public null|visuostruct_html $html = null;
	public null|visuostruct_min  $min  = null;
	public null|visuostruct_ajax $ajax = null;

	# --- methods ---
	public function __construct ( mixed $main = null ) {
		$this->main = $main;
	}

	public function html ( ) : visuostruct_html {
		if ( $this->html === null ) {
			$path = __DIR__ . DS . 'visuostruct' . DS . 'visuostruct_html.php';
			( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
			$this->html = new visuostruct_html( $this );
		}
		return $this->html;
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

	# ---------------------------------------------------------------
	#   CURRENT
	#   Что создал программист — то и вернётся.
	#   Если ничего — HTML по умолчанию (обратная совместимость).
	# ---------------------------------------------------------------
	public function current ( ) : null|object {
		if ( $this->min  !== null ) return $this->min;
		if ( $this->ajax !== null ) return $this->ajax;
		if ( $this->html !== null ) return $this->html;

		return null;
	}
}

?>
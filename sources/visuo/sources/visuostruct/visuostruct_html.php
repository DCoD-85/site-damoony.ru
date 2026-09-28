<?PHP

class visuostruct_html {
	# --- refs ---
	public null|visuostruct $visuostruct = null;

	# --- blocks (переопределения) ---
	public null|visuotag|false $top    = null;
	public null|visuotag|false $left   = null;
	public null|visuotag|false $center = null;
	public null|visuotag|false $right  = null;
	public null|visuotag|false $bottom = null;

	# --- meta (переопределение) ---
	public null|visuometa $meta = null;

	# --- methods ---
	public function __construct ( null|visuostruct $visuostruct = null ) {
		$this->visuostruct = $visuostruct;
	}

	public function return_type ( ) : string {
		return 'html';
	}

	public function set (
		null|visuotag|false $top    = null ,
		null|visuotag|false $left   = null ,
		null|visuotag|false $center = null ,
		null|visuotag|false $right  = null ,
		null|visuotag|false $bottom = null
	) : static {
		if ( $top    !== null ) $this->top    = $top;
		if ( $left   !== null ) $this->left   = $left;
		if ( $center !== null ) $this->center = $center;
		if ( $right  !== null ) $this->right  = $right;
		if ( $bottom !== null ) $this->bottom = $bottom;
		return $this;
	}

	public function off ( string $block ) : static {
		if ( property_exists( $this , $block ) ) {
			$this->{$block} = false;
		}
		return $this;
	}
}

?>
<?PHP

class visuoattrsactions {
	# --- refs ---
	public null|\visuoattrs $visuoattrs = null;
	public null|\visuotag   $visuotag   = null;

	# --- state ---
	private array $elements = [ ];   # [ 'onclick' => 'code' , ... ]

	# --- methods ---
	public function __construct ( \visuoattrs $visuoattrs ) {
		$this->visuoattrs = $visuoattrs;
		$this->visuotag   = $visuoattrs->visuotag( );
	}

	public function parent ( ) : null|\visuotag {
		return $this->visuotag;
	}

	public function tag ( ) : null|\visuotag {
		return $this->visuotag;
	}

	# --- add / remove ---
	public function add ( string $action , string $value ) : visuoattrsactions {
		$action = strtolower( trim( $action ) );

		if ( ! $this->is_event( $action ) ) return $this;
		if ( $value === '' ) return $this;

		$this->elements[ $action ] = $value;
		return $this;
	}

	public function remove ( string $action ) : visuoattrsactions {
		$action = strtolower( trim( $action ) );
		unset( $this->elements[ $action ] );
		return $this;
	}

	public function clear ( ) : visuoattrsactions {
		$this->elements = [ ];
		return $this;
	}

	# --- queries ---
	public function has ( string $action ) : bool {
		return isset( $this->elements[ strtolower( trim( $action ) ) ] );
	}

	public function get ( string $action ) : string {
		return $this->elements[ strtolower( trim( $action ) ) ] ?? '';
	}

	public function all ( ) : array {
		return $this->elements;
	}

	# --- shorthand (backward-compat) ---
	public function onclick     ( string $v ) : visuoattrsactions { return $this->add( 'onclick'     , $v ); }
	public function ondblclick  ( string $v ) : visuoattrsactions { return $this->add( 'ondblclick'  , $v ); }
	public function onmousedown ( string $v ) : visuoattrsactions { return $this->add( 'onmousedown' , $v ); }
	public function onmouseup   ( string $v ) : visuoattrsactions { return $this->add( 'onmouseup'   , $v ); }
	public function onmouseover ( string $v ) : visuoattrsactions { return $this->add( 'onmouseover' , $v ); }
	public function onmouseout  ( string $v ) : visuoattrsactions { return $this->add( 'onmouseout'  , $v ); }
	public function onmousemove ( string $v ) : visuoattrsactions { return $this->add( 'onmousemove' , $v ); }

	# --- release ---
	public function release ( bool $full = true ) : string {
		if ( empty( $this->elements ) ) return '';

		$html = '';
		foreach ( $this->elements as $name => $value ) {
			$html .= ' ' . $name . '="' . htmlspecialchars( $value , ENT_QUOTES ) . '"';
		}

		return $full ? $html : trim( $html );
	}

	# --- internals ---
	private function is_event ( string $action ) : bool {
		return (bool) preg_match( '/^on[a-z]+$/' , $action );
	}
}
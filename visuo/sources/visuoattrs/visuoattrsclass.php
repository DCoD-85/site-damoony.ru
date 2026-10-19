<?PHP

class visuoattrsclass {
	# --- refs ---
	public null|\visuoattrs $visuoattrs = null;
	public null|\visuotag   $visuotag   = null;

	# --- state ---
	private array $elements = [ ];   # [ 'class-name' => true , ... ]

	# --- methods ---
	public function __construct ( \visuoattrs $visuoattrs ) {
		$this->visuoattrs = $visuoattrs;
		$this->visuotag   = $visuoattrs->visuotag( );
	}

	public function parent ( ) : null|\visuotag {
		return $this->visuotag;
	}

	# --- add / remove ---
	public function add ( string $value ) : visuoattrsclass {
		foreach ( $this->split( $value ) as $class_name ) {
			$this->elements[ $class_name ] = true;
		}
		return $this;
	}

	public function remove ( string $value ) : visuoattrsclass {
		foreach ( $this->split( $value ) as $class_name ) {
			unset( $this->elements[ $class_name ] );
		}
		return $this;
	}

	public function clear ( ) : visuoattrsclass {
		$this->elements = [ ];
		return $this;
	}

	# --- queries ---
	public function has ( string $value ) : bool {
		return isset( $this->elements[ $value ] );
	}

	public function get ( string $value ) : string {
		return isset( $this->elements[ $value ] ) ? $value : '';
	}

	public function all ( ) : array {
		return array_keys( $this->elements );
	}

	# --- release ---
	public function release ( bool $full = true ) : string {
		if ( empty( $this->elements ) ) return '';

		$classes = htmlspecialchars( implode( ' ' , array_keys( $this->elements ) ) , ENT_QUOTES );

		return $full ? ' class="' . $classes . '"' : $classes;
	}

	# --- internals ---
	private function split ( string $value ) : array {
		$value = trim( $value );
		if ( $value === '' ) return [ ];

		$parts = preg_split( '/\s+/' , $value );
		return $parts === false ? [ ] : $parts;
	}
}
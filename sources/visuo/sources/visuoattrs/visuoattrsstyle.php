<?PHP

class visuoattrsstyle {
	# --- refs ---
	public null|\visuoattrs $visuoattrs = null;
	public null|\visuotag   $visuotag   = null;

	# --- state ---
	private array $elements = [ ];   # [ 'name' => 'value' , ... ]

	# --- methods ---
	public function __construct ( \visuoattrs $visuoattrs ) {
		$this->visuoattrs = $visuoattrs;
		$this->visuotag   = $visuoattrs->visuotag( );
	}

	public function parent ( ) : null|\visuotag {
		return $this->visuotag;
	}

	# --- add / remove ---
	public function add ( string $expression , string $pair_separator = ';' , string $kv_separator = ':' ) : visuoattrsstyle {
		$pairs = explode( $pair_separator , $expression );

		foreach ( $pairs as $pair ) {
			$pair = trim( $pair );
			if ( $pair === '' ) continue;

			$kv = explode( $kv_separator , $pair , 2 );
			if ( count( $kv ) < 2 ) continue;

			$name  = trim( $kv[ 0 ] );
			$value = trim( $kv[ 1 ] );

			if ( $name === '' ) continue;
			$this->elements[ $name ] = $value;
		}

		return $this;
	}

	public function remove ( string $names ) : visuoattrsstyle {
		foreach ( $this->split( $names ) as $name ) {
			unset( $this->elements[ $name ] );
		}
		return $this;
	}

	public function clear ( ) : visuoattrsstyle {
		$this->elements = [ ];
		return $this;
	}

	# --- queries ---
	public function has ( string $name ) : bool {
		return array_key_exists( $name , $this->elements );
	}

	public function get ( string $name ) : string {
		return $this->elements[ $name ] ?? '';
	}

	public function all ( ) : array {
		return $this->elements;
	}

	# --- release ---
	public function release ( bool $full = true ) : string {
		if ( empty( $this->elements ) ) return '';

		$css = '';
		foreach ( $this->elements as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}

		$css = htmlspecialchars( $css , ENT_QUOTES );

		return $full ? ' style="' . $css . '"' : $css;
	}

	# --- internals ---
	private function split ( string $value ) : array {
		$value = trim( $value );
		if ( $value === '' ) return [ ];

		$parts = preg_split( '/\s+/' , $value );
		return $parts === false ? [ ] : $parts;
	}
}
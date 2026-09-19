<?PHP

class visuoattrsdata {
	# --- refs ---
	private null|\visuoattrs $owner = null;

	# --- state ---
	private array $data = [ ];

	# --- methods ---
	public function __construct ( null|\visuoattrs $owner = null ) {
		$this->owner = $owner;
	}

	# --- data ---
	public function add ( string $name , null|bool|string|int|array $value , bool $to_json = true ) : visuoattrsdata {
		$this->data[ $name ] = [ 'value' => $value , 'json' => $to_json ];
		return $this;
	}

	public function get ( string $name ) : mixed {
		return array_key_exists( $name , $this->data )
			? $this->data[ $name ][ 'value' ]
			: null;
	}

	public function has ( string $name ) : bool {
		return array_key_exists( $name , $this->data );
	}

	public function remove ( string $name ) : visuoattrsdata {
		unset( $this->data[ $name ] );
		return $this;
	}

	public function clear ( ) : visuoattrsdata {
		$this->data = [ ];
		return $this;
	}

	public function all ( ) : array {
		return $this->data;
	}

	public function owner ( ) : null|\visuoattrs {
		return $this->owner;
	}

	# --- json helpers ---
	public static function encode ( mixed $value ) : string {
		$json = json_encode( $value , JSON_UNESCAPED_UNICODE );
		return $json === false ? '' : $json;
	}

	public static function decode ( string $json ) : mixed {
		return json_decode( $json , true );
	}

	# --- release ---
	public function release ( ) : string {
		if ( empty( $this->data ) ) return '';

		$html = '';
		foreach ( $this->data as $name => $entry ) {
			$raw = $entry[ 'json' ]
				? self::encode( $entry[ 'value' ] )
				: (string) $entry[ 'value' ];

			$html .= ' data-' . $name . '="' . htmlspecialchars( $raw , ENT_QUOTES ) . '"';
		}

		return $html;
	}
}
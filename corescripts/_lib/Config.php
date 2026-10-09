<?PHP

class Config {

	private string $path;
	private array  $data;

	public function __construct ( string $path ) {
		$this->path = $path;
		if ( ! is_file( $path ) ) {
			throw new RuntimeException( "Config not found: {$path}" );
		}
		$json = file_get_contents( $path );
		$data = json_decode( $json , true );
		if ( ! is_array( $data ) ) {
			throw new RuntimeException( "Invalid JSON: {$path}" );
		}
		$this->data = $data;
	}

	public function data ( ) : array {
		return $this->data;
	}

	public function get ( string $path , mixed $default = null ) : mixed {
		$keys = explode( '.' , $path );
		$ref  = $this->data;
		foreach ( $keys as $k ) {
			if ( ! is_array( $ref ) || ! array_key_exists( $k , $ref ) ) return $default;
			$ref = $ref[ $k ];
		}
		return $ref;
	}

	public function has ( string $path ) : bool {
		$keys = explode( '.' , $path );
		$ref  = $this->data;
		foreach ( $keys as $k ) {
			if ( ! is_array( $ref ) || ! array_key_exists( $k , $ref ) ) return false;
			$ref = $ref[ $k ];
		}
		return true;
	}

		# --- array-key API (без парсинга точек) ---

	public function hasKeys ( array $keys ) : bool {
		$ref = $this->data;
		foreach ( $keys as $k ) {
			if ( ! is_array( $ref ) || ! array_key_exists( $k , $ref ) ) return false;
			$ref = $ref[ $k ];
		}
		return true;
	}

	public function getKeys ( array $keys , mixed $default = null ) : mixed {
		$ref = $this->data;
		foreach ( $keys as $k ) {
			if ( ! is_array( $ref ) || ! array_key_exists( $k , $ref ) ) return $default;
			$ref = $ref[ $k ];
		}
		return $ref;
	}

	public function setKeys ( array $keys , mixed $value ) : void {
		$ref = &$this->data;
		foreach ( $keys as $k ) {
			if ( ! isset( $ref[ $k ] ) || ! is_array( $ref[ $k ] ) ) {
				$ref[ $k ] = [ ];
			}
			$ref = &$ref[ $k ];
		}
		$ref = $value;
	}

	public function set ( string $path , mixed $value ) : void {
		$keys = explode( '.' , $path );
		$ref  = &$this->data;
		foreach ( $keys as $k ) {
			if ( ! isset( $ref[ $k ] ) || ! is_array( $ref[ $k ] ) ) {
				$ref[ $k ] = [ ];
			}
			$ref = &$ref[ $k ];
		}
		$ref = $value;
	}

	public function save ( ) : void {
		$backup = $this->path . '.bak';
		@copy( $this->path , $backup );

		$json = json_encode(
			$this->data ,
			JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
		if ( $json === false ) {
			throw new RuntimeException( "Cannot encode JSON" );
		}

		# JSON_PRETTY_PRINT → 4 пробела, заменяем на табы
		$json = preg_replace_callback( '/^( +)/m' , function ( $m ) {
			return str_repeat( "\t" , (int) ( strlen( $m[ 1 ] ) / 4 ) );
		} , $json );

		if ( file_put_contents( $this->path , $json ) === false ) {
			throw new RuntimeException( "Cannot write: {$this->path}" );
		}
	}
}
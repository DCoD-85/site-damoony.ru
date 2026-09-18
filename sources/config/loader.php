<?PHP

class config_loader {
	public config $config;
	public \main $main;
	
	public function __construct ( config $config ) {
		$this->config = $config;
		$this->main = $config->main;
	}
	
	# --- read XML config (settings-*.xml.inc) ---
	public function read_xml ( string $file ) : array {
		$path = __DIR__ . DS . $file;
		if ( ! is_file( $path ) ) return [ ];
		
		$xml = simplexml_load_file( $path );
		if ( $xml === false ) return [ ];
		
		$result = [ ];
		foreach ( $xml->row as $row ) {
			# skip if disabled
			if ( isset( $row[ 'disabled' ] ) && (string) $row[ 'disabled' ] === 'true' ) continue;
			
			$name  = trim( (string) $row->name );
			$value = trim( (string) $row->value );
			
			if ( $name !== '' ) {
				$result[ $name ] = $value;
			}
		}
		
		return $result;
	}
	
	# --- read simple text config (key value per line) ---
	public function read_txt ( string $file ) : array {
		$path = __DIR__ . DS . $file;
		if ( ! is_file( $path ) ) return [ ];
		
		$result = [ ];
		foreach ( file( $path ) as $line ) {
			$line = trim( $line );
			if ( $line === '' || $line[ 0 ] === '#' || $line[ 0 ] === '/' ) continue;
			
			$parts = preg_split( '/\s+/' , $line , 2 );
			if ( count( $parts ) === 2 ) {
				$result[ $parts[ 0 ] ] = $parts[ 1 ];
			}
		}
		
		return $result;
	}
}
<?PHP

class library_vars {
	private null|library $library = null;
	
	public function __construct( null|library $library ) {
		$this->library = $library;
	}
	
	# check and validate variable
	# returns type string on success, $var_default or NULL on failure
	public function check ( mixed $var ,
	                        string $type ,
	                        int $var_length_min = 0 ,
	                        int $var_length_max = -1 ,
	                        string $var_ereg = '' ,
	                        bool $null = false ,
	                        array $var_values = [ ] ,
	                        mixed $var_default = NULL ) {
		
		if ( $var_values == false ) $var_values = [ ];
		if ( $null == true && $var_default == false ) $var_default = NULL;
		
		$type     = mb_strtolower( $type );
		$var_type = mb_strtolower( gettype( $var ) );
		
		if ( $type != $var_type ) return NULL;
		
		switch ( $type ) {
			case 'string' :
				if ( $var_length_min < $var_length_max ) {
					$len = mb_strlen( $var );
					if ( ! ( $len >= $var_length_min && $len <= $var_length_max ) ) {
						return $var_default;
					}
				}
				if ( $var_ereg !== '' ) {
					if ( ! mb_ereg_match( $var_ereg , $var ) ) {
						return $var_default;
					}
				}
				if ( ! empty( $var_values ) ) {
					if ( ! in_array( $var , $var_values , true ) ) {
						return $var_default;
					}
				}
				break;
			case 'integer' :
			case 'double' :
				if ( $var_length_min < $var_length_max ) {
					if ( ! ( $var >= $var_length_min && $var <= $var_length_max ) ) {
						return $var_default;
					}
				}
				break;
			case 'boolean' :
			case 'null' :
				break;
		}
		
		return $type;
	}
	
	# add error message via main->messages
	# (was in core_var before merge)
	public function error ( string $message ) {
		$messages = $this->library->main->messages;
		$messages->add(
			'error' ,
			'Заполните поля правильно: ' . $message ,
			'Заполните поле правильно: <b>' . $message . '</b>'
		);
	}
}
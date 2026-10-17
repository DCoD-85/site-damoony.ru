<?PHP

class library_link {
	private null|library $library = null;
	public string $connect_type = 'https://';
	
	public function __construct( null|library $library ) {
		$this->library = $library;
	}
	
	# --- public entry point ---
	public function href ( bool|array $allias_address = true ,
	                       bool|array $base_address = true ,
	                       array $base_vars = [ ] ,
	                       string $anchor = '' ,
	                       bool $use_all_adress_data = true ,
	                       bool $host = true ) {
		return $this->ahref( $allias_address , $base_address , $base_vars , $use_all_adress_data , $anchor , $host );
	}
	
	# --- build url ---
	# $allias_address - subdomain(s) [ true - use current , false - skip , array - custom ]
	# $base_address   - path segments [ true - from current url , false - root , string , array ]
	# $base_vars      - user query params
	# $use_all_adress_data - use current GET params [ true | false ]
	# $anchor         - #anchor
	# $host           - prepend protocol + host
	public function ahref ( bool|array $allias_address = [ ] ,
	                        bool|string|array $base_address = true ,
	                        array $base_vars = [ ] ,
	                        bool $use_all_adress_data = false ,
	                        string $anchor = '' ,
	                        bool $host = true ) {
		$return = '';
		
		# --- base address part ---
		if ( is_bool( $base_address ) ) {
			if ( $base_address === false ) {
				$return .= DSA;
			} else if ( $base_address === true ) {
				$address = $this->library->url( )->url( );
				foreach ( $address as $v ) {
					if ( $v != '' ) $return .= DSA . $v;
				}
			}
		} else if ( is_string( $base_address ) ) {
			$return = DSA . $base_address;
		} else if ( is_array( $base_address ) ) {
			if ( empty( $base_address ) ) {
				$return .= DSA;
			} else {
				foreach ( $base_address as $v ) {
					$return .= DSA . $v;
				}
			}
		}
		
		# --- query params ---
		$data = [ ];
		$data[ 'user' ]        = $base_vars;
		$data[ 'get' ]         = $_GET;
		$data[ 'user_string' ] = $this->library->url( )->get_return( $data[ 'user' ] );
		$data[ 'get_string' ]  = $this->library->url( )->get_return( $data[ 'get' ] );
		$data[ 'new_string' ]  = [ ];
		
		# --- merge user params ---
		foreach ( $data[ 'user_string' ] as $k => $v ) {
			$get  = $data[ 'get_string' ][ $k ] ?? null;
			$user = $data[ 'user_string' ][ $k ];
			
			if ( is_int( $user ) || is_double( $user ) ) {
				$data[ 'new_string' ][ $k ] = $user;
			} else if ( is_string( $user ) ) {
				$data[ 'new_string' ][ $k ] = $user;
			} else if ( is_bool( $user ) || is_null( $user ) ) {
				switch ( $user ) {
					case false :  # remove if exists
						if ( $get ) $data[ 'new_string' ][ $k ] = false;
						break;
					case true :   # keep if exists
						if ( $get ) $data[ 'new_string' ][ $k ] = true;
						break;
				}
			}
		}
		
		# --- handle current GET params ---
		if ( $use_all_adress_data == 1 ) {
			foreach ( $data[ 'get_string' ] as $k => $v ) {
				$get = $data[ 'get_string' ][ $k ];
				$new = $data[ 'new_string' ][ $k ] ?? null;
				
				if ( isset( $new ) ) {
					if ( $new === false ) {
						unset( $data[ 'new_string' ][ $k ] );
					} else if ( $new === true ) {
						$data[ 'new_string' ][ $k ] = $get;
					}
				} else {
					$data[ 'new_string' ][ $k ] = $get;
				}
			}
		} else {
			foreach ( $data[ 'new_string' ] as $k => $v ) {
				$get = $data[ 'get_string' ][ $k ] ?? null;
				$new = $data[ 'new_string' ][ $k ];
				
				if ( $new === false )      unset( $data[ 'new_string' ][ $k ] );
				else if ( $new === true )  $data[ 'new_string' ][ $k ] = $get;
			}
		}
		
		# --- build query string ---
		if ( ! empty( $data[ 'new_string' ] ) ) {
			$query = '?';
			foreach ( $data[ 'new_string' ] as $k => $v ) {
				$query .= $k . '=' . $v . '&';
			}
			$return .= $query;
		}
		
		# trim trailing &
		if ( $return !== '' && $return[ strlen( $return ) - 1 ] == '&' ) {
			$return = mb_substr( $return , 0 , mb_strlen( $return ) - 1 );
		}
		
		if ( ! empty( $anchor ) ) $return .= '#' . $anchor;
		
		# --- domain part ---
		if ( is_bool( $allias_address ) ) {
			if ( $allias_address === true ) {
				$allias = '';
				foreach ( $this->library->url( )->static_domain as $value ) {
					is_null( $value ) ?: $allias .= $value . '.';
				}
				$allias = mb_substr( $allias , 0 , mb_strlen( $allias ) - 1 );
				
				$return = $allias . $return;
				if ( $host ) $return = $this->connect_type . $return;
			}
		} else if ( is_array( $allias_address ) ) {
			if ( ! empty( $allias_address ) ) {
				$allias = '';
				foreach ( $allias_address as $value ) {
					is_null( $value ) ?: $allias .= $value . '.';
				}
				$allias = mb_substr( $allias , 0 , mb_strlen( $allias ) - 1 );
				
				$return = $allias . $return;
				if ( $host ) $return = $this->connect_type . $return;
			}
		}
		
		return $return;
	}
}
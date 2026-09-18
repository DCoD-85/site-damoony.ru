<?PHP

class library_vars {
	private null|library $library = null;
	public library_dir_data $functions_dir_data;
	
	public function __construct( null|library $library ) {
		$this->library = $library;
	}
	
	public function get ( array $get ) {
		
	}
	public function valinarray ( $var , $value , array $accepted = [ ] ) {
		
	}
	// .. возвращаем совпадения
	public function vars_ereg( $var_ereg , $var ){
	
		$ereg = mb_ereg( $var_ereg , $var , $matches );
		
		return ! empty( $matches ) ? $matches : false;
	}
	
	public function check( mixed $var ,
												string $type ,
												int $var_length_min = 0 ,
												int $var_length_max = -1 ,
												string $var_ereg = '' ,
												bool $null = false ,
												array $var_values = [ ] ,
												mixed $var_default = NULL ){
		
		$bOk = '';
		# if( is_string( $var_length_min ) || ( is_integer( $var_length_min ) && $var_length_min == 0 ) ) $var_length_min = false;
		# if( is_string( $var_length_max ) || ( is_integer( $var_length_max ) && $var_length_max == 0 ) ) $var_length_max = false;
		
		if( $var_values == false ) $var_values = [ ];
		if( $null == true && $var_default == false ) $var_default = NULL;
		
		$type = mb_strtolower( $type );
		$var_type = mb_strtolower( gettype( $var ) );

		if( $type == $var_type ){

			switch( $type ){
				case 'string' :
					if( $var_length_min < $var_length_max ){
						if( ! ( mb_strlen( $var ) >= $var_length_min && mb_strlen( $var ) <= $var_length_max ) ){
							return $var_default;
						}
					}
					break;
				case 'integer' :
					if( $var_length_min < $var_length_max ){
						if( ! ( $var >= $var_length_min && $var <= $var_length_max ) ){
							return $var_default;
						}
					}
					break;
				case 'boolean' :
					break;
				case 'null' :
					break;
				default :
					break;
			}
			return $type;
		} else {
			return NULL;
		}

		/*
		if( isset( $type ) ){
			switch( mb_strtolower( $type ) ){
				case 'string' :
					$var = (string)$var;
					
					if( mb_strlen( $var ) > 0 && ! is_string( $var ) ){ return $var_default; }
					
					// print '<div>string : "'.$var.'" | min: '.$var_length_min.' | max: '.$var_length_max.' | length: '.mb_strlen( $var ).' | ereg : '.$var_ereg.'</div>';
					if( $var_length_min !== false && empty( $var_values ) ){
						if( mb_strlen( $var ) < $var_length_min ){ return $var_default; }
					}
					// print '<div>min : '.$var.' | min: '.$var_length_min.' | max: '.$var_length_max.' | length: '.mb_strlen( $var ).' | ereg : '.$var_ereg.'</div>';
					
					// return $var_length_min;
					if( $var_length_max !== false && empty( $var_values ) ){
						if( mb_strlen( $var ) > $var_length_max ){ return $var_default; }
					}
					// print '<div>max : '.$var.' | min: '.$var_length_min.' | max: '.$var_length_max.' | length: '.mb_strlen( $var ).' | ereg : '.$var_ereg.'</div>';
					
					if( $var_ereg !== false && empty( $var_values ) ){
						$ereg = mb_ereg_match( $var_ereg , $var );
						if( ! $ereg ){ return $var_default; }
					}
					
					if( ! empty( $var_values ) && is_array( $var_values ) ){
						foreach( $var_values as $k ){
							if( $var == $k ){
								$bOk = true;
								break;
							}
						}
						if( ! $bOk ) $var = $var_default;
					}
					
					return $var;
				case 'int' :
					if( ! mb_ereg_match( '^(-){0,1}([0-9])+$' , $var ) ) return $var_default;
					
					$var = (int)$var;
					$var_length_min = (int)$var_length_min;
					$var_length_max = (int)$var_length_max;
					
					if( $var_length_min !== false ){
						if( $var < $var_length_min ) return $var_default;
					}
					if( $var_length_max !== false ){
						if( $var > $var_length_max ) return $var_default;
					}
					
					return $var;
				case 'float' :
				case 'double' :
					if( ! mb_ereg_match( '^(-){0,1}[0-9]+(\.[0-9]+)?$' , $var ) ) return false;
					
					$var = (double)$var;
					$var_length_min = (int)$var_length_min;
					$var_length_max = (int)$var_length_max;
					
					if( $var_length_min !== false ){
						if( $var < $var_length_min ) return false;
					}
					if( $var_length_max !== false ){
						if( $var > $var_length_max ) return false;
					}
					
					return $var;
				case 'datetime' :
					$var = (string)$var;
					
					if( $var_length_min !== false && empty( $var_values ) ){
						if( mb_strlen( $var ) < $var_length_min ){ return $var_default; }
					}
					
					if( $var_length_max !== false && empty( $var_values ) ){
						if( mb_strlen( $var ) > $var_length_max ){ return $var_default; }
					}
					
					return $var;
				default :
					break;
			}
		}
		else {
			$var = '';
			return '';
		}
		*/
	}
	
	public function vars_if( $var ,
													$type ,
													$var_length_min = false ,
													$var_length_max = false ,
													$var_ereg = '' ,
													$var_values='' ){
		if( isset( $var ) ){
			switch( $type ){
				case 'string' :
					if( ! is_string( $var ) ) return false;
					// print '<div>string : "'.$var.'" | min: '.$var_length_min.' | max: '.$var_length_max.' | length: '.mb_strlen( $var ).' | ereg : '.$var_ereg.'</div>';
					
					if( $var_length_min != false ){
						if( mb_strlen( $var ) < $var_length_min ) return false;
					}
					// print '<div>min : '.$var.' | min: '.$var_length_min.' | max: '.$var_length_max.' | length: '.mb_strlen( $var ).' | ereg : '.$var_ereg.'</div>';
					
					if( $var_length_max != false ){
						if( mb_strlen( $var ) > $var_length_max ) return false;
					}
					// print '<div>max : '.$var.' | min: '.$var_length_min.' | max: '.$var_length_max.' | length: '.mb_strlen( $var ).' | ereg : '.$var_ereg.'</div>';
					
					if( $var_ereg != false ){
						$ereg = mb_ereg_match( $var_ereg , $var );
						if( ! $ereg ) return false;
					}
					
					if( $var_values && is_array( $var_values ) ){
						$def = $var_values[ 0 ];
						foreach( $var_values as $k ){
							if( $var == $k ){
								$bOk = $k;
								break;
							}
						}
						if( ! $bOk ) $var = $def;
					}
					return $var;
				case 'int' : return is_int( $var );
				case 'double' : return is_double($var );
				default : return false;
			}
		}
	}
}

?>
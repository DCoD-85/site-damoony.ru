<?PHP
# 
# 
# 

class library_link {
	private null|library $library = null;
	public string $connect_type = 'https://';
	
	public function __construct( null|library $library ) {
		$this->library = $library;
		
		# $this->connect_type = $this->main->main_settings->get( 'connect_type' );
	}
	
	public function about( ){
		return __METHOD__ . ' [ ' . __FILE__ . ':' . __LINE__ . ' ]';
	}
	
	public function href ( bool|array $allias_address = true ,
												bool|array $base_address = true ,
												array $base_vars = [ ] ,
												string $anchor = '' ,
												bool $use_all_adress_data = true ,
												bool $host = true ) {
		return $this->ahref( $allias_address , $base_address , $base_vars  , $use_all_adress_data , $anchor , $host );
	}
	
	public function ahref ( bool|array $allias_address = [ ] ,
												bool|string|array $base_address = true ,
												array $base_vars = [ ] ,
												bool $use_all_adress_data = false ,
												string $anchor = '' ,
												bool $host = true ) {
		$return = '';
		// $datauser						<> Пользовательские данные [ data_string=>string , [ data_array=>[ data1_string=>string , data2_string=>string ] ] ...
		// $use_all_adress_data	<> Использовать переменные адресной строки [ 0 <> Нет , 1 <> Да ]
		// $base_link						<> [ 0 <>  Нет , 1 <> Да , (string) <> Использовать значене , (Array)[ val1 , val2 , .. ] <> Только выбранные значение
		// 
		// 	$datauser = array()
		// 		attr1		>> атрибут1
		// 		val1		>> значение1
		// 		...
		// 	$use_all_adress_data = 0
		// 		...
		// 	$base_link = array( )
		// 		type					>> *inside | outside
		// 		protocol			>> *https | http | ftp | ...
		// 		domain				>> *domain.com
		// 		subdomain		>> *www | array( www , www2 , www.www3 ... )
		
		# $class__index = $this->class__index;
		// .. алгоритм
		if( $outside ) $return = $outside;
		else $return = '';
		
		if( is_bool( $base_address ) ){
			if( $base_address === false ){
				$return .= DSA;
			}
			else if( $base_address === true ){
				$address = $this->library->url( )->url( );
				foreach( $address as $k=>$v ){
					if( $v != '' ) $return .= DSA . $v;
				}
			}
		}
		else if( is_string( $base_address ) ){
			$return = DSA . $base_address;
		}
		else if( is_array( $base_address ) ){
			if( empty( $base_address ) ){
				$return .= DSA;
			}
			else{
				foreach( $base_address as $v ){
					$return .= DSA . $v;
				}
			}
		}
		
		// .. after address
		
		if( $after_address != '' ){
			$return .= DSA . $after_address;
		}
		//
		//
		//
		
		
		$data[ 'user' ] = $base_vars;
		
		$data[ 'get' ] = $_GET;
		$data[ 'user_string' ]= $this->library->url( )->get_return( $data[ 'user' ] );
		$data[ 'get_string' ] = $this->library->url( )->get_return( $data[ 'get' ] );
		$data[ 'new_string' ] = [ ];
		
		# print '<div>user_string</div>';
		# foreach( $data[ 'user_string' ] as $k=>$v ) print '<div>' . $k . ' = ' . $v . '</div>';
		# print '<div>get_string</div>';
		# foreach( $data[ 'get_string' ] as $k=>$v ) print '<div>' . $k . ' = ' . $v . '</div>';
		
		// Проходим все данные GET
		foreach( $data[ 'user_string' ] as $k=>$v ){
			$get = isset( $data[ 'get_string' ][ $k ] ) ? $data[ 'get_string' ][ $k ] : null;
			$user = $data[ 'user_string' ][ $k ];
			
			$user_int				= is_integer( $user );
			$user_double		= is_double( $user );
			$user_string		= is_string( $user );
			$user_bool				= is_bool( $user );
			$user_null				= is_null( $user );
			
			if( $user_int || $user_double ){
				$data[ 'new_string' ][ $k ] = $user;
			}
			else if( $user_string ){
				$data[ 'new_string' ][ $k ] = $user;
			}
			else if( $user_bool || $user_null ){
				switch( $user ){
					// .. если есть значение - оставить, если нет значения - удалить
					case false :
						if( $get ) (bool)$data[ 'new_string' ][ $k ] = false;
						break;
					// .. оставить элемент
					case true :
						if( $get ) (bool)$data[ 'new_string' ][ $k ] = true;
						break;
					default :
						break;
				}
			}
		}
		
		if( $use_all_adress_data == 1 ){
			// Проходим GET
			foreach( $data[ 'get_string' ] as $k=>$v ){
				$get				= $data[ 'get_string' ][ $k ];
				$new			= $data[ 'new_string' ][ $k ];
				
				if( isset( $new ) ){
					// Если есть новый
					if( $new === false ){
						unset( $data[ 'new_string' ][ $k ] );
					}
					else if( $new === true ){
						$data[ 'new_string' ][ $k ] = $get;
					}
					else {
					}
				}
				else {
					$data[ 'new_string' ][ $k ] = $get;
				}
			}
		}
		else {
			// Проходим GET
			foreach( $data[ 'new_string' ] as $k=>$v ){
				$get = isset( $data[ 'get_string' ][ $k ] ) ? $data[ 'get_string' ][ $k ] : null;
				$new = $data[ 'new_string' ][ $k ];
				
				if( $new === false ) unset( $data[ 'new_string' ][ $k ] );
				else if( $new === true ) $data[ 'new_string' ][ $k ] = $get;
			}
		}
		
		
		// Проходим NEW
		if( ! empty( $data[ 'new_string' ] ) ){
			$return2 = '?';
			foreach( $data[ 'new_string' ] as $k=>$v ){
				$return2 .= $k . '=' . $v . '&';
			}
			$return = $return . $return2;
		}
		else {
		}
		// print $this->my_vardump( $data , 'data' );
		
		//
		//
		//
		
		if( $return[ strlen( $return ) - 1 ] == '&' ) $return = mb_substr( $return , 0 , mb_strlen( $return ) - 1 );
		
		if( ! empty( $anchor ) ) $return .= '#' . $anchor;
		# print '<div>return: ' . $return . ' (anchor: ' . $anchor . ')</div>';
		
		# domain : allias
		if( is_bool( $allias_address ) ){
			if( $allias_address === true ) {
				$allias = '';
				foreach( $this->library->url->static_domain as $value ){
					is_null( $value ) ?: $allias .= $value . '.';
				}
				$allias = mb_substr( $allias , 0 , mb_strlen( $allias ) - 1 );
				
				$return =  $allias . $return;
				if( $host ) $return = $this->connect_type . $return;
			} else {
			}
		} else if( is_array( $allias_address ) ){
			if( ! empty( $allias_address ) ) {
				$allias = '';
				foreach( $allias_address as $value ){
					is_null( $value ) ?: $allias .= $value . '.';
				}
				$allias = mb_substr( $allias , 0 , mb_strlen( $allias ) - 1 );
				
				$return =  $allias . $return;
				if( $host ) $return = $this->connect_type . $return;
			}
		}
		
		return $return;
	}
	public function ahref2 ( array $base_vars ,
												bool $use_all_adress_data = false ,
												bool|string|array $base_address = true ,
												bool|array $allias_address = [ ] ,
												string $after_address = '' ,
												string $anchor = '' ,
												string $outside = '' , 
												bool $host = true ) {
		$return = '';
		// $datauser						<> Пользовательские данные [ data_string=>string , [ data_array=>[ data1_string=>string , data2_string=>string ] ] ...
		// $use_all_adress_data	<> Использовать переменные адресной строки [ 0 <> Нет , 1 <> Да ]
		// $base_link						<> [ 0 <>  Нет , 1 <> Да , (string) <> Использовать значене , (Array)[ val1 , val2 , .. ] <> Только выбранные значение
		// 
		// 	$datauser = array()
		// 		attr1		>> атрибут1
		// 		val1		>> значение1
		// 		...
		// 	$use_all_adress_data = 0
		// 		...
		// 	$base_link = array( )
		// 		type					>> *inside | outside
		// 		protocol			>> *https | http | ftp | ...
		// 		domain				>> *domain.com
		// 		subdomain		>> *www | array( www , www2 , www.www3 ... )
		
		# $class__index = $this->class__index;
		
		// .. алгоритм
		if( $outside ) $return = $outside;
		else $return = '';
		
		if( is_bool( $base_address ) ){
			if( $base_address === false ){
				$return .= DSA;
			}
			else if( $base_address === true ){
				$address = $this->url( )->url( );
				foreach( $address as $k=>$v ){
					if( $v != '' ) $return .= DSA . $v;
				}
			}
		}
		else if( is_string( $base_address ) ){
			$return = DSA . $base_address;
		}
		else if( is_array( $base_address ) ){
			if( empty( $base_address ) ){
				$return .= DSA;
			}
			else{
				foreach( $base_address as $v ){
					$return .= DSA . $v;
				}
			}
		}
		
		// .. after address
		
		if( $after_address != '' ){
			$return .= DSA . $after_address;
		}
		//
		//
		//
		
		
		$data[ 'user' ] = $base_vars;
		
		$data[ 'get' ] = $_GET;
		$data[ 'user_string' ]= $this->url( )->get_return( $data[ 'user' ] );
		$data[ 'get_string' ] = $this->url( )->get_return( $data[ 'get' ] );
		$data[ 'new_string' ] = [ ];
		
		# print '<div>user_string</div>';
		# foreach( $data[ 'user_string' ] as $k=>$v ) print '<div>' . $k . ' = ' . $v . '</div>';
		# print '<div>get_string</div>';
		# foreach( $data[ 'get_string' ] as $k=>$v ) print '<div>' . $k . ' = ' . $v . '</div>';
		
		// Проходим все данные GET
		foreach( $data[ 'user_string' ] as $k=>$v ){
			$get = isset( $data[ 'get_string' ][ $k ] ) ? $data[ 'get_string' ][ $k ] : null;
			$user = $data[ 'user_string' ][ $k ];
			
			$user_int				= is_integer( $user );
			$user_double		= is_double( $user );
			$user_string		= is_string( $user );
			$user_bool				= is_bool( $user );
			$user_null				= is_null( $user );
			
			if( $user_int || $user_double ){
				$data[ 'new_string' ][ $k ] = $user;
			}
			else if( $user_string ){
				$data[ 'new_string' ][ $k ] = $user;
			}
			else if( $user_bool || $user_null ){
				switch( $user ){
					// .. если есть значение - оставить, если нет значения - удалить
					case false :
						if( $get ) (bool)$data[ 'new_string' ][ $k ] = false;
						break;
					// .. оставить элемент
					case true :
						if( $get ) (bool)$data[ 'new_string' ][ $k ] = true;
						break;
					default :
						break;
				}
			}
		}
		
		if( $use_all_adress_data == 1 ){
			// Проходим GET
			foreach( $data[ 'get_string' ] as $k=>$v ){
				$get				= $data[ 'get_string' ][ $k ];
				$new			= $data[ 'new_string' ][ $k ];
				
				if( isset( $new ) ){
					// Если есть новый
					if( $new === false ){
						unset( $data[ 'new_string' ][ $k ] );
					}
					else if( $new === true ){
						$data[ 'new_string' ][ $k ] = $get;
					}
					else {
					}
				}
				else {
					$data[ 'new_string' ][ $k ] = $get;
				}
			}
		}
		else {
			// Проходим GET
			foreach( $data[ 'new_string' ] as $k=>$v ){
				$get = isset( $data[ 'get_string' ][ $k ] ) ? $data[ 'get_string' ][ $k ] : null;
				$new = $data[ 'new_string' ][ $k ];
				
				if( $new === false ) unset( $data[ 'new_string' ][ $k ] );
				else if( $new === true ) $data[ 'new_string' ][ $k ] = $get;
			}
		}
		
		
		// Проходим NEW
		if( ! empty( $data[ 'new_string' ] ) ){
			$return2 = '?';
			foreach( $data[ 'new_string' ] as $k=>$v ){
				$return2 .= $k . '=' . $v . '&';
			}
			$return = $return . $return2;
		}
		else {
		}
		// print $this->my_vardump( $data , 'data' );
		
		//
		//
		//
		
		if( $return[ strlen( $return ) - 1 ] == '&' ) $return = mb_substr( $return , 0 , mb_strlen( $return ) - 1 );
		
		if( ! empty( $anchor ) ) $return .= '#' . $anchor;
		
		# domain : allias
		if( is_bool( $allias_address ) ){
			if( $allias_address === true ) {
				$allias = '';
				foreach( $this->url->static_domain as $value ){
					is_null( $value ) ?: $allias .= $value . '.';
				}
				$allias = mb_substr( $allias , 0 , mb_strlen( $allias ) - 1 );
				
				$return =  $allias . $return;
				if( $host ) $return = $this->connect_type . $return;
			} else {
			}
		} else if( is_array( $allias_address ) ){
			if( ! empty( $allias_address ) ) {
				$allias = '';
				foreach( $allias_address as $value ){
					is_null( $value ) ?: $allias .= $value . '.';
				}
				$allias = mb_substr( $allias , 0 , mb_strlen( $allias ) - 1 );
				
				$return =  $allias . $return;
				if( $host ) $return = $this->connect_type . $return;
			}
		}
		
		return $return;
	}
}

?>
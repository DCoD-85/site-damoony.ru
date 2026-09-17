<?PHP

class library_dir {
	private null|library $library = null;
	public library_dir_data $functions_dir_data;
	
	public function __construct( null|library $library ) {
		$this->library = $library;
	}
	
	# create
	public function create ( string $pathOutside ) {
		# $class__index = $this->class__index;
		
		# if( is_string( $pathOutside ) ) $pathOutside = $class__index->dirs[ $pathOutside ];
		
		if( is_array( $pathOutside ) ){
			$patha = DSA;
			// $path = DS;
			foreach( $pathOutside as $k ){
				$patha .= $k . DSA;
				$path .= $k . DS;
				if( ! is_dir( $path ) ){
					mkdir( $path );
				}
			}
		} else {
			return '';
		}
		
		return [ 'ds'=>$path , 'dsa'=>$patha ];
	}

	public function check_create( string $path ) {
		if( empty( $path ) ) return $path;

		$path = explode( DS , $path );
		if( count( $path ) == 0 ){
			if( ! is_dir( $path ) ) mkdir( $path );
			return $path;
		}

		$current = '';
		foreach( $path as $value ){
			if( empty( $value ) ) return '';

			$current .= $value;
			print 'Current: ' . $current . '<br />';
			if( ! is_dir( $current ) ){
				print '<div>mkdir</div>';
				if( ! mkdir( $curent ) ) print 'BAD<br />';
			}
			$current .= DS;

		}

		return $current;
	}

	public function get ( 
		string $dir = DSA ,				# путь
		bool $included = false ,
		bool $dirs = true ,
		bool $files = true ,
		array &$dir_result = [ ] ) {
		#
		$dir_current = $dir;
		if( is_dir( $dir_current ) ){
			if( $dh = opendir( $dir_current ) ){
				while( ( $file = readdir( $dh ) ) !== false ){
					switch( $file ){
						// Исключаем следующие файлы
						case '.' :
						case '..' :
							break;
						default :
							$dir_current = $dir . $file;
							$is_dir = is_dir( $dir_current );
							$is_file = is_file( $dir_current );
							
							if( ( $dirs === true && $is_dir === true ) || ( $files === true && $is_file === true ) ){
								$dir_result[ $dir_current ] = $file;
							}

							if( $included )
								$is_dir ? $this->get( $dir_current . DS , $included , $dirs , $files , $dir_result ) : false;
							
							break;
					}
				}
			}
		}

		return $dir_result;
	}
	
	public function dir_isset( string $dir ){
		return is_dir( $dir );
	}
	public function dir_scan(
		string $dir = DSA ,				# путь
		bool $included = false ,
		bool $show_dirs = true ,
		bool $show_files = true ,
		string $dir_base = '' , 
		functions_dir_data &$data = new functions_dir_data( )
	) {
		$dir_base = $dir_base !== '' ? $dir_base : $_SERVER[ 'DOCUMENT_ROOT' ];
		# if( mb_strstr( $dir , DSA ) ) error( 'Error' , 'Error: mb_strstr( );' , __FILE__ , __LINE__ );
		if( $dir === ''  || $dir === DSA ) $dir = $dir_base . DSA;
		if( $dir[ strlen( $dir ) - 1 ] !== DSA ) $dir .= DSA;
		
		if( mb_substr( $dir , 0 , mb_strlen( $dir_base ) ) !== $dir_base ) $dir = $dir_base . ( $dir[ 0 ] !== DSA ? DSA . $dir : $dir );
		if( mb_strstr( $dir , DSA . '..' . DSA ) ) error( 'Error' , 'Error: mb_strstr( );' , __FILE__ , __LINE__ );
		
		$dir_current = $dir;
		# $dir_result = $dir_current
		$dir_results = $this->dir_scan_sort( $dir_current , $dir_base );
		
		
		foreach( $dir_results as $dir_result ) {
			$dir_next = $dir . $dir_result;
			$is_dir = is_dir( $dir_next );
			$is_file = is_file( $dir_next );
			
			if( $show_dirs ) {
				if( $is_dir ) $data->insert( $is_dir ? 'dir' : 'file' , $dir , $dir_result );
			}
			if( $show_files ) {
				if( $is_file ) $data->insert( $is_dir ? 'dir' : 'file' , $dir , $dir_result );
			}
			
			if( $included === true ) {
				if( $is_dir ) $this->dir_scan( $dir_next . DSA , $included , $show_dirs , $show_files , $dir_base , $data );
			}
		}
		return $data;
	}
	
	public function dir_scan_sort( string $dir , string $dir_base ) {
		$dir_result = [ ];
		$dir_current = $dir;
		if( is_dir( $dir_current ) ){
			if( $dh = opendir( $dir_current ) ){
				while( ( $file = readdir( $dh ) ) !== false ){
					switch( $file ){
						// Исключаем следующие файлы
						case '.' :
						case '..' :
							break;
						default :
							$is_dir = is_dir( $dir . $file );
							$is_file = is_file( $dir . $file );
							# print $dir_current . $file . '<br />';
							$dir_result[ ] = $file;
							break;
					}
				}
			}
		}
		
		# $dir_result = scandir( $dir );
		
		$result_folders = [ ];
		$result_files = [ ];
		foreach( $dir_result as $result ) {
			if( $dir_current . $result == '.' || $dir_current . $result == '..' ) continue;
			$is_dir = is_dir( $dir_current . $result );
			$is_file = is_file( $dir_current . $result );
			if( $is_dir ) $result_folders[ ] = $result;
			if( $is_file ) $result_files[ ] = $result;
		}
		
		$dir_result = array_merge( $result_folders , $result_files );
		return $dir_result;
	}
	
	public function dir_scan_sort2( string $dir , string $dir_base ) {
		
		$dir_result = scandir( $dir_base . $dir );
		
		$result_folders = [ ];
		$result_files = [ ];
		foreach( $dir_result as $result ) {
			if( $result == '.' || $result == '..' ) continue;
			$is_dir = is_dir( $dir_base . $result );
			$is_file = is_file( $dir_base . $result );
			if( $is_dir ) $result_folders[ ] = $result;
			if( $is_file ) $result_files[ ] = $result;
		}
		
		$dir_result = array_merge( $result_folders , $result_files );
		return $dir_result;
	}
	
	
	public function dir_get(
		string $dir ,				# путь
		int  $dir_only = 0 ,	# Тип данных [ -1 - файлы , 0 - папки и файлы , 1 - папки ]
		bool $dir_included = false , # Вложенные папки [ false - нет , true - да ]
		string $dir_base = ''
	) {
		
		$dir_base = $dir_base ?: $_SERVER[ 'DOCUMENT_ROOT' ];
		print 'dir base: ' . $dir_base . '<br />';
		$return = [ ];
		print '<div>dir: ' . $dir . '</div>';
		
		if( is_dir( $dir_base . $dir ) ){
			if( $dh = opendir( $dir_base . $dir ) ){
				while( ( $file = readdir( $dh ) ) !== false ){
					$dir_next = $dir . DS . $file;
					$dir_full = $dir_base . $dir_next;
					
					if( is_dir( $dir_full ) || is_file( $dir_full ) ){
						switch( $file ){
							// Исключаем следующие файлы
							case '.' :
							case '..' :
								break;
							default :
								$isdir = is_dir( $dir_full );
								$isfile = is_file( $dir_full );
								print '<div>$dir_full: ' . mb_substr( $dir_full , mb_strlen( $dir_base ) ) . ' | dir: ' . $isdir . ' | file: ' . $isfile . '</div>';
								# if( $isdir ) $this->dir_get( $dir_next , $dir_only , $dir_included , $dir_base);
								break;
						}
					}
				}
			}
		}
	}
	
	public function dir_get_options( $file , $path , string $path_full , $options ){
		
		$type = is_dir( $path_full ) ? 'dir' : 'file' ;
		
		if( $options && is_array( $options ) ){
			if( $options[ 'search_in' ] != false ){
				$search = $options[ 'search_in' ];
				if( $str = strstr( $file , $search ) ){
					$return = $path . $file;
				}
			}
			else if( $options[ 'file' ][ 'exp_is' ] ){
				if( is_array( $options[ 'file' ][ 'exp_is' ] ) ){
					for( $q = 0 ; $q < count( $options[ 'file' ][ 'exp_is' ] ) ; $q++ ){
						$tt = $options[ 'file' ][ 'exp_is' ][ $q ];
						if( $str = strstr( $file , $options[ 'file' ][ 'exp_is' ][ $q ] ) ){
							$return = $path . $file;
							break;
						}
					}
				}
				else{
					if( $str = substr( $file , 0 , strripos( $file , $options[ 'file' ][ 'exp_is' ] ) ) ){
						$return = $path . $file;
					}
				}
			}
			else{
				$return = $path . $file;
			}
		}
		else{
			$return = $path_full;
		}
		
		return $return;
	}
}

class functions_dir_data {
	public array $data = [ ];
	public string $document_root;
	public function __construct( ) {
		$this->document_root = $_SERVER[ 'DOCUMENT_ROOT' ];
	}
	public function insert( string $type , string $folder, string $file ){
		$this->data[ ] = new functions_dir_fileinfo( $this , $type , $folder , $file );
	}
	
	public function count( ) {
		return count( $this->data );
	}
	public function as_array ( null|string $type = null ) {
		# if( is_string( $type ) && $type == 'type'|'folder'|'file'|'fullpath' ) die( 'bad' );
		$data = $this->data;
		$return = [ ];
		if( ! empty( $data ) ){
			foreach( $data as $value ){
				$return[ ] = $value->as_array( $type );
			}
		}
		return $return;
		
	}
	
	public function show( ){
		$data = $this->data;
		$return = '';
		if( ! empty( $data ) ){
			foreach( $data as $value ){
				
				$return .= '<div>' . $value->release( ) . '</div>';
			}
		}
		return $return;
	}
}

class functions_dir_fileinfo {
	public functions_dir_data $functions_dir_data;
	# vars
	public string $type;
	public string $folder;
	public string $file;
	public string $fullpath;
	# methods
	public function __construct( functions_dir_data $functions_dir_data , string $type , string $folder, string $file ) {
		$this->functions_dir_data = $functions_dir_data;
		$this->type = $type;
		$this->folder = $folder;
		$this->file = $file;
		$this->fullpath = $folder . $file;	
	}
	
	public function as_array ( null|string $type = null ) {
		$document_root = $this->functions_dir_data->document_root;
		if( $type == null ) {
			$type = $this->type;
			$folder = $this->folder; # mb_substr( $this->folder , mb_strlen( $document_root ) );
			$file = $this->file; # mb_substr( $this->folder , mb_strlen( $document_root ) );
			$fullpath = mb_substr( $this->fullpath , mb_strlen( $document_root ) );
			$return = '';
			$return .= '[ type: ' . $type . ' ] [ folder: ' . $folder . ' ] [ file: ' . $file . ' ] [ fullpath: ' . $fullpath . ' ]';
			return [
				'type'=>$type ,
				'folder'=>$folder ,
				'file'=>$file ,
				'fullpath'=>$fullpath ,
			];
		} else {
			return $this->{$type};
		}
	}
	public function release( ){
		$document_root = $this->functions_dir_data->document_root;
		$type = $this->type;
		$folder = $this->folder; # mb_substr( $this->folder , mb_strlen( $document_root ) );
		$file = $this->file; # mb_substr( $this->folder , mb_strlen( $document_root ) );
		$fullpath = mb_substr( $this->fullpath , mb_strlen( $document_root ) );
		$return = '';
		$return .= '[ type: ' . $type . ' ] [ folder: ' . $folder . ' ] [ file: ' . $file . ' ] [ fullpath: ' . $fullpath . ' ]';
		return $return;
	}
}



	/*
	
	# Получить данные каталога
	public function archive_dir_get(
		string $dir ,				# путь
		int  $dir_only = 0 ,	# Тип данных [ -1 - файлы , 0 - папки и файлы , 1 - папки ]
		int $dir_included = 0 , # Вложенные папки [ 0 - нет , 1 - да ]
		string $asList = 'simple' ,
		array $options = [ ] ,
		string $dir2 = ''
	) {
		// asList					
		//								-- array :						путь к файлу = array( тип файла [ папка , файл ] , название файла , путь к файлу )
		//								-- json :						 путь к файлу = JSON( тип файла [ папка , файл ] , название файла , путь к файлу )
		//								-- simple :					  путь к файлу = название файла
		//								-- simple_type :		путь к файлу = тип файла ( файл / дирректория )
		//								-- uno : 
		// options				- опции						array( )
		// Список опций
		// dir2					- рабочая дирректория
		
		$dir_base = $_SERVER[ 'DOCUMENT_ROOT' ];
		$return = [ ];
		$dir = $dir_base . $dir;
		
		if( is_dir( $dir ) ){
			if( $dh = opendir( $dir ) ){
				while( ( $file = readdir( $dh ) ) !== false ){
					$dir_full = $dir . $file;
					
					if( is_dir( $dir_full ) || is_file( $dir_full ) ){
						switch( $file ){
							// Исключаем следующие файлы
							case '.' :
							case '..' :
								break;
							default :
								// Костыль с русской кодировкой!
								// $file = iconv( 'Windows-1251' , 'UTF-8' , $file );
								// $file = iconv( 'UTF-8' , 'Win-1251' , $file );
								
								$dir3 = empty( $dir2 ) ? $file : $dir2 . DS . $file;
								
								$bOk = $this->dir_get_options( $file , ( empty( $dir2 ) ? '' : $dir2 . DS ) , $dir3 , $options );
								
								if( $bOk ){
									if( $dir_included == 0 ){
										// Папки
										if( is_dir( $dir_full ) && $dir_only >= 0 ){
											$returntype[ ] = new functions_dir_fileinfo( 'dir' , $file , $dir3 );
											# $return[ $dir3 ] = [ 'dir' , $file , $dir3 ]; // $file;
										}
										// Файлы
										else if( is_file( $dir_full ) && $dir_only <= 0 ){
											$returntype[ ] = new functions_dir_fileinfo( 'file' , $file , $dir3 );
											# $return[ $dir3 ] = [ 'file' , $file , $dir3 ]; // $file;
										}
									}
									else {
										// Папки
										if( is_dir( $dir_full ) ){
											$return[ $dir3 ] = [ 'dir' , $file , $dir3 ];
											$returntype[ ] = new functions_dir_fileinfo( 'dir' , $file , $dir3 );
											$return = array_merge( $return , $this->dir_get( $dir_full . DS , $dir_only , $dir_included , $asList , $options , $dir3 ) );
										}
										// Файлы
										else if( is_file( $dir_full ) && $dir_only <= 0 ){
											$return[ $dir3 ] = [ 'file' , $file , $dir3 ];
											$returntype[ ] = new functions_dir_fileinfo( 'file' , $file , $dir3 );
										}
									}
								}
								
								break;
						}
					}
					
				}
					
				if( $dir2 == '' && isset( $return ) ){
					// Если показывать как список (без доп массивов)
					$tmp = [ ];
					if( $asList == 'array' || $asList == 'json'){
						foreach( $return as $k=>$v ){
							$tmpName = $k;
							if( $asList == 'array' )			$tmpValue = $return[ $k ];
							else if( $asList == 'json' )	$tmpValue = json_encode( $return[ $k ] );
							
							$tmp[ $tmpName ] = $tmpValue;
						}
						$return = '';
						$return = $tmp;
					}
					else if( $asList == 'simple' ){
						foreach( $return as $k=>$v ){
							$tmpName = $k;
							$tmpType = $v[ 0 ];
							$tmpValue = $v[ 1 ];
							
							$add = false;
							switch( $tmpType ){
								case 'dir' :
									if( $dir_only >= 0 ) $tmp[ $tmpName ] = $tmpValue;
									break;
								case 'file' :
									if( $dir_only <= 0 ) $tmp[ $tmpName ] = $tmpValue;
									break;
								default : 
									break;
							}
							$tmp[ $tmpName ] = $tmpValue;
						}
						$return = '';
						$return = $tmp;
					}
					else if( $asList == 'simple_type' ){
						foreach( $return as $k=>$v ){
							$tmpName = $k;
							$tmpValue = $v[ 0 ];
							
							$tmp[ $tmpName ] = $tmpValue;
						}
						$return = '';
						$return = $tmp;
					}
					else if( $asList == 'uno' ){
						$count = 0;foreach( $return as $k=>$v ){
							$tmp[ $k ] = $v[ 2 ];
							if( $count == 0 ) break;
						}
						$return = $tmp;
					}
					else{
					}
				}
				
				closedir( $dh );
			}
		} else {
		}
		
		return $return;
	}
	*/
?>
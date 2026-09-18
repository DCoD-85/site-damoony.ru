<?PHP

class config {
	# vars
	public null|main $main;
	public string $path_domains = __DIR__ . DS . 'config' . DS . 'domains.txt.inc';
	public string $path_domains_real = __DIR__ . DS . 'config' . DS . 'domains-real.txt.inc';
	public string $path_domains_available = __DIR__ . DS . 'config' . DS . 'domains-available.txt.inc';
	public string $path_alliases = __DIR__ . DS . 'config' . DS . 'alliases.txt.inc';
	# methods
	public function __construct ( null|main $main ) {
		$this->main = $main;
	}

	public function init ( ) {
		# 
		# доделать
		# $this->domain_init( );
		$this->db_init( );
	}
	
	public function domain_init ( ) {
		$main = $this->main;

		$return_settings = [
			'domains_real'=>[ ] ,
			'domains_available'=>[ ] ,
			'alliases_real'=>[ ] , 
			'alliases_available'=>[ ] ];

		$file = $main->library->file( );
		$domains = $file->read( $this->path_domains );
		$domains = explode( '---' , $domains );

		foreach( $domains as $domain ){
			$domain = explode( '::between::' , $domain );

			$domain_name = $domain[ 0 ];
			$domain_settings = trim( $domain[ 1 ] );
			$domain_settings = explode( PHP_EOL , $domain_settings );


			print '<div>domain: ' . $domain_name . '</div>';
			foreach( $domain_settings as $setting_key=>$settings_value ){
				$setting = explode( '|' , $settings_value );
				$setting_name = trim( $setting[ 0 ] );
				$setting_value = trim( $setting[ 1 ] );
				print '<div>setting_name: ' . $setting_name . ' | ' . $setting_value . '</div>';

				switch( $setting_name ){
					case 'available' :
						$setting_value = mb_ereg_replace( '\'' , '' , $setting_value );
						$return_settings[ $domain_name ] = $setting_value;
						break;
					case 'real' :
						$setting_value = trim( $setting_value );
						$setting_value = explode( ' ' , $setting_value );
						
						$domains_real = [ ];
						foreach( $setting_value as $value ){
							$domains_real[ $value ] = $domain_name;
							print $value . ' - ' . $domain_name . '<br />';
						}
						$return_settings[ 'domains_real' ] = $domains_real;
						break;
					case 'alliases_real' :
						# $alliases_real
						break;
					case 'alliases_available' :
						break;
					default : break;
				}
			}
			# print '<div>
			# 	<div>' . $domain[ 0 ] . '</div>
			# 	<div>' . $domain[ 1 ] . '</div>
			# </div>';
		}
	}


	// Инициализация класса
	public function db_init ( ){
		// Путь к базовым настройкам сайта
		$path = __DIR__ . DS . 'config' . DS . 'settings-test.xml.inc';
		if( ! is_file( $path ) )
			error_file( $path , __FILE__ , __LINE__ );
		else {
			$dom = simplexml_load_file( $path );
			$count = 0;
			for( $q = 0 ; $q < count( $dom->row ) ; $q++ ){
				if( $dom->row[ $q ][ 'disabled' ] == false ){
					foreach( $dom->row[ $q ] as $k=>$v ){
						$v = iconv( 'utf-8' , 'windows-1251' , $v );
					}
					define( $dom->row[ $q ]->name , $dom->row[ $q ]->value );
				}
			}
		}
		
		return true;
	}
	// -----------------------------------------------------------------------------
	
	/*
	// -----------------------------------------------------------------------------
	public function _check( array $fo ){
		$class__index = $this->class__index;
		
		## -- DEBUG -- ##
		$class__index->loader->debug->debugtmp( $this, '', [ __FILE__, __LINE__, __METHOD__ ] , $fo );
		$return = true;
		// $class_report = $this->class_index->class_report;
		// $class_report->reportdatamini_insert( 'Класс отчетов : проверка.' , array( __FILE__ , __LINE__ , __CLASS__ , __FUNCTION__ ) , $file_output );
	
		return $return;
	}
	// -----------------------------------------------------------------------------
	
	
	// -----------------------------------------------------------------------------
	// Функция возвращает путь к скрипту системы
	public function core_name_get( array $fo ){
		$class__index = $this->class__index;
		
		## -- DEBUG -- ##
		$class__index->loader->debug->debugtmp( $this, '', [ __FILE__, __LINE__, __METHOD__ ] , $fo );
		
		$apath_uri = $class__index->class__functions->returnURL( 'url' );
		// 
		for( $q = 0 ; $q < count( $apath_uri ) ; $q++ ){
			$row = $apath_uri[ $q ];
			
			if( $row ){
				if( preg_match( '/^.+.core$/' , $row ) == true ){
					$str = str_replace( '.core' , '' , $row );
					$z_path = $class__index->baseDir . $class__index->path[ 'cores' ] . $str . DS;
					
					if( is_dir( $z_path ) && file_exists( $z_path . 'index.php' ) ){
						$path = $str;
						break;
					}
					else{
						$path = 'errorsScript' . DS . '404.php';
						$this->error = '1';
					}
				}
				else if( preg_match( '/^--settings$/' , $row ) == true ){
					$this->path[ 'core' ] = '__cores' . DS . '--settings' . DS;
					$str = 'index';
					$z_path = $this->path[ 'core' ] . $str . DS;
					
					if( is_dir( $z_path ) && file_exists( $z_path . 'index.php' ) ){
						$path = $str;
					}
					else{
						$path = 'errorsScript/404.php';
						$this->error = '1';
					}
				}
				else{
					$path = 'index';
				}
			}
			else{
				$path = 'index';
			}
		}
		
		// $this->path = $path;
		$class__index->coreName = $path;
	}
	// Возвращает настройки вывода страницы
	
	public function settings_page_return( array $fo ){
		$class__index = $this->class__index;

		## -- DEBUG -- ##
		$class__index->loader->debug->debugtmp( $this, '', [ __FILE__, __LINE__, __METHOD__ ] , $fo );
		
		$d = [ 'type'=>'' , 'data'=>'' , 'charset'=>'' , 'error'=>false ];
		
		$path = $class__index->baseDir . $class__index->path[ 'config' ] . 'settings-page.xml.inc';
		
		if( is_file( $path ) ){
			$dom = simplexml_load_file( $path );
			
			$data = [ ];
			for( $q = 0 ; $q < count( $dom->row ) ; $q++ ){
				$r = $dom->row[ $q ];
				if( $r[ 'disabled' ] == false ){
					foreach( $r as $k=>$v ){
						// $v = iconv( 'utf-8' , 'windows-1251' , $v );
						// print $k.' == '.$v.'<br />';
					}
					$data[ (string)$r->name ] = [ 'value' => $r->value , 'comment' => $r->comment ];
				}
			}
			// print '</div>';
			
			$d[ 'type' ] = $data[ 'use-type' ][ 'value' ];
			
			if( $data[ 'use-type' ][ 'value' ] == 'header' ){
				$d[ 'data' ] = 'content-type: '.$data[ 'doc-type' ][ 'value' ].'; charset='.$data[ 'doc-lang-charset' ][ 'value' ];
			}
			else{
				$d['data'] = '<meta http-equiv="Content-Type" content="'.$data[ 'doc-type' ][ 'value' ].'; charset='.$data[ 'doc-lang-charset' ][ 'value' ].'" />';
			}
			$d[ 'charset' ] = [
				'current'=>$data[ 'doc-lang-charset' ][ 'value' ] ,
				'from'=>$data[ 'doc-lang-charset-from' ][ 'value' ] ,
				'to'=>$data[ 'doc-lang-charset-to' ][ 'value' ]
			];
		}
		else{
			$d[ 'type' ] = false;
			$d[ 'data' ] = false;
			$d[ 'error' ] = true;
		}
		
		return [ 'type'=>$d[ 'type' ] , 'data'=>$d[ 'data' ] , 'charset'=>$d[ 'charset' ] , 'error'=>$d[ 'error' ] ];
	}
	
	// Возвращаем список переменных [значений]
	public function settings_test_return( $type = 'default' ){
		$class__index = $this->class__index;
		$d = '';
		
		switch( $type ){
			case 'db' :
				break;
			case 'settings-test' :
				break;
			case 'settings' :
				break;
			default :
				$path = $class__index->baseDir . $class__index->path[ 'config' ] . 'settings-test.xml.inc';
				break;
		}
		
		if( is_file( $path ) ){
			$dom = simplexml_load_file( $path );
			
			$data = array();
			for( $q = 0 ; $q < count( $dom->row ) ; $q++ ){
				$r = $dom->row[ $q ];
				if( $r[ 'disabled' ] == false ){
					foreach( $r as $k=>$v ){
					}
					$d[ (string)$r->name ] = array(
						'name'=>$r->name ,
						'value'=>$r->value ,
						'about'=>$r->about ,
						'comment'=>$r->comment ,
						'notedited'=>$r[ 'notedited' ]
					);
				}
			}
			//print '</div>';
		}
		else{
			$d = [ ];
		}
		
		return $d;
	}
		*/
}

?>
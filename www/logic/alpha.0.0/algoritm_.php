<?PHP

class algoritm_ {
	# vars
	public main $main;
	
	public \damoony\www\algoritm|\damoony\login\algoritm|\damoony\oxydog\algoritm $algoritm;
	public array $args = [ ];
	public null|algoritmdata_ $algoritmdata = null;
	public string $path = '';

	public string $path_class = 'algoritm';
	public string $class = 'algoritm';
	public array $algoritm_classes = [ ];
	public string $basepath = '';
	public string $baseclass = '';
	public string $errorpath = '';
	public string $errorclass = '';
	public string $authpath = '';
	public string $authclass = '';
	public int $count_pages = 0;

	protected function construct ( main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		/*
		if( ! empty( $args ) ){
			if( isset( $args[ 'base' ] ) ){
				$this->basepath = $args[ 'base' ][ 'basepath' ];
				$this->baseclass = $args[ 'base' ][ 'baseclass' ];
				$this->errorpath = $args[ 'base' ][ 'errorpath' ];
				$this->errorclass = $args[ 'base' ][ 'errorclass' ];
				$this->authpath = $args[ 'base' ][ 'authpath' ];
				$this->authclass = $args[ 'base' ][ 'authclass' ];
			}
		}
		*/
		$this->algoritmdata = new algoritmdata_( );
	}

	public function clear ( ) {
		$url = $this->main->library->url( );
		$url->init( );
	}

	public function debug ( ) {
	}

	public function include_ext ( object $object , array $available , bool $search = false , bool $default = false ){
		$main = $this->main;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$static_domain = $main->main_settings->domain_accepted( false );
		$static_allias = $url->static_allias[ 1 ];

		$in_array = false;
		$args = [ ];
		
		$url_base_pos = count( explode( '_' , get_class( $object ) ) ) - 1;
		$url_base_real = $url_base;

		
		if( $search === true ){
			# print 'count $available: ' . count( $available ) . '<br />';
			if( ! empty( $available ) ){
				foreach( $available as $key=>$value ){
					# print '<div># ' . $value . '</div>';
					# print 'available value: ' . $value . '<br />';
					$mbstr = mb_strpos( $value , '|' );
					if( $mbstr ){
						$base = explode( '|' , $value );
						mb_ereg( '^' . $base[ 0 ] . '$' , $url_base_real[ $url_base_pos ] , $matches );

						if( ! empty( $matches ) ){
							# print $value . '<br />';
							$args[ $matches[ 1 ] ] = $matches[ 2 ];
							$url_base_real[ $url_base_pos ] = $base[ 1 ];
							$available[ $key ] = $base[ 1 ];
							break;
						}
					} else if( $value === $url_base_real[ $url_base_pos ] ) break;
				}
			}
		} else {
			// 
		}

		$in_array = in_array( $url_base_real[ $url_base_pos ] , $available ) ? $url_base_real[ $url_base_pos ] : false ;
		
		$this->main->algoritm->basepath .= $url_base_real[ $url_base_pos ] . DS;
		$this->main->algoritm->baseclass .= '_' . $url_base_real[ $url_base_pos ];
		
		$class = $this->main->algoritm->baseclass;
		$path = $this->main->algoritm->basepath . $class . '.php';
		$class = '\\' . $static_domain . '\\' . $static_allias . '\\' . $class;

		# print 'this: ' . get_class( $this ) . '<br />';
		# print (int)$search . '<br />';
		# print 'in_array: ' . $in_array . '<br />';
		# print 'base real: ' . $url_base_real[ $url_base_pos ] . '<br />';
		# print '<div>[ debug ] available: ' . implode( ' | ' , $available ) . '</div>';
		# print '<div>[ debug ] base path: ' . $path . '</div>';
		# print 'object: ' . get_class( $object ) . '<br />';
		# print '<div>base class: ' . $class . '</div>';

		if( $in_array && include_once $path ){
			
			# print '<div>[ debug ] class exists: ' . (int)class_exists( $class ) . '</div>';

			if( class_exists( $class ) ){
				
				$this->algoritm_classes[ $url_base_pos ] = new $class( $main , $args );
				if( $this->algoritm_classes[ $url_base_pos ]->logged == false
					|| ( $this->algoritm_classes[ $url_base_pos ]->logged == true && $main->user->is_guest( ) == false )
				){
					$this->algoritm_classes[ $url_base_pos ]->algoritm( );
					$this->algoritm_classes[ $url_base_pos ]->release( );
				} else {
					$this->error_( $this->algoritm_classes[ $url_base_pos ] );
				}
			} else {
				http_response_code( 403 );
				print '<div>' . gettype( $this->algoritm_classes[ $url_base_pos - 1 ] ) . '</div>';
				$this->algoritm_classes[ $url_base_pos - 1 ]->release( );
			}
			
		} else {
			# print 'gettype: ' . (int)$in_array . ' - ' . gettype( $in_array ) . ' - ' . gettype( $object->algoritmdata->default ) . ' - ' . (int)$object->algoritmdata->default . '<br />';

			if( ( is_string( $in_array ) ) && $object->algoritmdata->default === true ){
				$object->default( ) ? true : $this->error_( $object );
			} else if( ( is_null( $in_array ) ) && $object->algoritmdata->default === true ){
				$object->default( ) ? true : $this->error_( $object );
			} else {
				$class = $this->main->algoritm->errorclass;
				
				$path = $this->main->algoritm->errorpath . $class . '.php';

				( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
				
				$this->algoritm_classes[ $url_base_pos ] = new $class( $main , $args );
				$this->algoritm_classes[ $url_base_pos ]->algoritm( );
				$this->algoritm_classes[ $url_base_pos ]->release( );
			}
		}
	}
	
	public function release_ ( object $object ){
		if( $object->algoritmdata == null ){
			# $object->algoritmdata = new algoritmdata_( );
		} else {
		}

		$available = $object->algoritmdata->available;
		$auth = $object->algoritmdata->auth;
		$search = $object->algoritmdata->search;
		$default = $object->algoritmdata->default;

		# print '<div>
		# 	<div>class: ' . get_class( $object ) . '</div>
		# 	<div>available: ' . implode( ' , ' , $available ) . '</div>
		# 	<div>auth: ' . (int)$auth . '</div>
		# 	<div>search: ' . (int)$search . '</div>
		# 	<div>default: ' . (int)$default . '</div>
		# </div>';
		
		if( $auth ){
			$auth = $this->auth( );
		} else $auth = true;
		
		if( $auth ){
			if( ! $search && $default ){
				$object->default( ) ? true : $this->error_( $object );
			}
			else {
				$this->include_ext( $object , $available , $search , $default );
			}
		}
	}

	public function auth ( ) {
		$main = $this->main;
		$args = [ ];

		$domain = $main->main_settings->domain_accepted( false );
		$allias = $main->main_settings->allias_accepted( );
		
		if( $main->user->is_guest( ) == false ){
			return true;
		}
		
		$class = $this->algoritm->authclass;
		$path = $this->algoritm->authpath . $class . '.php';
		$class = '\\' . $domain . '\\' . $allias . '\\' . $class;		
		
		# print 'error path: ' . $path . '<br />';
		# print 'error class: ' . $class . '<br />';

		( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
		
		$url_base_pos = 0;

		$this->algoritm_classes[ $url_base_pos ] = new $class( $main , $args );
		$this->algoritm_classes[ $url_base_pos ]->algoritm( );
		$this->algoritm_classes[ $url_base_pos ]->release( );
	}

	public function error_ ( object $object ) {
		$main = $this->main;
		$args = [ ];

		$domain = $main->main_settings->domain_accepted( false );
		$allias = $main->main_settings->allias_accepted( );

		$top = $main->alliasglobal->top;
		$left = $main->alliasglobal->left;
		$center = $main->alliasglobal->center;
		$right = $main->alliasglobal->right;
		$bottom = $main->alliasglobal->bottom;
		
		$class = $this->main->algoritm->errorclass;
		$path = $this->main->algoritm->errorpath . $class . '.php';
		$class = '\\' . $domain . '\\' . $allias . '\\' . $class;		
		# print 'error path: ' . $path . '<br />';
		# print 'error class: ' . $class . '<br />';

		( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
		
		$center->visuotag( )->clear( );

		$url_base_pos = 0;
		$this->algoritm_classes[ $url_base_pos ] = new $class( $main , $args );
		$this->algoritm_classes[ $url_base_pos ]->algoritm( );
		$this->algoritm_classes[ $url_base_pos ]->release( );
	}
}

?>
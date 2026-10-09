<?PHP

class algoritm_ {
	# --- core references ---
	public main $main;

	# public \damoony\www\algoritm|\damoony\login\algoritm|\damoony\login\algoritm $algoritm;
	public null|\algoritm_ $algoritm = null;
	public array $args = [ ];
	public null|algoritmdata_ $algoritmdata = null;
	public string $path = '';

	# --- routing paths and classes ---
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

	# --- page settings (override in child) ---
	public bool  $logged     = false;
	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = false;

	# ===============================================================
	#   CONSTRUCTOR - unified for all children
	# ===============================================================
	public function __construct ( main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritm = $main->algoritm;
		$this->algoritmdata = new algoritmdata_( );
	}

	public function args ( array $args ) {
		$this->args = $args;
	}

	public function logic ( ) {
		$main = $this->main;

		$allowed = [ ];
		$schemes = [ 'https://' , 'http://' ];

		foreach ( $main->main_settings->domains_available as $domain => $enabled ) {
			if ( ! $enabled ) continue;

			foreach ( $schemes as $scheme ) {
				$allowed[ $scheme . $domain ] = true;
			}

			foreach ( $main->main_settings->alliases_available[ $domain ] ?? [ ] as $allias => $ena ) {
				if ( ! $ena || $allias === '' ) continue;
				foreach ( $schemes as $scheme ) {
					$allowed[ $scheme . $allias . '.' . $domain ] = true;
				}
			}
		}

		$origin = $_SERVER[ 'HTTP_ORIGIN' ] ?? '';
		if ( $origin !== '' && isset( $allowed[ $origin ] ) ) {
			header( 'Access-Control-Allow-Origin: ' . $origin );
			header( 'Access-Control-Allow-Credentials: true' );
			header( 'Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Accept, X-PINGOTHER, Content-Type' );
		}
	}
	# ===============================================================
	#   RELEASE FRAMEWORK
	# ===============================================================
	public function release () {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	# ===============================================================
	#   HELPERS
	# ===============================================================
	# ===============================================================
	#   HELPERS
	# ===============================================================
	public function is_ajax ()    : bool { return (bool) $this->main->render->getdata( 'render_ajax' ); }
	public function is_guest ()   : bool { return $this->main->is_guest( ); }
	public function is_post ()    : bool { return ! empty( $_POST ); }

	public function is_default () : bool {
		$class_short = substr( strrchr( get_class( $this ) , '\\' ) , 1 );
		$pos = count( explode( '_' , $class_short ) ) - 1;
		$url_base = $this->main->library->url->static_base;
		return ( $url_base[ $pos ] ?? '' ) === '';
	}

	# ===============================================================
	#   ROUTING FRAMEWORK - default -> ajax -> role
	# ===============================================================
	public function default () {
		if ( $this->is_ajax() ) return $this->ajax();
		return $this->role();
	}
	public function ajax () {
		if ( $this->is_guest() ) return $this->ajax_guest();
		return $this->ajax_logged();
	}
	public function role () {
		if ( $this->is_guest() ) return $this->guest();
		return $this->logged();
	}

	# ===============================================================
	#   STUBS - override in children
	# ===============================================================
	public function algoritm ()    { }
	public function guest ()       { return false; }
	public function logged ()      { return false; }
	public function ajax_guest ()  { return false; }
	public function ajax_logged () { return false; }

	# ===============================================================
	#   ERROR ALIAS
	# ===============================================================
	public function error () {
		return $this->error_( $this );
	}

	# ===============================================================
	#   ROUTING HOOK - empty, override in child for debug
	# ===============================================================
	public function on_route ( string $class , string $path ) {
	}

	# ===============================================================
	#   SERVICE METHODS
	# ===============================================================
	public function clear ( ) {
		$url = $this->main->library->url( );
	}

	public function debug ( ) {
	}

	# ===============================================================
	#   ROUTING - DO NOT TOUCH
	# ===============================================================
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
		$static_allias = $main->main_settings->allias_accepted( );

		$in_array = false;
		$args = [ ];

		$class_short = substr( strrchr( get_class( $object ) , '\\' ) , 1 );
		$url_base_pos = count( explode( '_' , $class_short ) ) - 1;
		$url_base_real = $url_base;

		if ( ! isset( $url_base_real[ $url_base_pos ] ) ) $url_base_real[ $url_base_pos ] = '';

		if( $search === true ){
			if( ! empty( $available ) ){
				foreach( $available as $key=>$value ){
					$mbstr = mb_strpos( $value , '|' );
					if( $mbstr ){
						$base = explode( '|' , $value );
						mb_ereg( '^' . $base[ 0 ] . '$' , $url_base_real[ $url_base_pos ] , $matches );

						if( ! empty( $matches ) ){
							$args[ $matches[ 1 ] ?? '' ] = $matches[ 2 ] ?? '';
							$url_base_real[ $url_base_pos ] = $base[ 1 ];
							$available[ $key ] = $base[ 1 ];
							break;
						}
					} else if( $value === $url_base_real[ $url_base_pos ] ) break;
				}
			}
		}

		$in_array = in_array( $url_base_real[ $url_base_pos ] , $available ) ? $url_base_real[ $url_base_pos ] : false ;

		$this->main->algoritm->basepath .= $url_base_real[ $url_base_pos ] . DS;
		$this->main->algoritm->baseclass .= '_' . $url_base_real[ $url_base_pos ];

		$class = $this->main->algoritm->baseclass;
		$path = $this->main->algoritm->basepath . $class . '.php';
		$class = '\\' . $static_domain . '\\' . $static_allias . '\\' . $class;
		
		$this->on_route( $class , $path );

		if( $in_array && include_once $path ){
			if( class_exists( $class ) ){
				$this->algoritm_classes[ $url_base_pos ] = new $class( $main , $args );
				if( $this->algoritm_classes[ $url_base_pos ]->logged == false
					|| ( $this->algoritm_classes[ $url_base_pos ]->logged == true && $main->is_guest( ) == false )
				){
					$this->algoritm_classes[ $url_base_pos ]->algoritm( );
					$this->algoritm_classes[ $url_base_pos ]->release( );
				} else {
					$this->error_( $this->algoritm_classes[ $url_base_pos ] );
				}
			} else {
				http_response_code( 403 );
				$this->algoritm_classes[ $url_base_pos - 1 ]->release( );
			}
		} else {
			if( ( $in_array === '' || is_null( $in_array ) ) && $object->algoritmdata->default === true ){
				$object->default( ) ? true : $this->error_( $object );
			} else {
				$class = $this->main->algoritm->errorclass;

				$path = $this->main->algoritm->errorpath . $class . '.php';
				$class = '\\' . $static_domain . '\\' . $static_allias . '\\' . $class;

				( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );

				$this->algoritm_classes[ $url_base_pos ] = new $class( $main , $args );
				$this->algoritm_classes[ $url_base_pos ]->algoritm( );
				$this->algoritm_classes[ $url_base_pos ]->release( );
			}
		}
	}

	public function release_ ( object $object ){
		$available = $object->algoritmdata->available;
		$auth = $object->algoritmdata->auth;
		$search = $object->algoritmdata->search;
		$default = $object->algoritmdata->default;

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

		if( $main->is_guest( ) == false ){
			return true;
		}

		$class = $this->algoritm->authclass;
		$path = $this->algoritm->authpath . $class . '.php';
		$class = '\\' . $domain . '\\' . $allias . '\\' . $class;

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

		( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );

		$center->visuotag( )->clear( );

		$url_base_pos = 0;
		$this->algoritm_classes[ $url_base_pos ] = new $class( $main , $args );
		$this->algoritm_classes[ $url_base_pos ]->algoritm( );
		$this->algoritm_classes[ $url_base_pos ]->release( );
	}
}

?>
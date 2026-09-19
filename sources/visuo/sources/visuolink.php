<?PHP

class visuolink {
	# --- refs ---
	public null|visuometa $visuometa = null;

	# --- state ---
	public string $type = '';

	# --- sub ---
	public null|visuolink_favicon $favicon = null;
	public null|visuolink_css     $css     = null;
	public null|visuolink_js      $js      = null;

	# --- allowed types (whitelist for as_tag guard) ---
	private const TYPES = [ 'favicon' , 'css' , 'js' ];

	# --- methods ---
	public function __construct ( visuometa $visuometa ) {
		$this->visuometa = $visuometa;
	}

	public function favicon ( string $href , null|bool $rand = null ) : visuolink_favicon {
		$this->type = 'favicon';
		return $this->favicon = new visuolink_favicon( $this , $href , $rand );
	}

	public function css ( string $href , null|bool $rand = null ) : visuolink_css {
		$this->type = 'css';
		return $this->css = new visuolink_css( $this , $href , $rand );
	}

	public function js ( string $href , null|bool $rand = null ) : visuolink_js {
		$this->type = 'js';
		return $this->js = new visuolink_js( $this , $href , $rand );
	}

	public function as_tag ( ) : visuotag {
		if ( ! in_array( $this->type , self::TYPES , true ) ) return new visuotag( );
		if ( $this->{$this->type} === null ) return new visuotag( );
		return $this->{$this->type}->as_tag( );
	}

	# --- helpers ---

	# Разрешает $rand с учётом глобальной настройки VISUOLINK_RAND_DEFAULT.
	# Приоритет: явный параметр → константа → false.
	public static function resolve_rand ( null|bool $rand ) : bool {
		if ( $rand !== null ) return $rand;
		return defined( 'VISUOLINK_RAND_DEFAULT' ) && VISUOLINK_RAND_DEFAULT === true;
	}

	# Добавляет cache-buster к URL, если $rand === true.
	# Корректно работает с query-строкой и якорем.
	public static function apply_rand ( string $href , bool $rand ) : string {
		if ( ! $rand || $href === '' ) return $href;

		$token = (string) random_int( 10000 , 99999 );

		# отделяем якорь (#...) от остального URL
		$hash_pos = strpos( $href , '#' );
		$base     = $hash_pos === false ? $href : substr( $href , 0 , $hash_pos );
		$hash     = $hash_pos === false ? ''    : substr( $href , $hash_pos );

		# выбираем разделитель: ? если query ещё нет, & если есть
		$sep = str_contains( $base , '?' ) ? '&' : '?';

		return $base . $sep . $token . $hash;
	}
}

class visuolink_favicon {
	# --- refs ---
	public visuolink $visuolink;

	# --- state ---
	public string $d_rel  = 'icon';
	public string $d_href = '/favicon.ico';
	public string $d_type = 'image/x-icon';

	# --- methods ---
	public function __construct ( visuolink $visuolink , string $href , null|bool $rand = null ) {
		$this->visuolink = $visuolink;
		$this->d_href    = visuolink::apply_rand( $href , visuolink::resolve_rand( $rand ) );
	}

	public function as_tag ( ) : visuotag {
		$vt = new visuotag( );
		$vt->tag( 'link' , false )
			->attrs_add( 'rel'  , $this->d_rel  )
			->attrs_add( 'href' , $this->d_href )
			->attrs_add( 'type' , $this->d_type );
		return $vt;
	}
}

class visuolink_css {
	# --- refs ---
	public visuolink $visuolink;

	# --- state ---
	public string $href = '';

	# --- methods ---
	public function __construct ( visuolink $visuolink , string $href , null|bool $rand = null ) {
		$this->visuolink = $visuolink;
		$this->href      = visuolink::apply_rand( $href , visuolink::resolve_rand( $rand ) );
	}

	public function as_tag ( ) : visuotag {
		$vt = new visuotag( );
		$vt->tag( 'link' , false )
			->attrs_add( 'href' , $this->href )
			->attrs_add( 'type' , 'text/css' )
			->attrs_add( 'rel'  , 'stylesheet' );
		return $vt;
	}
}

class visuolink_js {
	# --- refs ---
	public visuolink $visuolink;

	# --- state ---
	public string $href = '';

	# --- methods ---
	public function __construct ( visuolink $visuolink , string $href , null|bool $rand = null ) {
		$this->visuolink = $visuolink;
		$this->href      = visuolink::apply_rand( $href , visuolink::resolve_rand( $rand ) );
	}

	public function as_tag ( ) : visuotag {
		$vt = new visuotag( );
		$vt->tag( 'script' )
			->attrs_add( 'src'  , $this->href )
			->attrs_add( 'type' , 'text/javascript' );
		return $vt;
	}
}

?>
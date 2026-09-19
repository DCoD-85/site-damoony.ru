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

	# --- methods ---
	public function __construct ( visuometa $visuometa ) {
		$this->visuometa = $visuometa;
	}

	public function favicon ( string $href ) : visuolink_favicon {
		$this->type = 'favicon';
		return $this->favicon = new visuolink_favicon( $this , $href );
	}

	public function css ( string $href ) : visuolink_css {
		$this->type = 'css';
		return $this->css = new visuolink_css( $this , $href );
	}

	public function js ( string $href ) : visuolink_js {
		$this->type = 'js';
		return $this->js = new visuolink_js( $this , $href );
	}

	public function as_tag ( ) : visuotag {
		if ( $this->type === '' ) return new visuotag( );
		if ( $this->{$this->type} === null ) return new visuotag( );
		return $this->{$this->type}->as_tag( );
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
	public function __construct ( visuolink $visuolink , string $href ) {
		$this->visuolink = $visuolink;
		$this->d_href    = $href;
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
	public function __construct ( visuolink $visuolink , string $href ) {
		$this->visuolink = $visuolink;
		$this->href      = $href;
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
	public function __construct ( visuolink $visuolink , string $href ) {
		$this->visuolink = $visuolink;
		$this->href      = $href;
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
<?PHP

class visuolink {
	# vars
	public null|visuometa $visuometa = null;
	public string $name = '';
	public string $type = '';
	public null|visuolink_favicon $favicon = null;
	public null|visuolink_css $css = null;
	public null|visuolink_js $js = null;
	
	# methods
	public function __construct( visuometa $visuometa ){
		$this->visuometa = $visuometa;
	}

	public function favicon ( string $href ) {
		$this->type = 'favicon';
		return $this->favicon = new visuolink_favicon ( $this , $href );
	}
	public function css ( string $href ) {
		$this->type = 'css';
		return $this->css = new visuolink_css ( $this , $href );
	}
	
	public function js ( string $href ) {
		$this->type = 'js';
		return $this->js = new visuolink_js ( $this , $href );
	}
	public function as_tag( ) {
		return $this->{$this->type}->as_tag( );
	}
}

class visuolink_favicon {
	# vars
	# vars [ private ]
	public visuolink $visuolink;
	# vars [ public ]
	private bool $rand = true;
	# vars [ public ] : data
	public string $d_rel = 'icon';
	public string $d_href = '/favicon.ico';
	public string $d_type = 'image/x-icon';
	# methods
	public function __construct ( visuolink $visuolink , string $href ) {
		$this->visuolink = $visuolink;
		$this->d_href = $href;
	}
	public function as_tag ( ) {
		$vt = new visuotag( );
		if( $this->rand ) $this->href .= '?' . rand( 11111 , 99999 ); 
		
		$vt->tag( 'link' , false )
			->attrs_add( 'rel' , $this->d_rel )
			->attrs_add( 'href' , $this->d_href )
			->attrs_add( 'type' , $this->d_type );
		return $vt;
	}
}

class visuolink_css {
	# vars
	# vars [ private ]
	public visuolink $visuolink;
	# vars [ public ]
	private bool $rand = true; 
	public string $href = '';
	# methods
	public function __construct ( visuolink $visuolink , string $href ) {
		$this->visuolink = $visuolink;
		$this->href = $href;
	}
	public function as_tag ( ) {
		if( $this->rand ) $this->href .= '?' . rand( 11111 , 99999 ); 
		
		$vt = new visuotag( );
		$vt->tag( 'link' , false )
			->attrs_add( 'href' , $this->href )
			->attrs_add( 'type' , 'text/css' )
			->attrs_add( 'rel' , 'stylesheet' );
		
		return $vt;
	}
}

class visuolink_js {
	# vars
	# vars [ private ]
	public visuolink $visuolink;
	# vars [ public ]
	private bool $rand = true;
	public string $href = '';
	# methods
	public function __construct ( visuolink $visuolink , string $href ) {
		$this->visuolink = $visuolink;
		$this->href = $href;
	}
	public function as_tag ( ) {
		if( $this->rand ) $this->href .= '?' . rand( 11111 , 99999 ); 
		$vt = new visuotag( );
		$vt->tag( 'script' )
			->attrs_add( 'src' , $this->href )
			->attrs_add( 'type' , 'text/javascript' );
		
		return $vt;
	}
}

?>
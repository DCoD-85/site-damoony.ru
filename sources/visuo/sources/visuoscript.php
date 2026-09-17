<?PHP

class visuoscript {
	# vars
	public null|visuometa $visuometa = null;
	public string $name = '';
	public string $type = '';	# link | content
	# methods
	public function __construct( visuometa $visuometa ){
		$this->visuometa = $visuometa;
	}
	public function link ( string $href ) {
		return $this->css = new visuoscript_link ( $this , $href );
	}
	
	public function content ( string $href ) {
		return $this->js = new visuoscript_content ( $this , $href );
	}
	public function as_tag( ) {
		return $this->{$this->type}->as_tag( );
	}
}

class cisuoscript_link {
	# vars
	# vars [ private ]
	public visuolink $visuolink;
	# vars [ public ]
	public string $href = '';
	# methods
	public function __construct ( visuolink $visuolink , string $href ) {
		$this->visuolink = $visuolink;
		$this->href = $href;
	}
	public function as_tag ( ) {
		$vt = new visuotag( );
		$vt->tag( 'script' )->attr( 'src' , $this->href )->attr( 'type' , 'text/javascript' );
		
		return $vt;
	}
}
class visuoscript_content {
	# vars
	# vars [ private ]
	public visuolink $visuolink;
	# vars [ public ]
	public string $content = '';
	# methods
	public function __construct ( visuolink $visuolink , string $content ) {
		$this->visuolink = $visuolink;
		$this->content = $content;
	}
	public function as_tag ( ) {
		$vt = new visuotag( );
		$vt->tag( 'script' )->attr( 'type' , 'text/javascript' )->content( $this->content );
		
		return $vt;
	}
}

?>
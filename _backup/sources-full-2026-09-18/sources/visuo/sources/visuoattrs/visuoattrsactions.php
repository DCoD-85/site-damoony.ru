<?PHP

class visuoattrsactions {
	# vars
	public null|visuoattrs $visuoattrs = null;
	public null|visuotag $visuotag = null;
	private null|string $onclick = null;
	private null|string $ondblclick = null;
	private null|string $onmouseout = null;
	private null|string $onmouseover = null;
	private null|string $onmousemove = null;
	private array $vars = [ 'onclick' , 'onsubmit' , 'ondblclick' , 'onmouseout' , 'onmouseover' , 'onmousemove' ];
	public array $data = [ ];
	public array $elements = [ ];
	# methods
	public function __construct ( visuoattrs $visuoattrs ){
		$this->visuoattrs = $visuoattrs;
		$this->visuotag = $visuoattrs->visuotag( );
	}
	public function add( string $action , string $value , bool $json = false , bool $new = true ){
		# if( $json ) $value = json_encode( $value );
		# print $value;
		$this->{strtolower($action)} = $value;
		return $this->visuotag;
	}
	public function remove ( string $action ) {
		unset( $this->{$action} );
		return $this->visuotag;
	}
	
	public function get( string $action ){
		return $this->data[ $action ];
	}
	public function tag ( ) {
		return $this;
	}
	
	public function onclick( string $data ) {
		$this->{__FUNCTION__} = $data;
		return $this->visuotag;
	}
	public function ondblclick( string $data ) {
		$this->{__FUNCTION__} = $data;
		return $this->visuotag;
	}
	
	public function onmouseout( string $data ) {
		$this->{__FUNCTION__} = $data;
		return $this->visuotag;
	}
	public function onmouseover( string $data ) {
		$this->{__FUNCTION__} = $data;
		return $this->visuotag;
	}
	public function onmousemove( string $data ) {
		$this->{__FUNCTION__} = $data;
		return $this->visuotag;
	}
	
	public function release( bool $full = true ){
		$html = '';
		$vars = $this->vars;
		if( ! empty( $vars ) ) foreach( $vars as $var ) if( $this->$var ) $html .= ' ' . $var . '="' . $this->$var . '"';
		
		return $html ? ' ' . trim( $html ) : '';
	}
}

?>
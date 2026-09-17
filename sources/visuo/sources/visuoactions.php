<?PHP

class visuoactions {
	private null|visuotag $visuotag = null;
	private null|string $onclick = null;
	private null|string $ondblclick = null;
	private null|string $onmouseout = null;
	private null|string $onmouseover = null;
	private null|string $onmousemove = null;
	private array $vars = [ 'onclick' , 'ondblclick' , 'onmouseout' , 'onmouseover' , 'onmousemove' ];
	public array $data = [ ];
	# functions
	public function __construct( null|visuotag $visuotag = null ){
		$this->visuotag = $visuotag;
	}
	public function add( string $attr_name , string $attr_value ){
		$this->{strtolower( $attr_name )} = $attr_value;
		return $this->visuotag;
	}
	public function get( string $attr_name ){
		return $this->data[ $attr_name ];
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
	
	public function release( ){
		$html = '';
		$vars = $this->vars;
		foreach( $vars as $var ) if( $this->$var ) $html .= ' ' . $var . '="' . $this->$var . '"';
		
		return $html;
	}
}

?>
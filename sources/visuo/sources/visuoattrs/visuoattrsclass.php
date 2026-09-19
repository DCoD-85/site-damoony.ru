<?PHP

class visuoattrsclass {
	# vars
	public null|visuoattrs $visuoattrs = null;
	public null|visuotag $visuotag = null;
	public array $elements = [ ];
	# methods
	public function __construct ( visuoattrs $visuoattrs ){
		$this->visuoattrs = $visuoattrs;
		$this->visuotag = $visuoattrs->visuotag( );
	}
	public function parent ( ) {
		return $this->visuotag;
	}
	public function add ( string $value , bool $generic = true ){
		$value_arr = explode( ' ' , $value );
		foreach( $value_arr as $v ){
			$this->add_check( $v );
		}
		
		return $this;
	}
	private function add_check ( string $value ) {
		$elements_list = $this->getlist( );
		$bok = true;
		foreach( $elements_list as $element ) if( $value == $element->value ) $bok = false;
		
		if( $bok === true ) $this->elements[ ] = new visuoattrsclass_element( $this , $value );
	}
	private function getlist ( ) {
		return $this->elements;
	}
	public function get ( string $value ) {
		# $value_arr = explode( ' ' , $value );
		foreach( $this->elements as $element ) {
			if( $element->value == $value ) return $value;
		}
		return '';
	}
	public function remove ( string $value ) {
		$value_arr = explode( ' ' , $value );
		foreach( $value_arr as $value ){
			foreach( $this->elements as $k=>$v ){
				if( $v->value  == $value ){
					unset( $this->elements[ $k ] );
					break;
				}
			}
		}
		
		return $this;
	}
	public function release ( bool $full = true ){
		$return = '';
		$elements = $this->elements;
		if( ! empty( $elements ) ) foreach( $elements as $element ) $return .= trim( $element->value ) . ' ';
		$return = trim( $return );
		
		return $return ? ( $full ? ' class="' . $return . '"' : $return ) : $return;
	}
}

class visuoattrsclass_element {
	private visuoattrsclass $parent;
	public string $name = '';
	public string $value = '';
	
	public function __construct( visuoattrsclass $parent , string $value ){
		$this->parent = $parent;
		$this->value = $value;
	}
}


?>
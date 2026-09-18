<?PHP

class visuoattrsstyle {
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
	public function add( string $expierence , string $separator = ';' , bool $generic = true ){
		$attrs = explode( $separator , $expierence );
		
		if( ! empty( $attrs ) ){
			foreach( $attrs as $attr ) {
				$attr = trim( $attr );
				if( ! empty( $attr ) ) $this->add_check( $attr );
			}
		}
		
		return $this;
	}
	private function add_check ( string $attr , string $separator = ':' ) {
		$attr = explode( $separator , $attr );
		$name = trim( $attr[ 0 ] );
		$value = trim($attr[ 1 ] );
		
		# if( ( $element = $this->get($name ) ) !== false ) $element->edit( $name , $value );
		# else $this->elements[ $name ] = new visuostyle_element( $this , $name , $value );
		$this->elements[ $name ] = new visuoattrsstyle_element( $this , $name , $value );
	}
	private function getlist ( ) {
		return $this->elements;
	}
	public function get ( string $name ) {
		if( ! empty( $this->elements ) ) {
			foreach( $this->elements as $name=>$element ) {
				if( $element->name == $name ) return $this->elements[ $name ];
			}
		}
		return false;
	}
	public function remove ( string $names ) {
		$names_arr = explode( ' ' , $names );
		foreach( $names_arr as $name ){
			foreach( $this->elements as $el_name=>$element ){
				if( $element->name  == $name ){
					unset( $element , $this->elements[ $el_name ] );
					break;
				}
			}
		}
		
		return $this;
	}
	public function release ( bool $full = true ){
		$return = '';
		$elements = $this->elements;
		if( ! empty( $elements ) ) foreach( $elements as $k=>$v ) $return .= trim( $v->name ) . ':' . trim( $v->value ) . ';';
		
		if( ! empty( $return ) ){
			if( $full === true ) return ' style="' . $return . '"';
			else return $return;
		}
		
		return '';
	}
	
	private function release_attrs ( ) {
		$return = '';
		
		return $return;
	}
}

class visuoattrsstyle_element {
	private visuoattrsstyle $parent;
	public string $name = '';
	public string $value = '';
	
	public function __construct ( visuoattrsstyle $parent , string $name , string $value ){
		$this->parent = $parent;
		$this->name = $name;
		$this->value = $value;
	}
	public function edit ( string $name , string $value ) {
		$this->name = $name;
		$this->value = $value;
	}
}


?>
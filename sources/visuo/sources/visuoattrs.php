<?PHP

class visuoattrs {
	private null|visuotag $visuotag = null;
	private visuoattrsclass $class;
	private visuoattrsstyle $style;
	private visuoattrsactions $actions;
	public array $elements = [ ];
	# functions
	public function __construct ( null|visuotag $visuotag = null ){
		$this->visuotag = $visuotag;
		$this->class = new visuoattrsclass( $this );
		$this->style = new visuoattrsstyle( $this );
		$this->actions = new visuoattrsactions( $this );
	}
	public function visuotag ( ) {
		return $this->visuotag;
	}
	public function add( string $name , string $value , bool $add_empty = false , bool $return_visuotag = false ){
		if( ! empty( $value ) || $add_empty === true ) $this->elements[ $name ] = $value;
		return $return_visuotag ? $this->visuotag : $this;
	}
	public function remove ( string $name , bool $return_visuotag = false ) {
		unset( $this->elements[ $name ] );
		return $return_visuotag ? $this->visuotag : $this;
	}
	public function get( string $name ){
		return $this->elements[ $name ] ?: null;
	}
	public function return_visuotag ( ) {
		return $this->visuotag( );
	}
	### attributes , class , class_add , class_remove
	public function attributes ( ) {
		# return $this->elements( );	
	}
	
	public function class ( ) {
		return $this->class;
	}
	public function class_add ( string $expierence , bool $return_visuotag = false ) {
		$this->class->add( $expierence );
		return $return_visuotag ? $this->visuotag : $this;
	}
	public function class_remove ( string $expierence , bool $return_visuotag = false ) {
		$this->class->remove( $expierence );
		return $return_visuotag ? $this->visuotag : $this;
	}
	### style , style_add , style_remove
	public function style ( ) {
		return $this->style;
	}
	public function style_add ( string $expierence , bool $return_visuotag = false ) {
		$this->style->add( $expierence );
		return $return_visuotag ? $this->visuotag : $this;
	}
	public function style_remove ( string $expierence , bool $return_visuotag = false ) {
		$this->style->remove( $expierence );
		return $return_visuotag ? $this->visuotag : $this;
	}
	
	public function actions ( ) {
		return $this->actions;
	}
	public function actions_add ( string $action , string $value , bool $json = false , bool $new = true , bool $return_visuotag = false ) {
		$this->actions->add( $action , $value , $json , $new );
		return $return_visuotag ? $this->visuotag : $this;
	}
	public function actions_remove ( string $action , bool $return_visuotag = false ) {
		$this->actions->remove( $action );
		return $return_visuotag ? $this->visuotag : $this;
	}
	
	public function attributes_release( ) {
		$html = '';
		$elements = $this->elements;
		if( ! empty( $elements ) ) foreach( $elements as $name=>$value ) $html .= ' ' . $name . '="' . $value . '"';
		return $html ? ' ' . trim( $html ) : '';
	}
	public function release( ){
		$html = '';
		$html_class = $this->class->release( );
		$html_style = $this->style->release( );
		$html_actions = $this->actions->release( );
		$html_elements = $this->attributes_release( );
		
		$html = ( $html_class ? $html_class : '' ) . ( $html_style ? $html_style : '' ) . ( $html_actions ? $html_actions : '' ) . ( $html_elements ? $html_elements : '' );
		return $html;
	}
}

?>
<?PHP
class visuoelement_block__data {
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_block $visuoelement_group;
	
	# functions
	public function __construct( visuoelement_block $visuoelement_group ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement__data = $visuoelement_group->visuoelement->visuoelement__data;
		$this->visuoelement_group = $visuoelement_group;
	}
}


class visuoelement_block {
	# vars
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_block__data $visuoelement_group__data;
	public null|visuoelement_block_block|visuoelement_block_text $visuoelement_group_element = null;
	public null|visuoelement_block_block $visuoelement_group_block = null;
	public null|visuoelement_block_text $visuoelement_group_text = null;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement $visuoelement , null|visuoelement_block__data $visuoelement_group__data = null ) {
		$this->visuoelement = $visuoelement;
		$this->visuoelement__data = $visuoelement->data( );
		if( $visuoelement_group__data !== null ) $this->visuoelement_group__data = $visuoelement_group__data;
		else $this->visuoelement_group__data = new visuoelement_block__data( $this );
		
		$this->visuotag_start = new visuotag( );
		$this->visuotag_end = new visuotag( );
	}

	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function get_element ( ) {
		$element = null;
		
		if( is_null( $element ) ) $element = $this->visuoelement_group_text;
		if( is_null( $element ) ) $element = $this->visuoelement_group_block;
		
		return $element;
	}
	public function text ( null|visuoelement_block_text__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_text = new visuoelement_block_text( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_text;
		
		$this->init( );
		return $this->visuoelement_group_text;
	}
	
	public function block ( null|visuoelement_block_block__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_block = new visuoelement_block_block( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_block;
		
		$this->init( );
		return $this->visuoelement_group_block;
	}

	public function get_block_text ( ) : visuoelement_block_text {
		return $this->get_element( );
	}

	public function get_block_block ( ) : visuoelement_block_block {
		return $this->get_element( );
	}
	
	
	public function init ( ) {
		$this->get_element( )->init( );

		$vt = new visuotag( );
		$vt->append( $this->visuotag_start );
		
		$this->visuoelement->visuotag_start = $vt;
		$this->visuoelement->visuotag_end = $this->visuotag_end;
	}
}
?>
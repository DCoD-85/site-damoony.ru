<?PHP
class visuoelement_user__data {
	private visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	private visuoelement_input $visuoelement_group;
	
	# functions
	public function __construct( visuoelement_form $visuoelement_group ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement__data = $visuoelement_group->visuoelement->visuoelement__data;
		$this->visuoelement_group = $visuoelement_group;
	}
}


class visuoelement_user {
	# vars
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_user__data $visuoelement_group__data;
	public null|visuoelement_user_popup $visuoelement_group_element = null;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement $visuoelement , null|visuoelement_user__data $visuoelement_group__data = null ) {
		$this->visuoelement = $visuoelement;
		if( $visuoelement_group__data !== null ) $this->visuoelement_group__data = $visuoelement_group__data;
		else $this->visuoelement_group__data = new visuoelement_user__data( $this );
		# $this->element->init( );
	}
	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function element ( ) {
		return $this->element;
	}
	
	public function box ( null|visuoelement_user_box__data $element__data = null ) {
		# if( $element__data !== null ) $element__data->visuoelement = $this->visuoelement;
		
		$this->element = new visuoelement_user_box( $this , $element__data );
		
		$this->init( );
		return $this->element;
	}
	
	public function set_hidden ( null|visuoelement_input_hidden__data $element__data = null ) {
		if( $element__data !== null ) $element__data->visuoelement = $this->visuoelement;
		
		$this->element = new visuoelement_input_hidden( $this , $element__data );
		
		$this->init( );
		return $this->element;
	}
	
	public function init ( ) {
		$this->element->init( );
		$element_data = $this->element->data( );
		$type = $element_data->get( 'type' );
		if( $type !== 'hidden' ) {
			$vt = new visuotag( null , null );
			$vt = $vt->tag( 'span' , style : 'display : block ; padding : 10px ; background : #999 ; ' );
			$vt->append( $this->visuoelement->visuotag_start );
			
			$this->visuoelement->visuotag_start = $vt->root( );
		}
		
		
	}
}

class visuoelement_input__type {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_input $visuoelement_type;
	public visuoelement_input_hidden__data|visuoelement_input_text__data|visuoelement_input_password__data $data;
	# methods
	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function type ( ) {
		return $this->visuoelement_type;
	}
	public function data ( ) {
		return $this->data;
	}
}
?>
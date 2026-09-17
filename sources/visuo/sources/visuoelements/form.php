<?PHP
class visuoelement_form__data {
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_form $visuoelement_group;
	public visuoelement_form $visuoelement_form;
	
	# functions
	public function __construct( visuoelement_form $visuoelement_group ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement__data = $visuoelement_group->visuoelement->visuoelement__data;
		$this->visuoelement_group = $visuoelement_group;
	}
}


class visuoelement_form {
	# vars
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_form__data $visuoelement_group__data;
	public null|visuoelement_form_form $visuoelement_group_element = null;
	public null|visuoelement_form_form $visuoelement_group_form = null;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement $visuoelement , null|visuoelement_form__data $visuoelement_group__data = null ) {
		$this->visuoelement = $visuoelement;
		if( $visuoelement_group__data !== null ) $this->visuoelement_group__data = $visuoelement_group__data;
		else $this->visuoelement_group__data = new visuoelement_form__data( $this );
	}
	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function element ( ) {
		return $this->visuoelement_group_element;
	}
	
	public function form ( null|visuoelement_form_form__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		
		$this->visuoelement_group_form = new visuoelement_form_form( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_form;

		return $this->visuoelement_group_element;
	}
	
	
	
	# GET
	public function get_form ( ) : visuoelement_form_form {
		return $this->visuoelement_group_element;
	}
	
	
	public function init ( ) {
		$this->visuoelement_group_element->init( );
		
		$vt = new visuotag( );
		$vt->tag( 'div' , class : 'blockform' )->append( $this->visuotag_start );
		
		$this->visuoelement->visuotag_start = $vt;
		$this->visuoelement->visuotag_end = $this->visuotag_end;
	}
}
?>
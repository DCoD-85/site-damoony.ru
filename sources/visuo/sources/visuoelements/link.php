<?PHP
class visuoelement_link__data {
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_link $visuoelement_group;
	
	# functions
	public function __construct( visuoelement_link $visuoelement_group ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement__data = $visuoelement_group->visuoelement->visuoelement__data;
		$this->visuoelement_group = $visuoelement_group;
	}
}


class visuoelement_link {
	# vars
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_link__data $visuoelement_group__data;
	public null|visuoelement_link_simple|visuoelement_link_load|visuoelement_link_popup $visuoelement_group_element = null;
	
	public null|visuoelement_link_simple $visuoelement_group_simple = null;
	public null|visuoelement_link_load $visuoelement_group_load = null;
	public null|visuoelement_link_popup $visuoelement_group_popup = null;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	
	# methods
	public function __construct ( visuoelement $visuoelement , null|visuoelement_link__data $visuoelement_group__data = null ) {
		$this->visuoelement = $visuoelement;
		$this->visuoelement__data = $visuoelement->data( );
		if( $visuoelement_group__data !== null ) $this->visuoelement_group__data = $visuoelement_group__data;
		else $this->visuoelement_group__data = new visuoelement_link__data( $this );
		
		$this->visuotag_start = new visuotag( );
		$this->visuotag_end = new visuotag( );
	}

	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function get_element ( ) {
		$element = null;
		
		if( is_null( $element ) ) $element = $this->visuoelement_group_simple;
		if( is_null( $element ) ) $element = $this->visuoelement_group_load;
		if( is_null( $element ) ) $element = $this->visuoelement_group_popup;
		
		return $element;
	}
	
	public function simple ( null|visuoelement_link_simple__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_simple = new visuoelement_link_simple( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_simple;
		
		return $this->visuoelement_group_simple;
	}
	
	public function load ( null|visuoelement_link_load__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_load = new visuoelement_link_load( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_load;
		
		return $this->visuoelement_group_load;
	}
	
	public function popup ( null|visuoelement_link_popup__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_popup = new visuoelement_link_popup( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_popup;
		
		return $this->visuoelement_group_popup;
	}
	
	# GET
	
	public function get_link_simple ( ) : visuoelement_link_simple {
		return $this->get_element( );
	}
	public function get_link_load ( ) : visuoelement_link_load {
		return $this->get_element( );
	}
	public function get_link_popup ( ) : visuoelement_link_popup {
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
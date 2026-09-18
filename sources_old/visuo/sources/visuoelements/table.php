<?PHP
class visuoelement_table__data {
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_table $visuoelement_group;
	
	# functions
	public function __construct( visuoelement_table $visuoelement_group ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement__data = $visuoelement_group->visuoelement->visuoelement__data;
		$this->visuoelement_group = $visuoelement_group;
	}
}


class visuoelement_table {
	# vars
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_table__data $visuoelement_group__data;
	public null|visuoelement_table_tablebox|visuoelement_table_table|visuoelement_table_block|visuoelement_table_blocktext $visuoelement_group_element = null;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement $visuoelement , null|visuoelement_table__data $visuoelement_group__data = null ) {
		$this->visuoelement = $visuoelement;
		if( $visuoelement_group__data !== null ) $this->visuoelement_group__data = $visuoelement_group__data;
		else $this->visuoelement_group__data = new visuoelement_table__data( $this );
	}
	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function element ( ) {
		return $this->visuoelement_group_element;
	}
	
	public function tablebox ( null|visuoelement_table_tablebox__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_tablebox = new visuoelement_table_tablebox( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_tablebox;
		
		$this->init( );
		return $this->visuoelement_group_tablebox;
	}
	
	public function table ( null|visuoelement_table_table__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_table = new visuoelement_table_table( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_table;
		
		$this->init( );
		return $this->visuoelement_group_table;
	}
	
	public function block ( null|visuoelement_table_block__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_block = new visuoelement_table_block( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_block;
		
		$this->init( );
		return $this->visuoelement_group_block;
	}

	public function blocktext ( null|visuoelement_table_blocktext__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_blocktext = new visuoelement_table_blocktext( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_blocktext;
		
		$this->init( );
		return $this->visuoelement_group_blocktext;
	}
	
	# GET
	public function get_table ( ) : visuoelement_table_table {
		return $this->visuoelement_group_element;
	}
	public function get_block ( ) : visuoelement_table_block {
		return $this->visuoelement_group_element;
	}
	
	
	public function init ( ) {
		$this->visuoelement_group_element->init( );
		
		$vt = new visuotag( );
		$vt->append( $this->visuotag_start );
		
		$this->visuoelement->visuotag_start = $vt;
		$this->visuoelement->visuotag_end = $this->visuotag_end;
	}
}
?>
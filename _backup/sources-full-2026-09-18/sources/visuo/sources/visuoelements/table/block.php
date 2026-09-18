<?PHP
class visuoelement_table_block__data {
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_table $visuoelement_group = null;
	public null|visuoelement_table_block $visuoelement_group_element = null;
	public string $content = '';
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_table_block $visuoelement_group_element = null ) {
		
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
		$this->clear( );
	}
	public function set_visuoelement ( ) {
		
	}
	public function set ( string $form_name ,
											string $form_method = 'POST' ,
											string $form_action = '' ,
											string $form_enctype = 'application/x-www-form-urlencoded' ,
											null|visuoattrs $form_attrs = null ,
											null|visuodata $form_data = null ) {
		
		###
		$form_method =  strtolower( $form_method );
		$this->form_name = $form_name;
		$this->form_method = $form_method;
		$this->form_action = $form_action;
		
		if( $form_method == 'get' ) $this->form_enctype = 'get/html';
		else if( $form_method == 'post' ) $this->form_enctype = 'application/x-www-form-urlencoded';
		else $this->form_enctype = $form_enctype;
		
		$this->form_attrs = $form_attrs !== null ? $form_attrs : new visuoattrs( );
		$this->form_data = $form_data !== null ? $form_data : new visuodata( );
		
		# return
		return $this;
	}
	public function set_var ( string $var , mixed $val ) {
		$this->{$var} = $val;
		return $this;
	}
	public function get ( string $var , bool$null = false ) {
		return $this->$var ?: ( $null ? : '' );
	}
	
	public function clear ( ) {
		$ttypes = $this->ttype;
		$_data_ = [ 'ttypedata' , 'colwidth' , 'colspan' , 'rowspan' , 'colelsinline' ];
		$ttypes_clear = [ ];
		foreach( $ttypes as $ttype ) $ttype_clear[ $ttype ] = [ ];
		foreach( $_data_ as $value ) $this->{$value} = $ttype_clear;
	}
	
	public function init ( ) {
		$this->visuoelement->visuoelement_group->init( );
	}
}


class visuoelement_table_block {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_table $visuoelement_group;
	public visuoelement_table_block__data $visuoelement_group_element__data;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement_table $visuoelement_group , null|visuoelement_table_block__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_table_block__data( $this );
		else
			$this->visuoelement_group_element__data = $visuoelement_group_element__data;
		
		# $this->init( );
	}
	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function group ( ) {
		return $this->visuoelement_group;
	}
	public function data ( ) {
		return $this->visuoelement_group_element__data;
	}
	
	public function clear ( ) {
		unset( $this->visuotag_start , $this->visuotag_end , $this->visuoelement->visuotag_start , $this->visuoelement->visuotag_end );
	}
	public function init ( ) {		
		
		$this->clear( );
		$vt_start = new visuotag( );
		$vt_end = new visuotag( );
		
		$data = $this->data( );
		$content = $data->content;
		
		$vt = new visuotag( );
		$vt->tag('div' , class : 'table_block' )->tag( 'div' , class : 'div-inside' )->content( $content );
		
		# die( $vt->releasetree( ) );
		$vt_start = $vt;
		$vt_end = $vt;
		
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
	}
}

?>
<?PHP
class visuoelement_input_hidden__data
	extends visuoelement_group_element__data
	{
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_input $visuoelement_group = null;
	public null|visuoelement_input_hidden $visuoelement_group_element = null;
	public string $el_class = 'el el-input-text';
	public string $tag_type = 'text';
	public string $tag_class = 'el-text';
	public bool $readonly = false;
	public bool $disabled = false;
	public string $text_name = 'name';
	public string $text_value = 'Default';
	public string $label = 'Label default';
	public string $label_comment = 'Label comment default.';
	public bool $label_comment_popup = true;
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_input_hidden $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
	}
	
	public function set ( string $text_name = '' ,
											string $text_value = '' ,
											string $label = '' ,
											string $label_comment = '' ,
											null|visuoattrs $text_attrs = null ,
											null|visuodata $text_data = null ) {
		$this->text_name = $text_name;
		$this->text_value = $text_value;
		$this->label = $label;
		$this->label_comment = $label_comment;
		$this->text_attrs = $text_attrs !== null ? $text_attrs : new visuoattrs( );
		$this->text_data = $text_data !== null ? $text_data : new visuodata( );
		
		# return
		return $this;
	}
}


class visuoelement_input_hidden {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_input $visuoelement_group;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	public visuoelement_input_hidden__data $visuoelement_group_element__data;
	# methods
	public function __construct ( visuoelement_input $visuoelement_group , null|visuoelement_input_hidden__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_input_hidden__data( $this );
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
		$vt_tag = new visuotag( );
		$vt_start = $vt_tag;
		$vt_end = $vt_start;
		
		
		$vt_input = new visuotag( );
		$vt_input = $vt_input->tag( 'span' , class: 'span-input' )
			->tag( 'input' , class : 'el-hidden' )->attrs_add( 'type' , 'hidden' )->data_add( 'type' , 'el-hidden' , false );
		if( $value = $this->data( )->get_var( 'name' ) ) $vt_input->attrs_add( 'name' , $value );
		if( $value = $this->data( )->get_var( 'value' ) ) $vt_input->attrs_add( 'value' , $value );
		
		# объединение
		$vt_tag->tag( 'div' , class : 'el el-input-hidden' )
			->append( $vt_input );
		
		# form : attrs
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
		
		
	}
}

?>
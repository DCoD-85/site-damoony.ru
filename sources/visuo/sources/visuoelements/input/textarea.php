<?PHP
class visuoelement_input_textarea__data
	extends visuoelement_group_element__data
	{
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_input $visuoelement_group = null;
	public null|visuoelement_input_textarea $visuoelement_group_element = null;
	public string $type = 'text';
	public string $name = 'name';
	public string $value = 'Default';
	public string $label = 'Label default';
	public string $label_comment = 'Label comment default.';
	public bool $label_comment_popup = true;
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_input_textarea $visuoelement_group_element = null ) {
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


class visuoelement_input_textarea {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_input $visuoelement_group;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	public visuoelement_input_textarea__data $visuoelement_group_element__data;
	# methods
	public function __construct ( visuoelement_input $visuoelement_group , null|visuoelement_input_textarea__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_input_textarea__data( $this );
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
		$vt_label = null;
		$vt_input = null;
		$vt_label_comment = null;
		
		$vt_tag = new visuotag( );
		$vt_start = $vt_tag;
		$vt_end = $vt_start;
		
		if( $this->data( )->label ){
			$vt_label = new visuotag( );
			$vt_label = $vt_label->tag( 'span' , class: 'span-label' )->content( $this->data( )->get_var( 'label' ) );
		}
		if( $this->data( )->label_comment ){
			$vt_label_comment = new visuotag( );
			$vt_label_comment = $vt_label_comment->tag( 'span' , class: 'span-label-comment' )->content( $this->data( )->get_var( 'label_comment' ) );
		}
		
		
		$vt_input = new visuotag( );
		$vt_input = $vt_input->tag( 'span' , class: 'span-input' )
			->tag( 'span' , class : 'border-box' )
				->tag( 'textarea' , class : 'el-textarea' )->data_add( 'type' , 'el-textarea' , false );
		if( $value = $this->data( )->get_var( 'readonly' ) ) $vt_input->attrs_add( 'readonly' , 'readonly' );
		if( $value = $this->data( )->get_var( 'name' ) ) $vt_input->attrs_add( 'name' , $value );
		if( $value = $this->data( )->get_var( 'value' ) ) $vt_input->attrs_add( 'value' , $value );
		
		# объединение
		$vt_tag = $vt_tag->tag( 'div' , class : 'el el-input-text' );
		if( $value = $this->data( )->get_var( 'readonly' ) ) $vt_tag->data_add( 'readonly' , 'readonly' , false );
		
		! $vt_label ?: $vt_tag->append( $vt_label );
		! $vt_input ?: $vt_tag->append( $vt_input );
		! $vt_label_comment ?: $vt_tag->append( $vt_label_comment );
		
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
		
		
	}
}

?>
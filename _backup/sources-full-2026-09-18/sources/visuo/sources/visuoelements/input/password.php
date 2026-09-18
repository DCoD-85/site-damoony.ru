<?PHP
class visuoelement_input_password__data
	extends visuoelement_group_element__data
	{
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_input $visuoelement_group = null;
	public null|visuoelement_input_password $visuoelement_group_element = null;
	public string $el_class = 'el el-input-password';
	public string $tag_type = 'password';
	public string $tag_class = 'el-password';
	public string $input_name = '';
	public string $input_value = '';
	public bool $input_readonly = false;
	public bool $input_disabled = false;
	public string $name = 'name';
	public string $value = '';
	public string $label = '';
	public string $label_comment = '';
	public bool $label_comment_popup = false;
	public string $label_comment_popup_position = 'top';
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_input_password $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
		
	}
	
	public function set ( string $input_name = '' ,
											string $input_value = '' ,
											string $label = '' ,
											string $label_comment = '' ,
											bool $input_readonly = false ,
											bool $input_disabled = false ,
											bool $popup = false ,
											null|visuoattrs $text_attrs = null ,
											null|visuodata $text_data = null ) {
		$this->input_name = $input_name;
		$this->input_value = $input_value;
		$this->input_readonly = $input_readonly;
		$this->input_disabled = $input_disabled;
		$this->label = $label;
		$this->label_comment = $label_comment;
		$this->text_attrs = $text_attrs !== null ? $text_attrs : new visuoattrs( );
		$this->text_data = $text_data !== null ? $text_data : new visuodata( );
		
		# return
		return $this;
	}
}


class visuoelement_input_password {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_input $visuoelement_group;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	public visuoelement_input_password__data $visuoelement_group_element__data;
	# methods
	public function __construct ( visuoelement_input $visuoelement_group , null|visuoelement_input_password__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_input_password__data( $this );
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

		$data = $this->data( );
		$vt_element = new visuotag( );
		$vt_label = null;
		$vt_input_tag = new visuotag( );
		$vt_input = new visuotag( );
		$vt_label_comment = null;
		
		$vt_start = $vt_element;
		$vt_end = $vt_start;
		
		$vt_element = $vt_element->tag( 'div' , class : 'col_block' );

		if( $label = $data->label ){
			$vt_label = new visuotag( );
			$vt_label = $vt_label->tag( 'span' , class: 'span-label' )->content( $label );
		}
		if( $label_comment = $data->label_comment ){
			$vt_label_comment = new visuotag( );
			$vt_label_comment = $vt_label_comment->tag( 'span' , class: 'span-label-comment' );
			$popup = $data->label_comment_popup;
			$popup_position = $data->label_comment_popup_position;
			
			$vt_label_comment = $vt_label_comment->content( $label_comment )->root( );
		}
		
		$vt_input_tag = $vt_input_tag->tag( 'input' , class : $this->data( )->get_var( 'tag_class' ) )
			->attrs_add( 'type' , $this->data( )->get_var( 'tag_type' ) )
			->data_add( 'type' , 'el-text' , false );
		
		if( $value = $this->data( )->get_var( 'el_type' ) ) $vt_input_tag->attrs_add( 'type' , $value );
		if( $value = $this->data( )->get_var( 'input_disabled' ) ) $vt_input_tag->attrs_add( 'disabled' , 'disabled' );
		if( $value = $this->data( )->get_var( 'input_readonly' ) ) $vt_input_tag->attrs_add( 'readonly' , 'readonly' );
		if( $value = $this->data( )->get_var( 'input_name' ) ) $vt_input_tag->attrs_add( 'name' , $value );
		if( $value = $this->data( )->get_var( 'input_value' ) ) $vt_input_tag->attrs_add( 'value' , $value );
		
		
		$vt_input = $vt_input->tag( 'span' , class: 'span-input' )
			->tag( 'span' , class : 'border-box' )
				->append( $vt_input_tag );
		
		
		$vt_element = $vt_element->tag( 'div' , class : $this->data( )->get_var( 'el_class' ) ?: '' );
		if( $value = $this->data( )->get_var( 'readonly' ) ) $vt_element->data_add( 'readonly' , 'readonly' , false );
		$vt_element = $vt_element->tag( 'div' , class : 'box' );

		! $vt_label ?: $vt_element->append( $vt_label );
		! $vt_input ?: $vt_element->append( $vt_input );
		! $vt_label_comment ?: $vt_element->append( $vt_label_comment );
		
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
	}
}

?>
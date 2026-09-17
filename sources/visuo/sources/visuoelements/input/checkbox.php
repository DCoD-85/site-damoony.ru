<?PHP
class visuoelement_input_checkbox__data
	extends visuoelement_group_element__data
	{
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_input $visuoelement_group = null;
	public null|visuoelement_input_checkbox $visuoelement_group_element = null;
	public null|bool $render_classic = null;
	# element
	public string $input_type = 'checkbox';
	public string $input_name = '';
	public string $input_value = 'true';
	public bool $input_checked = false;
	public bool $input_readonly = false;
	public bool $input_disabled = false;
	public bool $popup = false;
	public bool|string $popup_link = false;
	public bool|string $popup_layer = false;
	public array $popup_data = [ ];
	# context
	public string $label = 'Label default';
	public string $label_comment = 'Label comment default.';
	public bool $label_comment_popup = true;
	public string $text = '';

	
	# functions
	public function __construct( null|visuoelement_input_checkbox $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
		
	}
	
	public function set ( string $input_name = '' ,
											string $input_value = 'true' ,
											bool $input_checked = false ,
											bool $input_readonly = false ,
											bool $input_disabled = false ,
											string $label = '' ,
											string $label_comment = '' ,
											string $text = '' ,
											bool $popup = false ,
											bool|string $popup_link = false ,
											bool|string $popup_layer = false ,
											array $popup_data = [ ] ) {
		$this->input_name = $input_name;
		$this->input_value = $input_value;
		$this->input_checked = $input_checked;
		$this->input_readonly = $input_readonly;
		$this->input_disabled = $input_disabled;
		$this->label = $label;
		$this->label_comment = $label_comment;
		$this->text = $text;
		
		$this->popup = $popup;
		$this->popup_link = $popup_link;
		$this->popup_layer = $popup_layer;
		$this->popup_data = $popup_data;

		# return
		return $this;
	}
}


class visuoelement_input_checkbox {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_input $visuoelement_group;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	public visuoelement_input_checkbox__data $visuoelement_group_element__data;
	# methods
	public function __construct ( visuoelement_input $visuoelement_group , null|visuoelement_input_checkbox__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_input_checkbox__data( $this );
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
		$visuoelement = $this->data( )->visuoelement;
		$element__data = $this->data( );
		if( $element__data->render_classic === null ) $element__data->render_classic = $visuoelement->render_classic;
		
		if( $element__data->render_classic === true ) $this->render_classic( );
		else if( $element__data->render_classic === false ) $this->render_user( );
	}
	
	public function render_classic ( ) {
		$lib = $this->visuoelement( )->visuorelease->main->library;
		# $gen = $lib->gen( );

		$this->clear( );
		$data = $this->data( );
		$vt_element = new visuotag( );
		$vt_label = new visuotag( ); # label
		$vt_label_comment = new visuotag( ); # comment
		$vt_text = new visuotag( ); # text
		$vt_input = new visuotag( );
		$vt_input_tag = new visuotag( );
		$vt_input_text = new visuotag( );
		
		$vt_start = $vt_element;
		$vt_end = $vt_start;

		$vt_element = $vt_element
			->tag( 'div' , class : 'col_block' )
				->tag( 'div' , class : 'el el-input-' . $data->get_var( 'input_type' ) );

		if( $data->label ) $vt_label->tag( 'span' , class: 'span-label' )->content( $data->get_var( 'label' ) );
		if( $data->label_comment ) $vt_label_comment->tag( 'span' , class: 'span-label-comment' )->content( $data->get_var( 'label_comment' ) );
		if( $data->text ) $vt_text->tag( 'span' , class: 'span-text' )->content( $data->get_var( 'text' ) );
		
		
		$vt_input_tag = $vt_input_tag->tag( 'input' , class : 'el-checkbox' );
		if( $value = $data->get_var( 'input_type' ) ) $vt_input_tag->attrs_add( 'type' , $value );
		if( $value = $data->get_var( 'input_checked' ) ) $vt_input_tag->attrs_add( 'checked' , 'checked' );
		if( $value = $data->get_var( 'input_readonly' ) ) $vt_input_tag->attrs_add( 'disabled' , 'disabled' );
		if( $value = $data->get_var( 'input_disabled' ) ) $vt_input_tag->attrs_add( 'disabled' , 'disabled' );
		if( $value = $data->get_var( 'input_name' ) ) $vt_input_tag->attrs_add( 'name' , $value );
		if( $value = $data->get_var( 'input_value' ) ) $vt_input_tag->attrs_add( 'value' , $value );
		
		
		$vt_input_text->tag( 'span' , class : 'box-text' )->append( $vt_text );


		$vt_input
			->tag('span' , class: 'span-input')
				->tag( 'div' , class : 'span-input-box' )
					->tag( 'div' , class : 'box-input' )->append( $vt_input_tag )
					->parent( )
					->tag( 'div' , class : 'box-text' )->append( $vt_text );
		
		
		# объединение
		
		! $data->label ?: $vt_element->append( $vt_label );
		! $vt_input ?: $vt_element->append( $vt_input );
		! $data->label_comment ?: $vt_element->append( $vt_label_comment );
		
		/*
		# input_data
		$vt_input_element->data_add( 'type' , 'el-checkbox' , false );
		if( $this->data( )->popup ){
			if( ! $this->data( )->popup_layer ) $this->data( )->popup_layer = $gen->id( );
			$genid = $this->data( )->popup_layer;
			# print 'genid : ' . $genid . '<br />';
			$vt_input_element->data_add( 'popup' , [ 'on'=>$this->data( )->popup , 'link'=>$this->data( )->popup_link , 'tolayerid'=>$this->data( )->popup_layer , 'data'=>$this->data( )->popup_data ] , true );
			$vt_input_element->attrs_actions_add( 'onclick' , 'engine.load.a( this , \'loaduno\' );' );

			$vt_input_text->attrs_add( 'id' , $genid );
		}
		*/
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
	}
	
	public function render_user ( ) {
		$this->clear( );
		$vt_label = null;
		$vt_input = null;
		$vt_label_comment = null;
		
		$vt_tag = new visuotag( );
		$vt_start = $vt_tag;
		$vt_end = $vt_start;

		$vt_start->tag('div')->content('Render User');
		
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
	}
}

?>
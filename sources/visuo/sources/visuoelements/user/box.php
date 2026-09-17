<?PHP
class visuoelement_user_box__data {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_input $visuoelement_type;
	public visuoelement_input_text $visuoelement_element;
	public string $type = 'text';
	public string $text_name = 'name';
	public string $text_value = 'Default';
	public string $label = 'Label default';
	public string $label_comment = 'Label comment default.';
	public bool $label_comment_popup = true;
	public visuoattrs $text_attrs;
	public visuodata $text_data;
	
	# functions
	public function __construct( null|visuoelement_input_text $visuoelement_element = null ) {
		if( $visuoelement_element !== null ){
			$this->visuoelement = $visuoelement_element->visuoelement( );
			$this->visuoelement_type = $visuoelement_element->type( );
			$this->visuoelement_element = $visuoelement_element;
		}
	}
	public function set ( string $text_name ,
											string $text_value ,
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
		
		return $this;
	}
	public function get ( string $var , $null = false ) {
		return $this->$var ?: ( $null ? : '' );
	}
}


class visuoelement_user_box
	# extends visuoelement_input__type
	{
	# vars
	private visuoelement $visuoelement;
	private visuoelement_input $visuoelement_type;
	# public visuotag $visuotag_start;
	# public visuotag $visuotag_end;
	public visuoelement_input_text__data $data;
	# methods
	public function __construct ( visuoelement_input $visuoelement_type , null|visuoelement_input_text__data $data = null ) {
		$this->visuoelement = $visuoelement_type->visuoelement;
		$this->visuoelement_type = $visuoelement_type;
		
		if( $data === null ) $this->data = new visuoelement_input_text__data( $this );
		else $this->data = $data;
		
		$this->init( );
	}
	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function type ( ) {
		return $this->visuoelement_type;
	}
	public function data ( ) {
		return $this->data;
	}
	public function init ( ) {
		$fd = $this->data( ); 
		# label
		$vt_label = new visuotag( null , null );
		$vt_label = $vt_label->tag( 'span' , class: 'label' )->content( $fd->get( 'label' ) );
		# label comment
		$vt_labelcomment = new visuotag( null , null );
		$vt_labelcomment->tag( 'span' , class: 'labelcomment' )->content( $fd->get( 'label_comment' ) );
		# input
		$vt_inputtext = new visuotag( null , null );
		$vt_inputtext = $vt_inputtext->tag( 'div' , class: 'el' )->tag( 'input' );
		
		$vt_inputtext->attrs_add( 'type' , 'text' );
		if( $value = $fd->get( 'text_name' ) ) $vt_inputtext->attrs_add( 'name' , $value );
		if( $value = $fd->get( 'text_value' ) ) $vt_inputtext->attrs_add( 'value' , $value );
		$vt_final_start = new visuotag( null , null );
		$vt_final_start->tag( 'div' , class: 'div1' )
			->tag( 'div' , class: 'div2' )
				->append( $vt_label )
				->append( $vt_inputtext )
				->append( $vt_labelcomment );
		
		
		$this->visuoelement->visuotag_start = $vt_final_start;
		$this->visuoelement->visuotag_end = $vt_final_start;
	}
}

?>
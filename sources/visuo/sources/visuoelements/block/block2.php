<?PHP
class visuoelement_block_block2__data {
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_bock $visuoelement_group = null;
	public null|visuoelement_block_block $visuoelement_group_element = null;
	public string $type = 'text';
	public string $text_name = 'name';
	public string $text_value = 'Default';
	public string $label = 'Label default';
	public string $label_comment = 'Label comment default.';
	public bool $label_comment_popup = true;
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_block_block $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
		}
		
	}
	public function set_visuoelement ( ) {
		
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
		
		#
		if( $this->visuoelement !== null ){
			$this->visuoelement->visuoelement_group->init( );
		}
	}
	
	public function set_var ( string $var , mixed $val ) {
		$this->{$var} = $val;
		
		# $this->visuoelement !== null ? $this->visuoelement->visuoelement_group->init( ) : false;
		
		return $this;
	}
	public function get ( string $var , bool $null = false ) {
		return $this->$var ?: ( $null ? : '' );
	}
}


class visuoelement_block_block {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_block $visuoelement_group;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	public visuoelement_block_block__data $visuoelement_group_element__data;
	# methods
	public function __construct ( visuoelement_block $visuoelement_group , null|visuoelement_block_block__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_block_block__data( $this );
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
		
		
		$fd = $this->data( );
		
		$vt_label = new visuotag( );
		$vt_label = $vt_label->tag( 'span' , class: 'span-label' )->content( $fd->get( 'label' ) );
		# label comment
		$vt_labelcomment = new visuotag( );
		$vt_labelcomment->tag( 'span' , class: 'span-label-comment' )->content( $fd->get( 'label_comment' ) );
		# input
		$vt_block = new visuotag( );
		$vt_block = $vt_block->tag( 'span' , class: 'span-text' );
		
		$vt = new visuotag( );
		$vt_start = $vt;
		
		$vt = $vt->tag( 'div' , class : 'el el-block-block' );
		$vt->append( $vt_block );
			
		$vt_end = $vt_start;
		
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
		
		
	}
}

?>
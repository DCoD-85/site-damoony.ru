<?PHP
class visuoelement_block_block__data
	extends visuoelement_group_element__data
	{
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_block $visuoelement_group = null;
	public null|visuoelement_block_block $visuoelement_group_element = null;
	public string $block_name = '';
	public string $head = '';
	public null|visuotag $body = null;
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_block_block $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
		
	}
	
	public function set ( string $block_name ,
											string $head = '' ,
											null|visuotag $body = null ,
											null|visuoattrs $text_attrs = null ,
											null|visuodata $text_data = null ) {
		$this->block_name = $block_name;
		$this->head = $head;
		$this->body = $body;
		$this->text_attrs = $text_attrs !== null ? $text_attrs : new visuoattrs( );
		$this->text_data = $text_data !== null ? $text_data : new visuodata( );
		
		#
		if( $this->visuoelement !== null ){
			$this->visuoelement->init( );
		}
	}
}


class visuoelement_block_block {
	# vars
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_block $visuoelement_group;
	public visuoelement_block__data $visuoelement_group__data;
	public visuoelement_block_block__data $visuoelement_group_element__data;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
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
		#
		$vt_element = new visuotag( );
		#
		$vt_head = new visuotag( );
		$vt_body = new visuotag( );
		
		# 
		$vt_start = $vt_element;
		$vt_end = $vt_start;
		# $vt_element = $vt_element->tag( 'div' , class : 'block' );
		# print gettype( $this->data( )->get_var( 'body' ) ) ;
		# print gettype( $this->data( )->get_var( 'block_name' ) );
		$vt_element = $vt_element->tag( 'div' , class : 'block ' . $this->data( )->get_var( 'block_name' ) ?: '' );
		$vt_element->tag( 'div' , class : 'block_head' )->content( $this->data( )->get_var( 'head' ) );
		if( $this->data( )->get_var( 'body' ) ) $vt_element->tag( 'div' , class : 'block_body' )->append( $this->data( )->get_var( 'body' ) );
		
		
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
	}
}

?>
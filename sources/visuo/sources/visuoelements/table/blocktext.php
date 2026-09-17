<?PHP
class visuoelement_table_blocktext__data
	extends visuoelement_group_element__data
	{
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_table $visuoelement_group = null;
	public null|visuoelement_table_blocktext $visuoelement_group_element = null;
	public string $el_class = 'el block-text';
	public string $tag_type = 'text';
	public string $tag_class = 'el-text';
	public bool $readonly = false;
	public bool $disabled = false;
	public string $text = 'Test string text';
	public string $label = '';
	public string $label_comment = '';
	public bool $label_comment_popup = false;								# false | true
	public string $label_comment_popup_position = 'top';			# top | left | right | bottom
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_table_blocktext $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
	}
	
	public function set ( string $text = '' ,
											string $label = '' ,
											string $label_comment = '' ,
											null|visuoattrs $text_attrs = null ,
											null|visuodata $text_data = null ) {
		$this->text = $text;
		$this->label = $label;
		$this->label_comment = $label_comment;
		$this->text_attrs = $text_attrs !== null ? $text_attrs : new visuoattrs( );
		$this->text_data = $text_data !== null ? $text_data : new visuodata( );
		
		# return
		return $this;
	}
}


class visuoelement_table_blocktext {
	# vars
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_table__data $visuoelement_group__data;
	public visuoelement_table_blocktext__data $visuoelement_group_element__data;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement_table $visuoelement_group , null|visuoelement_table_blocktext__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_table_blocktext__data( $this );
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

		$vt_element = new visuotag( );
		$vt_label = null;
		$vt_text = new visuotag( );
		$vt_input = new visuotag( );
		$vt_label_comment = null;
		
		$vt_start = $vt_element;
		$vt_end = $vt_start;
		
		if( $label = $this->data( )->label ){
			$vt_label = new visuotag( );
			$vt_label = $vt_label->tag( 'span' , class: 'span-label' )->content( $label );
		}
		if( $label_comment = $this->data( )->label_comment ){
			$vt_label_comment = new visuotag( );
			$vt_label_comment = $vt_label_comment->tag( 'span' , class: 'span-label-comment' );
			$popup = $this->data( )->label_comment_popup;
			$popup_position = $this->data( )->label_comment_popup_position;
			# if( $popup ) $vt_label_comment = $vt_label_comment->tag( 'span' , class : 'span-label-comment-popup' . ' ' . $popup_position );
			# if( $popup ) $vt_label_comment->attrs_class_add( 'span-label-comment-popup' . ' ' . $popup_position );
			
			$vt_label_comment = $vt_label_comment->content( $label_comment )->root( );
		}
		
				
		$vt_input = $vt_input
			->tag( 'span' , class : 'span-content' )
			->content( $this->data( )->get_var( 'text' ) );
		
		
		$vt_element = $vt_element->tag( 'div' , class : 'el-table-blocktext' );
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
<?PHP
class visuoelement_link_simple__data
	extends visuoelement_group_element__data
	{
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_link $visuoelement_group = null;
	public null|visuoelement_link_simple $visuoelement_group_element = null;
	public null|bool $render_classic = null;
	public string $text = '';
	public string $href = '';
	public bool $popup = false;
	public string $popup_layer = '';
	public array $elements = [ ];

	
	# functions
	public function __construct( null|visuoelement_link_simple $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
		
	}
	
	public function set ( string $text ,
											string $href = '' ,
											bool $popup = false ,
											string $popup_layer = '' ) {
		#
		$this->text = $text;
		$this->href = $href;
		$this->popup = $popup;
		$this->popup_layer = $popup_layer;

		#
		return $this;
	}
}


class visuoelement_link_simple {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_link $visuoelement_group;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	public visuoelement_link_simple__data $visuoelement_group_element__data;
	# methods
	public function __construct ( visuoelement_link $visuoelement_group , null|visuoelement_link_simple__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_link_simple__data( $this );
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
		$data = $this->data( );
		
		# 
		$this->clear( );
		$vt_start = new visuotag( );
		$vt_end = new visuotag( );
		
		# vt
		$vt_element = new visuotag ( );
		$vt_link = new visuotag( );

		# vt :: vt_link
		$vt_link = $vt_link->tag( 'a' );
		if( $value = $data->get_var( 'href' ) ) $vt_link->attrs_add( 'href' , $value );
		$vt_link->content( $data->get_var( 'text' ) );

		
		if( $value = $data->get_var( 'popup' ) ) $vt_link->data_add( 'popup' , $value );		
		if( $value = $data->get_var( 'popup_layer' ) ) $vt_link->data_add( 'popup-layer' , $value );
		$vt_link->data_add( 'popup-this' , 'true' );

		$vt_element->append( $vt_link );
		
		$vt_start = $vt_element->root( );
		$vt_end = $vt_element;
		
		# visuotag: start, end
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
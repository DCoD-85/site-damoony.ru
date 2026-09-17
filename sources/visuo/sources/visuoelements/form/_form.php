<?PHP

class visuoelement_form_form__data
	extends visuoelement_group_element__data
	{
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_form $visuoelement_group = null;
	public null|visuoelement_form_form $visuoelement_group_element = null;
	public string $title_text = '';
	public string $form_class = '';
	public string $form_name = 'form';
	public string $form_method = 'post';
	public string $form_action = '';	# url
	public string $form_enctype = '';	# default: 'application/x-www-form-urlencoded' | files: 'multipart/form-data' | text/plain
	public bool $form_jquery = true;
	public string $form_autocomplete = 'off';	# off | on
	public string $form_target = '_self';	# default: 'application/x-www-form-urlencoded' | files: 'multipart/form-data' | text/plain
	public array $form_data_jquery = [ 'on'=>true , 'layerto'=>'load_result' ];
	public array $elements = [ ];
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_form_form $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_group_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
	}
	public function set_visuoelement ( ) {
		
	}
	public function set ( string $form_name ,
											string $form_method = 'POST' ,
											string $form_action = '' ,
											string $form_enctype = 'application/x-www-form-urlencoded' ,
											bool $form_jquery = false ,
											string $title_text = '' ,
											string $form_class = '' ,
											null|visuoattrs $form_attrs = null ,
											null|visuodata $form_data = null ) {
		
		###
		$form_method =  strtolower( $form_method );
		$this->form_name = $form_name;
		$this->form_method = $form_method;
		$this->form_action = $form_action;
		
		if( $form_method == 'get' ) $this->form_enctype = 'get/html';
		else if( $form_method == 'post' ) $this->form_enctype = 'application/x-www-form-urlencoded';
		else if( $form_method == 'file' ){
			$this->form_method = 'POST';
			$this->form_enctype = 'multipart/form-data';
		}
		else $this->form_enctype = $form_enctype;
		
		$this->form_jquery = $form_jquery;
		
		$this->title_text = $title_text;
		$this->form_class = $form_class;

		$this->form_attrs = $form_attrs !== null ? $form_attrs : new visuoattrs( );
		$this->form_data = $form_data !== null ? $form_data : new visuodata( );
		
	}
	
	public function add ( mixed $element ) {
		$this->elements[ ] = $element;
	}
	
	public function init ( ) {
		$this->visuoelement->visuoelement_group->init( );
	}
}

class visuoelement_form_form {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_form $visuoelement_group;
	public visuoelement_form_form__data $visuoelement_group_element__data;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement_form $visuoelement_group , null|visuoelement_form_form__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		$visuoelement_group_element__data === null
			? $this->visuoelement_group_element__data = new visuoelement_form_form__data( $this )
			: $this->visuoelement_group_element__data = $visuoelement_group_element__data;
		
	}
	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function group ( ) {
		return $this->visuoelement_group;
	}
	public function element ( ) {
		return $this;
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
		
		# vt
		$vt_element = new visuotag ( );
		$vt_form = new visuotag( );
		$vt_elements = new visuotag( );

		# vt :: vt_form
		$vt_form = $vt_form->tag( 'form' );
		if( $value = $this->data( )->get_var( 'form_action' ) ) $vt_form->attrs_add( 'action' , $value );
		if( $value = $this->data( )->get_var( 'form_name' ) ) $vt_form->attrs_add( 'name' , $value );
		if( $value = $this->data( )->get_var( 'form_method' ) ) $vt_form->attrs_add( 'method' , $value );
		if( $value = $this->data( )->get_var( 'form_enctype' ) ) $vt_form->attrs_add( 'enctype' , $value );
		if( $value = $this->data( )->get_var( 'form_jquery' ) ) $vt_form->data_add( 'form-jquery' , $value );
		if( $value = $this->data( )->get_var( 'form_data_jquery' ) ) $vt_form->data_add( 'load' , $value );
		
		if( $value = $this->data( )->get_var( 'title_text' ) )
			$vt_form = $vt_form->tag( 'div' , class : 'form_title' )->content( $value )->parent( );
		if( $value = $this->data( )->get_var( 'form_class' ) )
			$vt_form->attrs_class_add( ' ' . $value );
	
		$vt_form = $vt_form->tag( 'div' , class : 'form_body' );
		
		

		$elements = $this->data( )->elements;

		if( ! empty( $elements ) ){
			$count = 0;
			foreach( $elements as $element ){
				if( is_object( $element ) ){
					
					$el = $element;
					
					if( get_class( $el ) == 'visuoelement' ){
						$vt_form->element( 'inside' , $el );
					} else if( get_class( $el ) == 'visuotag' ){
						$vt_form->append( $el->root( ) );
					} else {
					}
				} else {
					if( ! is_object( $element ) && ! is_null( $element ) ){
						$ve_block = new visuoelement( $this->visuoelement->visuorelease );
						$ve_block->block( )->text( );
						$ve_block__data = $ve_block->get_block_text__data( );
						$ve_block__data->set( $element );

						$vt_form->element( 'block' , $ve_block );
					} else {
						$vt_form->content( 'NULL' );
					}
				}
			}
		}

		$vt_element->append( $vt_form );
		
		$vt_start = $vt_element->root( );
		$vt_end = $vt_element;
		
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
	}
}

?>
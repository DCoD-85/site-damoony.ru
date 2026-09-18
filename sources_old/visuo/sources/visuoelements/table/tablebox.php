<?PHP
class visuoelement_table_tablebox__data {
	# vars
	public null|visuoelement $visuoelement = null;
	public null|visuoelement_table $visuoelement_group = null;
	public null|visuoelement_table_tablebox $visuoelement_group_element = null;
	public string $form_name = 'form';
	public string $form_method = 'post';
	public string $form_action = '';	# url
	public string $form_enctype = '';	# default: 'application/x-www-form-urlencoded' | files: 'multipart/form-data' | text/plain
	public string $form_autocomplete = 'off';	# off | on
	public string $form_target = '_self';	# default: 'application/x-www-form-urlencoded' | files: 'multipart/form-data' | text/plain
	public string $prefix = '';
	public array $head = [ ];
	public array $body = [ ];
	public null|visuoattrs $form_attrs = null;
	public null|visuodata $form_data = null;
	
	# functions
	public function __construct( null|visuoelement_table_tablebox $visuoelement_group_element = null ) {
		if( $visuoelement_group_element !== null ){
			$this->visuoelement = $visuoelement_group_element->visuoelement;
			$this->visuoelement_group = $visuoelement_group_element->visuoelement_group;
			$this->visuoelement_element = $visuoelement_group_element;
			
			$this->visuoelement->type = __CLASS__;
		}
		$this->clear( );
	}
	public function set_visuoelement ( ) {
		
	}
	public function set ( string $form_name ,
											string $form_method = 'POST' ,
											string $form_action = '' ,
											string $form_enctype = 'application/x-www-form-urlencoded' ,
											null|visuoattrs $form_attrs = null ,
											null|visuodata $form_data = null ) {
		
		###
		$form_method =  strtolower( $form_method );
		$this->form_name = $form_name;
		$this->form_method = $form_method;
		$this->form_action = $form_action;
		
		if( $form_method == 'get' ) $this->form_enctype = 'get/html';
		else if( $form_method == 'post' ) $this->form_enctype = 'application/x-www-form-urlencoded';
		else $this->form_enctype = $form_enctype;
		
		$this->form_attrs = $form_attrs !== null ? $form_attrs : new visuoattrs( );
		$this->form_data = $form_data !== null ? $form_data : new visuodata( );
		
		#
		if( $this->visuoelement !== null ){
			$this->visuoelement->init( );
		}
	}
	public function get ( string $var , $null = false ) {
		return $this->$var ?: ( $null ? : '' );
	}
	
	public function clear ( ) {
		$this->prefix = rand( 10 , 99 );
		$_data_ = [ 'head' , 'body' ];
		foreach( $_data_ as $value ) $this->{$value} = [ ];
	}
	
	public function add_box ( string $head , mixed $data , int $row = -1 ) {
		if( $row < 0 ) $row = count( $this->head );
		
		$this->head[ $row ] = $head;
		$this->body[ $row ] = $data;
	}
	
	public function init ( ) {
		$this->visuoelement->visuoelement_group->init( );
	}
}


class visuoelement_table_tablebox {
	# vars
	public visuoelement $visuoelement;
	public visuoelement_table $visuoelement_group;
	public visuoelement_table_tablebox__data $visuoelement_group_element__data;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	# methods
	public function __construct ( visuoelement_table $visuoelement_group , null|visuoelement_table_tablebox__data $visuoelement_group_element__data = null ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement_group = $visuoelement_group;
		
		if( $visuoelement_group_element__data === null )
			$this->visuoelement_group_element__data = new visuoelement_table_tablebox__data( $this );
		else
			$this->visuoelement_group_element__data = $visuoelement_group_element__data;
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
		
		$type_div = false;
		$type_table = $type_div ? 'div' : 'table';
		$type_thead = $type_div ? 'div' : 'thead';
		$type_tbody = $type_div ? 'div' : 'tbody';
		$type_tfoot = $type_div ? 'div' : 'tfoot';
		$type_tr = $type_div ? 'div' : 'tr';
		$type_td = $type_div ? 'div' : 'td';
		
		$_data = $this->data( );
		$_head = $_data->head;
		$_body = $_data->body;
		
		
		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : 'visuotablebox' );
		$vt_head = new visuotag( );
		$vt_head = $vt_head->tag( 'div' , class : 'hhead' );
		$vt_body = new visuotag( );
		$vt_body = $vt_body->tag( 'div' , class : 'bbody' );
		
		$id_head = [ ];
		$id_body = [ ];
		foreach( $_head as $head_count=>$head ){
			$id_head[ $head_count ] = $this->data( )->prefix . '_tableboxlink_id_' . $head_count;
			$id_body[ $head_count ] = $this->data( )->prefix . '_tableboxbody_id_' . $head_count;
			$vt_head->tag( 'span' , class : 'span-link' . ( ( $head_count <= 0 ) ? ' selected' : '' ) , id : $id_head[ $head_count ] )->attrs_actions_add( 'onclick' , 'visuotablebox.show( this , \'' . $id_head[ $head_count ] . '\' , \'' . $id_body[ $head_count ] . '\' ); return false;')->content( '' . $head. '' );
		}

		foreach( $_body as $body_count2=>$body2 ){
			# print '$body_count2: ' . $body_count2;
			$vt_element = new visuotag( );
			$vt_element = $vt_element->tag( 'div' , class : 'bbodybox' .( ( $body_count2 <= 0 ) ? ' selected' : '' ) , id : $id_body[ $body_count2 ] );
			
			if( ! is_array( $body2 ) ) $body2 = [ $body2 ];

			if( ! empty( $body2 ) ){
				foreach( $body2 as $body_count=>$body ){
					if( is_object( $body ) ){
						$body->init( );
						$vt_element->append( $body->visuotag_start );
					} else {
						# $vt_body->content( $body );
					}
				}
			}
			$vt_body->append( $vt_element->root( ) );
		}
		
		$vt->append( $vt_head );
		$vt->append( $vt_body );
		
		$vt_start = $vt;
		$vt_end = $vt;
		
		# form : attrs
		# $this->visuotag_start = $vt_start;
		# $this->visuotag_end = $vt_end;
		$this->visuoelement_group->visuotag_start = $vt_start;
		$this->visuoelement_group->visuotag_end = $vt_end;
	}
}

?>
<?PHP
class visuoelement_input__data {
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_input $visuoelement_group;
	
	# functions
	public function __construct( visuoelement_input $visuoelement_group ) {
		$this->visuoelement = $visuoelement_group->visuoelement;
		$this->visuoelement__data = $visuoelement_group->visuoelement->visuoelement__data;
		$this->visuoelement_group = $visuoelement_group;
	}
}


class visuoelement_input {
	# vars
	public visuoelement $visuoelement;
	public visuoelement__data $visuoelement__data;
	public visuoelement_input__data $visuoelement_group__data;
	public null|visuoelement_input_form|visuoelement_input_hidden|visuoelement_input_text|visuoelement_input_password|visuoelement_input_button|visuoelement_input_submit|visuoelement_input_reset|visuoelement_input_radio|visuoelement_input_checkbox|visuoelement_input_file|visuoelement_input_number|visuoelement_input_date|visuoelement_input_range|visuoelement_input_color|visuoelement_input_email|visuoelement_input_textarea|visuoelement_input_select $visuoelement_group_element = null;
	
	public null|visuoelement_input_form $visuoelement_group_form = null;
	public null|visuoelement_input_hidden $visuoelement_group_hidden = null;
	public null|visuoelement_input_text $visuoelement_group_text = null;
	public null|visuoelement_input_password $visuoelement_group_password = null;
	public null|visuoelement_input_file $visuoelement_group_file = null;
	public null|visuoelement_input_button $visuoelement_group_button = null;
	public null|visuoelement_input_submit $visuoelement_group_submit = null;
	public null|visuoelement_input_reset $visuoelement_group_reset = null;
	public null|visuoelement_input_checkbox $visuoelement_group_checkbox = null;
	public null|visuoelement_input_radio $visuoelement_group_radio = null;
	
	public null|visuoelement_input_number $visuoelement_group_number = null;
	public null|visuoelement_input_date $visuoelement_group_date = null;
	public null|visuoelement_input_range $visuoelement_group_range = null;
	public null|visuoelement_input_color $visuoelement_group_color = null;
	public null|visuoelement_input_email $visuoelement_group_email = null;
	
	public null|visuoelement_input_select $visuoelement_group_select = null;
	public null|visuoelement_input_textarea $visuoelement_group_textarea = null;
	public visuotag $visuotag_start;
	public visuotag $visuotag_end;
	
	# methods
	public function __construct ( visuoelement $visuoelement , null|visuoelement_input__data $visuoelement_group__data = null ) {
		$this->visuoelement = $visuoelement;
		$this->visuoelement__data = $visuoelement->data( );
		if( $visuoelement_group__data !== null ) $this->visuoelement_group__data = $visuoelement_group__data;
		else $this->visuoelement_group__data = new visuoelement_input__data( $this );
		
		$this->visuotag_start = new visuotag( );
		$this->visuotag_end = new visuotag( );
	}

	public function visuoelement ( ) {
		return $this->visuoelement;
	}
	public function get_element ( ) {
		$element = null;
		
		if( is_null( $element ) ) $element = $this->visuoelement_group_form;
		if( is_null( $element ) ) $element = $this->visuoelement_group_hidden;
		if( is_null( $element ) ) $element = $this->visuoelement_group_text;
		if( is_null( $element ) ) $element = $this->visuoelement_group_password;
		if( is_null( $element ) ) $element = $this->visuoelement_group_file;
		if( is_null( $element ) ) $element = $this->visuoelement_group_button;
		if( is_null( $element ) ) $element = $this->visuoelement_group_submit;
		if( is_null( $element ) ) $element = $this->visuoelement_group_reset;
		if( is_null( $element ) ) $element = $this->visuoelement_group_checkbox;
		if( is_null( $element ) ) $element = $this->visuoelement_group_radio;
		if( is_null( $element ) ) $element = $this->visuoelement_group_select;
		if( is_null( $element ) ) $element = $this->visuoelement_group_textarea;
		
		return $element;
	}
	
	public function form ( null|visuoelement_input_form__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_form = new visuoelement_input_form( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_form;
		
		# $this->init( );
		return $this->visuoelement_group_form;
	}

	public function hidden ( null|visuoelement_input_hidden__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_hidden = new visuoelement_input_hidden( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_hidden;
		
		# $this->init( );
		return $this->visuoelement_group_hidden;
	}
	
	public function text ( null|visuoelement_input_text__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_text = new visuoelement_input_text( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_text;
		
		# $this->init( );
		return $this->visuoelement_group_text;
	}
	
	public function password ( null|visuoelement_input_password__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_password = new visuoelement_input_password( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_password;
		
		# $this->init( );
		return $this->visuoelement_group_password;
	}
	
	public function file ( null|visuoelement_input_file__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_file = new visuoelement_input_file( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_file;
		
		# $this->init( );
		return $this->visuoelement_group_file;
	}
	
	public function button ( null|visuoelement_input_button__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_button = new visuoelement_input_button( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_button;
		
		# $this->init( );
		return $this->visuoelement_group_button;
	}
	public function submit ( null|visuoelement_input_submit__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_submit = new visuoelement_input_submit( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_submit;
		
		# $this->init( );
		return $this->visuoelement_group_submit;
	}
	
	public function reset ( null|visuoelement_input_reset__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_reset = new visuoelement_input_reset( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_reset;
		
		# $this->init( );
		return $this->visuoelement_group_reset;
	}
	
	public function checkbox ( null|visuoelement_input_checkbox__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_checkbox = new visuoelement_input_checkbox( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_checkbox;
		
		$this->init( );
		return $this->visuoelement_group_checkbox;
	}
	
	public function radio ( null|visuoelement_input_radio__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_radio = new visuoelement_input_radio( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_radio;
		
		$this->init( );
		return $this->visuoelement_group_radio;
	}
	
	public function number ( null|visuoelement_input_number__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_number = new visuoelement_input_number( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_number;
		
		$this->init( );
		return $this->visuoelement_group_number;
	}
	
	public function date ( null|visuoelement_input_date__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_date = new visuoelement_input_date( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_date;
		
		$this->init( );
		return $this->visuoelement_group_date;
	}
	
	public function range ( null|visuoelement_input_range__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_range = new visuoelement_input_range( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_range;
		
		$this->init( );
		return $this->visuoelement_group_range;
	}
	
	public function color ( null|visuoelement_input_color__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_color = new visuoelement_input_color( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_color;
		
		$this->init( );
		return $this->visuoelement_group_color;
	}
	
	public function select ( null|visuoelement_input_select__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_select = new visuoelement_input_select( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_select;
		
		$this->init( );
		return $this->visuoelement_group_select;
	}
	
	public function textarea ( null|visuoelement_input_select__data $visuoelement_group_element__data = null ) {
		if( $visuoelement_group_element__data !== null ){
			$visuoelement_group_element__data->visuoelement = $this->visuoelement;
			$visuoelement_group_element__data->visuoelement_group = $this->visuoelement->visuoelement_group;
		}
		$this->visuoelement_group_textarea = new visuoelement_input_textarea( $this , $visuoelement_group_element__data );
		$this->visuoelement_group_element = $this->visuoelement_group_textarea;
		
		$this->init( );
		return $this->visuoelement_group_textarea;
	}
	
	# GET
	
	public function get_input_form ( ) : visuoelement_input_form {
		return $this->get_element( );
	}
	public function get_input_button ( ) : visuoelement_input_button {
		return $this->get_element( );
	}
	public function get_input_submit ( ) : visuoelement_input_submit {
		return $this->get_element( );
	}
	public function get_input_reset ( ) : visuoelement_input_reset {
		return $this->get_element( );
	}
	public function get_input_hidden ( ) : visuoelement_input_hidden {
		return $this->get_element( );
	}
	public function get_input_text ( ) : visuoelement_input_text {
		return $this->get_element( );
	}
	public function get_input_password ( ) : visuoelement_input_password {
		return $this->get_element( );
	}
	public function get_input_radio ( ) : visuoelement_input_radio {
		return $this->get_element( );
	}
	public function get_input_checkbox ( ) : visuoelement_input_checkbox {
		return $this->get_element( );
	}
	public function get_input_select ( ) : visuoelement_input_select {
		return $this->get_element( );
	}
	
	
	public function init ( ) {
		$this->get_element( )->init( );
		
		$vt = new visuotag( );
		$vt//->tag( 'div' , class : 'col_block' )
				->append( $this->visuotag_start );
			
		$this->visuoelement->visuotag_start = $vt;
		$this->visuoelement->visuotag_end = $this->visuotag_end;
	}
}
?>
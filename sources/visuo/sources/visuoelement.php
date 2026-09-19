<?PHP
class visuoelement {
	public bool $init = false;
	public null|visuorelease $visuorelease = null;
	public null|visuoelement__data $visuoelement__data = null;
	public null|visuoelement_form|visuoelement_input|visuoelement_table|visuoelement_block|visuoelement_link|visuoelement_user $visuoelement_group = null;
	public null|visuoelement_form $visuoelement_form = null;
	public null|visuoelement_input $visuoelement_input = null;
	public null|visuoelement_table $visuoelement_table = null;
	public null|visuoelement_block $visuoelement_block = null;
	public null|visuoelement_link $visuoelement_link = null;
	public null|visuoelement_user $visuoelement_user = null;
	public null|visuoelement_form__data|visuoelement_input__data|visuoelement_table__data|visuoelement_block__data|visuoelement_link__data|visuoelement_user__data $visuoelement_group__data = null;
	public null|visuoelement_form__data $visuoelement_form__data = null;
	public null|visuoelement_input__data $visuoelement_input__data = null;
	public null|visuoelement_table__data $visuoelement_table__data = null;
	public null|visuoelement_block__data $visuoelement_block__data = null;
	public null|visuoelement_link__data $visuoelement_link__data = null;
	public null|visuoelement_user__data $visuoelement_user__data = null;
	public null|visuotag $visuotag_start = null;
	public null|visuotag $visuotag_end = null;
	public string $type = '';
	public bool $render_classic = true;
	public string $name = '';
	public array $elements = [ ];
	public function __construct ( null|visuorelease $visuorelease = null , null|visuoelement__data $visuoelement__data = null , null|visuotag $visuotag = null ){
		$this->visuorelease = $visuorelease;
		$this->visuoelement__data = ! is_null( $visuoelement__data ) ? $visuoelement__data->visuoelement( $this ) : new visuoelement__data( $this );
		$this->visuotag_start = $visuotag;
		$this->visuotag_end = $visuotag;
		$this->render_classic = is_null( $this->visuorelease ) ? true : $this->visuorelease->main->main_settings->visuoelement_render_classic;
	}
	
	public function init ( ) {
		$this->init = true;
		is_null( $this->visuoelement_group ) ?: $this->visuoelement_group->init( );
	}
	public function data ( ) {
		return $this->visuoelement__data;
	}
	
	public function typeinfo ( ) {
		return is_object( $this->visuoelement_group ) ? get_class( $this->visuoelement_group ) : gettype( $this->visuoelement_group );
	}
	
	
	# GET
	public function visuoelement ( ) {
		return $this;
	}
	public function group ( ) {
		return $this->visuoelement_group;
	}
	public function group__data ( ) {
		return $this->visuoelement_group__data;
	}
	# element
	public function element ( ) {
		return $this->visuoelement_group->visuoelement_group_element;
	}
	# element
	public function element__data ( ) {
		return $this->visuoelement_group->visuoelement_group_element__data;
	}
	
	public function form ( null|visuoelement_form__data $__data = null ) {
		return $this->visuoelement_form ?: $this->visuoelement_form = $this->visuoelement_group = new visuoelement_form( $this , $__data );
	}
	public function input ( null|visuoelement_input__data $__data = null ) {
		return $this->visuoelement_input ?: $this->visuoelement_input = $this->visuoelement_group = new visuoelement_input( $this , $__data );
	}
	public function table ( null|visuoelement_table__data $__data = null ) {
		return $this->visuoelement_table ?: $this->visuoelement_table = $this->visuoelement_group = new visuoelement_table( $this , $__data );
	}
	public function block ( null|visuoelement_block__data $__data = null ) {
		return $this->visuoelement_block ?: $this->visuoelement_block = $this->visuoelement_group = new visuoelement_block( $this , $__data );
	}
	public function link ( null|visuoelement_link__data $__data = null ) {
		return $this->visuoelement_link ?: $this->visuoelement_link = $this->visuoelement_group = new visuoelement_link( $this , $__data );
	}
	public function user ( null|visuoelement_user__data $__data = null ) {
		return $this->visuoelement_user ?: $this->visuoelement_user = $this->visuoelement_group = new visuoelement_user( $this , $__data );
	}
	
	# # # FORM
	public function get_form_form__data ( ) : visuoelement_form_form__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	
	# # # INPUT
	public function get_input_hidden__data ( ) : visuoelement_input_hidden__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_text__data ( ) : visuoelement_input_text__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_password__data ( ) : visuoelement_input_password__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_file__data ( ) : visuoelement_input_file__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_button__data ( ) : visuoelement_input_button__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_submit__data ( ) : visuoelement_input_submit__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_reset__data ( ) : visuoelement_input_reset__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_checkbox__data ( ) : visuoelement_input_checkbox__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_radio__data ( ) : visuoelement_input_radio__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_select__data ( ) : visuoelement_input_select__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_input_textarea__data ( ) : visuoelement_input_textarea__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	# TABLE
	public function get_table_tablebox__data ( ) : visuoelement_table_tablebox__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_table_table__data ( ) : visuoelement_table_table__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_table_block__data ( ) : visuoelement_table_block__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_table_blocktext__data ( ) : visuoelement_table_blocktext__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	
	# LINK
	public function get_link_simple__data ( ) : visuoelement_link_simple__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_link_popup__data ( ) : visuoelement_link_popup__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_link_load__data ( ) : visuoelement_link_load__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}

	# USER
	
	# BLOCK
	public function get_block_block__data ( ) : visuoelement_block_block__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	public function get_block_text__data ( ) : visuoelement_block_text__data {
		return $this->visuoelement_group->visuoelement_group_element->visuoelement_group_element__data;
	}
	
	
	# RELEASE
	public function release ( visuotag $vt ) {
		$vt_final = new visuotag( );
		$vt_final = $vt_final
			->content( '<!-- start : ' . get_class( $this->element( ) ) . ' -->' )
			->append( $this->visuotag_start )
			->content( '<!-- end : ' . get_class( $this->element( ) ) . ' -->' );
		$this->visuotag_start = $vt_final;
		
		$vt->insert( $this->visuotag_start , $this->visuotag_end );
	}
	
	public function show( ) {
		return $this->visuotag_start->release( );
	}
}

class visuoelement__data {
	# vars
	public visuoelement $visuoelement;
	# methods
	public function __construct ( visuoelement $visuoelement ){
		$this->visuoelement = $visuoelement;
	}
	public function visuoelement ( visuoelement $visuoelement ) {
		$this->visuoelement = $visuoelement;
		return $this;
	}
}

class visuoelement_group_element__data {
	public null|visuoelement $visuoelement = null;
	public bool $readonly = false;
	public bool $disabled = false;
	public function __construct ( ) {
	}
	public function init( ) {
		$this->visuoelement->init( );
	}
	# получение переменной
	public function get_var ( string $var , bool $null = false ) {
		return $this->$var ?: ( $null ? : '' );
	}
	# Установка переменной
	public function set_var ( string $var , mixed $val ) {
		$this->{$var} = $val;
		return $this;
	}

	public function set_label ( mixed $val ) {
		$this->label = $val;
		return $this;
	}

	public function set_label_comment ( mixed $val ) {
		$this->label_comment = $val;
		return $this;
	}

	public function set_var_readonly ( bool $val ) {
		$this->readonly = $val;
		return $this;
	}

	public function set_var_disabled ( bool $val ) {
		$this->disabled = $val;
		return $this;
	}

	public function set_popup ( bool $popup , string $position = 'top' ) {
		$this->label_comment_popup = $popup;
		$this->label_comment_popup_position = $position;
		return $this;
	}
}
?>
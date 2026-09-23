<?PHP

class visuoel_field_select extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'select';
	}

	protected function make_input ( ) : visuoel_input__element {
		return new visuoel_input_select( $this );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_select__data( $this );
	}
}

class visuoel_field_select__data extends visuoel_field__element__data {
}
<?PHP

class visuoel_field_password extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'password';
	}

	protected function make_input ( ) : visuoel_input__element {
		return new visuoel_input_password( $this );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_password__data( $this );
	}
}

class visuoel_field_password__data extends visuoel_field__element__data {
}
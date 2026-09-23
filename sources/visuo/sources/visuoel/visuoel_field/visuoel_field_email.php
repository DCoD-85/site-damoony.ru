<?PHP

class visuoel_field_email extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'email';
	}

	protected function make_input ( ) : visuoel_input__element {
		return new visuoel_input_email( $this );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_email__data( $this );
	}
}

class visuoel_field_email__data extends visuoel_field__element__data {
}
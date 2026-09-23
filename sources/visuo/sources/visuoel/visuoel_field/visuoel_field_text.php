<?PHP

class visuoel_field_text extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'text';
	}

	protected function make_input ( ) : visuoel_input__element {
		return new visuoel_input_text( $this );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_text__data( $this );
	}
}

class visuoel_field_text__data extends visuoel_field__element__data {
}
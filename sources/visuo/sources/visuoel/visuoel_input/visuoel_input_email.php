<?PHP

class visuoel_input_email extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'email';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_email__data( $this );
	}
}

class visuoel_input_email__data extends visuoel_input__element__data {
}
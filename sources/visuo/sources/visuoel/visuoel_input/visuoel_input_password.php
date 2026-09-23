<?PHP

class visuoel_input_password extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'password';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_password__data( $this );
	}
}

class visuoel_input_password__data extends visuoel_input__element__data {
}
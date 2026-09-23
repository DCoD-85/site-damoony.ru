<?PHP

class visuoel_input_reset extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'reset';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_reset__data( $this );
	}
}

class visuoel_input_reset__data extends visuoel_input__element__data {
}
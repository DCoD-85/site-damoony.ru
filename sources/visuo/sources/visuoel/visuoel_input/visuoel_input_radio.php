<?PHP

class visuoel_input_radio extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'radio';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_radio__data( $this );
	}
}

class visuoel_input_radio__data extends visuoel_input__element__data {
}
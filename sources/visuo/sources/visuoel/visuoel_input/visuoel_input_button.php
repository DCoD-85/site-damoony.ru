<?PHP

class visuoel_input_button extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'button';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_button__data( $this );
	}
}

class visuoel_input_button__data extends visuoel_input__element__data {
}
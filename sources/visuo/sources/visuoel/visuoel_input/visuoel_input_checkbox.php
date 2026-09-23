<?PHP

class visuoel_input_checkbox extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'checkbox';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_checkbox__data( $this );
	}
}

class visuoel_input_checkbox__data extends visuoel_input__element__data {
}
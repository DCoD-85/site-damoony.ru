<?PHP

class visuoel_input_submit extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'submit';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_submit__data( $this );
	}
}

class visuoel_input_submit__data extends visuoel_input__element__data {
}
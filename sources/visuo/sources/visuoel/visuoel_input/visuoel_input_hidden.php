<?PHP

class visuoel_input_hidden extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'hidden';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_hidden__data( $this );
	}
}

class visuoel_input_hidden__data extends visuoel_input__element__data {
}
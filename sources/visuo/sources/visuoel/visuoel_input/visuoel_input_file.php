<?PHP

class visuoel_input_file extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'file';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_file__data( $this );
	}
}

class visuoel_input_file__data extends visuoel_input__element__data {
}
<?PHP

class visuoel_field_file extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'file';
	}

	protected function make_input ( ) : visuoel_input__element {
		return new visuoel_input_file( $this );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_file__data( $this );
	}
}

class visuoel_field_file__data extends visuoel_field__element__data {
}
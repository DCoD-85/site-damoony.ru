<?PHP

class visuoel_field_reset extends visuoel_field__button {
	protected function input_type ( ) : string {
		return 'reset';
	}

	protected function make_button ( ) : visuoel_button__element {
		return new visuoel_button_reset( );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_reset__data( $this );
	}
}

class visuoel_field_reset__data extends visuoel_field__button__data {
}
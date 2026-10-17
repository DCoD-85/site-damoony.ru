<?PHP

class visuoel_field_submit extends visuoel_field__button {
	protected function input_type ( ) : string {
		return 'submit';
	}

	protected function make_button ( ) : visuoel_button__element {
		return new visuoel_button_submit( );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_submit__data( $this );
	}
}

class visuoel_field_submit__data extends visuoel_field__button__data {
}
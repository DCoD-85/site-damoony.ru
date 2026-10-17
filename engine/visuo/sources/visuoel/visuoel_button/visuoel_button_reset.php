<?PHP

class visuoel_button_reset extends visuoel_button__element {
	protected function button_type ( ) : string {
		return 'reset';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_button_reset__data( $this );
	}
}

class visuoel_button_reset__data extends visuoel_button__element__data {
}
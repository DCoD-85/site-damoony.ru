<?PHP

class visuoel_button_button extends visuoel_button__element {
	protected function button_type ( ) : string {
		return 'button';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_button_button__data( $this );
	}
}

class visuoel_button_button__data extends visuoel_button__element__data {
}
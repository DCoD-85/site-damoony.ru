<?PHP

class visuoel_button_submit extends visuoel_button__element {
	protected function button_type ( ) : string {
		return 'submit';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_button_submit__data( $this );
	}
}

class visuoel_button_submit__data extends visuoel_button__element__data {
}
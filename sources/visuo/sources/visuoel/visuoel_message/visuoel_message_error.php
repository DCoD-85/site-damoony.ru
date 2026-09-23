<?PHP

class visuoel_message_error extends visuoel_message__element {
	protected function message_type ( ) : string { return 'error'; }
	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_message_error__data( $this );
	}
}

class visuoel_message_error__data extends visuoel_message__element__data {
}
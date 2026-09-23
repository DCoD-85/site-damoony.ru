<?PHP

class visuoel_message_warning extends visuoel_message__element {
	protected function message_type ( ) : string { return 'warning'; }
	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_message_warning__data( $this );
	}
}

class visuoel_message_warning__data extends visuoel_message__element__data {
}
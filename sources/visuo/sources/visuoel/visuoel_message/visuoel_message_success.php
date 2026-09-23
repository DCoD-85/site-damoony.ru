<?PHP

class visuoel_message_success extends visuoel_message__element {
	protected function message_type ( ) : string { return 'success'; }
	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_message_success__data( $this );
	}
}

class visuoel_message_success__data extends visuoel_message__element__data {
}
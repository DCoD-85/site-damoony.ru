<?PHP

class visuoel_message_info extends visuoel_message__element {
	protected function message_type ( ) : string { return 'info'; }
	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_message_info__data( $this );
	}
}

class visuoel_message_info__data extends visuoel_message__element__data {
}
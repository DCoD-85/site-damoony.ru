<?PHP

class visuoel_message extends visuoel_group {
	public bool $singleton = false;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_message__data( $this );
	}

	protected function build ( ) : void {
		$vt = new visuotag( );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function info ( null|visuoel__data $__data = null ) : visuoel_message_info {
		$data = $__data instanceof visuoel_message_info__data ? $__data : null;
		$el = new visuoel_message_info( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function success ( null|visuoel__data $__data = null ) : visuoel_message_success {
		$data = $__data instanceof visuoel_message_success__data ? $__data : null;
		$el = new visuoel_message_success( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function warning ( null|visuoel__data $__data = null ) : visuoel_message_warning {
		$data = $__data instanceof visuoel_message_warning__data ? $__data : null;
		$el = new visuoel_message_warning( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function error ( null|visuoel__data $__data = null ) : visuoel_message_error {
		$data = $__data instanceof visuoel_message_error__data ? $__data : null;
		$el = new visuoel_message_error( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
}

class visuoel_message__data extends visuoel_group__data {
}
<?PHP

class visuoel_input_submit extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'submit';
	}

	public function data ( ) : visuoel_input_submit__data {
		/** @var visuoel_input_submit__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_submit__data( $this );
	}
}

class visuoel_input_submit__data extends visuoel_input__element__data {
}
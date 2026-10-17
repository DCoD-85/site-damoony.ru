<?PHP

class visuoel_input_reset extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'reset';
	}

	public function data ( ) : visuoel_input_reset__data {
		/** @var visuoel_input_reset__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_reset__data( $this );
	}
}

class visuoel_input_reset__data extends visuoel_input__element__data {
}
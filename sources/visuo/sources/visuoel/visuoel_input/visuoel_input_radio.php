<?PHP

class visuoel_input_radio extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'radio';
	}

	public function data ( ) : visuoel_input_radio__data {
		/** @var visuoel_input_radio__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_radio__data( $this );
	}
}

class visuoel_input_radio__data extends visuoel_input__element__data {
}
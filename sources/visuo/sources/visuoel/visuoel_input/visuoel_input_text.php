<?PHP

class visuoel_input_text extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'text';
	}

	public function data ( ) : visuoel_input_text__data {
		/** @var visuoel_input_text__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_text__data( $this );
	}
}

class visuoel_input_text__data extends visuoel_input__element__data {
}
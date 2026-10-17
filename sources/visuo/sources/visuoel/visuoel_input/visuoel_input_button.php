<?PHP

class visuoel_input_button extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'button';
	}

	public function data ( ) : visuoel_input_button__data {
		/** @var visuoel_input_button__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_button__data( $this );
	}
}

class visuoel_input_button__data extends visuoel_input__element__data {
}
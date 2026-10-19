<?PHP

class visuoel_input_checkbox extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'checkbox';
	}

	public function data ( ) : visuoel_input_checkbox__data {
		/** @var visuoel_input_checkbox__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_checkbox__data( $this );
	}
}

class visuoel_input_checkbox__data extends visuoel_input__element__data {
}
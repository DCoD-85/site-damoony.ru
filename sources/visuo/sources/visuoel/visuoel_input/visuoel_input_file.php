<?PHP

class visuoel_input_file extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'file';
	}

	public function data ( ) : visuoel_input_file__data {
		/** @var visuoel_input_file__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_file__data( $this );
	}
}

class visuoel_input_file__data extends visuoel_input__element__data {
}
<?PHP

# visuoel_input — группа полей ввода.
# Обёртка: <div class="input">[элементы]</div>.
# Multi-элементы: text, hidden, password, submit, reset, button.

class visuoel_input extends visuoel_group {
	protected string $css_class = 'input';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input__data( $this );
	}

	public function text ( null|visuoel__data $__data = null ) : visuoel_input_text {
		$data = $__data instanceof visuoel_input_text__data ? $__data : null;
		$el = new visuoel_input_text( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function hidden ( null|visuoel__data $__data = null ) : visuoel_input_hidden {
		$data = $__data instanceof visuoel_input_hidden__data ? $__data : null;
		$el = new visuoel_input_hidden( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function password ( null|visuoel__data $__data = null ) : visuoel_input_password {
		$data = $__data instanceof visuoel_input_password__data ? $__data : null;
		$el = new visuoel_input_password( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
	
	public function email ( null|visuoel__data $__data = null ) : visuoel_input_email {
		$data = $__data instanceof visuoel_input_email__data ? $__data : null;
		$el = new visuoel_input_email( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function file ( null|visuoel__data $__data = null ) : visuoel_input_file {
		$data = $__data instanceof visuoel_input_file__data ? $__data : null;
		$el = new visuoel_input_file( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function textarea ( null|visuoel__data $__data = null ) : visuoel_input_textarea {
		$data = $__data instanceof visuoel_input_textarea__data ? $__data : null;
		$el = new visuoel_input_textarea( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function select ( null|visuoel__data $__data = null ) : visuoel_input_select {
		$data = $__data instanceof visuoel_input_select__data ? $__data : null;
		$el = new visuoel_input_select( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function submit ( null|visuoel__data $__data = null ) : visuoel_input_submit {
		$data = $__data instanceof visuoel_input_submit__data ? $__data : null;
		$el = new visuoel_input_submit( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function reset ( null|visuoel__data $__data = null ) : visuoel_input_reset {
		$data = $__data instanceof visuoel_input_reset__data ? $__data : null;
		$el = new visuoel_input_reset( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function button ( null|visuoel__data $__data = null ) : visuoel_input_button {
		$data = $__data instanceof visuoel_input_button__data ? $__data : null;
		$el = new visuoel_input_button( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	# --- last getters ---
	public function get_text__data ( ) : null|visuoel_input_text__data {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel_input_text ) return $el->data( );
		}
		return null;
	}

	public function get_hidden__data ( ) : null|visuoel_input_hidden__data {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel_input_hidden ) return $el->data( );
		}
		return null;
	}

	public function get_password__data ( ) : null|visuoel_input_password__data {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel_input_password ) return $el->data( );
		}
		return null;
	}

		public function get_email__data ( ) : null|visuoel_input_email__data {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel_input_email ) return $el->data( );
		}
		return null;
	}

	public function get_file__data ( ) : null|visuoel_input_file__data {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel_input_file ) return $el->data( );
		}
		return null;
	}

	public function get_textarea__data ( ) : null|visuoel_input_textarea__data {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel_input_textarea ) return $el->data( );
		}
		return null;
	}
	
	public function get_select__data ( ) : null|visuoel_input_select__data {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel_input_select ) return $el->data( );
		}
		return null;
	}
	
	public function get_submit__data ( ) : null|visuoel_input_submit__data {
		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel_input_submit ) return $el->data( );
		}
		return null;
	}
}

class visuoel_input__data extends visuoel_group__data {
}
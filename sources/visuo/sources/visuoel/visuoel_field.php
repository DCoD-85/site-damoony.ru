<?PHP

# visuoel_field — группа полей с обёрткой (label + input + hint + error).
#
# Группа прозрачная — обёртку рисует КАЖДЫЙ элемент сам:
#   <div class="field field_text">
#     <label for="field_login">Логин</label>
#     <input type="text" name="login" id="field_login" />
#     <div class="hint">Email</div>
#   </div>

class visuoel_field extends visuoel_group {
	public bool $singleton = false;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field__data( $this );
	}

	protected function build ( ) : void {
		$vt = new visuotag( );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function text ( null|visuoel__data $__data = null ) : visuoel_field_text {
		$data = $__data instanceof visuoel_field_text__data ? $__data : null;
		$el = new visuoel_field_text( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function password ( null|visuoel__data $__data = null ) : visuoel_field_password {
		$data = $__data instanceof visuoel_field_password__data ? $__data : null;
		$el = new visuoel_field_password( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function email ( null|visuoel__data $__data = null ) : visuoel_field_email {
		$data = $__data instanceof visuoel_field_email__data ? $__data : null;
		$el = new visuoel_field_email( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function file ( null|visuoel__data $__data = null ) : visuoel_field_file {
		$data = $__data instanceof visuoel_field_file__data ? $__data : null;
		$el = new visuoel_field_file( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function textarea ( null|visuoel__data $__data = null ) : visuoel_field_textarea {
		$data = $__data instanceof visuoel_field_textarea__data ? $__data : null;
		$el = new visuoel_field_textarea( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function select ( null|visuoel__data $__data = null ) : visuoel_field_select {
		$data = $__data instanceof visuoel_field_select__data ? $__data : null;
		$el = new visuoel_field_select( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function checkbox ( null|visuoel__data $__data = null ) : visuoel_field_checkbox {
		$data = $__data instanceof visuoel_field_checkbox__data ? $__data : null;
		$el = new visuoel_field_checkbox( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function radio ( null|visuoel__data $__data = null ) : visuoel_field_radio {
		$data = $__data instanceof visuoel_field_radio__data ? $__data : null;
		$el = new visuoel_field_radio( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function checkbox_group ( null|visuoel__data $__data = null ) : visuoel_field_checkbox_group {
		$data = $__data instanceof visuoel_field_checkbox_group__data ? $__data : null;
		$el = new visuoel_field_checkbox_group( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function radio_group ( null|visuoel__data $__data = null ) : visuoel_field_radio_group {
		$data = $__data instanceof visuoel_field_radio_group__data ? $__data : null;
		$el = new visuoel_field_radio_group( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
}

class visuoel_field__data extends visuoel_group__data {
}
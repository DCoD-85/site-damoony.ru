<?PHP

# visuoel_button — группа кнопок.
# Прозрачная — каждая кнопка рендерит свой <button>.
# Multi-группа: submit + reset + cancel — обычная ситуация.

class visuoel_button extends visuoel_group {
	public bool $singleton = false;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_button__data( $this );
	}

	protected function build ( ) : void {
		$vt = new visuotag( );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function submit ( null|visuoel__data $__data = null ) : visuoel_button_submit {
		$data = $__data instanceof visuoel_button_submit__data ? $__data : null;
		$el = new visuoel_button_submit( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function reset ( null|visuoel__data $__data = null ) : visuoel_button_reset {
		$data = $__data instanceof visuoel_button_reset__data ? $__data : null;
		$el = new visuoel_button_reset( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function button ( null|visuoel__data $__data = null ) : visuoel_button_button {
		$data = $__data instanceof visuoel_button_button__data ? $__data : null;
		$el = new visuoel_button_button( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
}

class visuoel_button__data extends visuoel_group__data {
}
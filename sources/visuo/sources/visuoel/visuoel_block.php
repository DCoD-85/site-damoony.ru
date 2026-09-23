<?PHP

class visuoel_block extends visuoel_group {
	public bool $singleton = false;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_block__data( $this );
	}

	protected function build ( ) : void {
		$vt = new visuotag( );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function text ( null|visuoel__data $__data = null ) : visuoel_block_text {
		$data = $__data instanceof visuoel_block_text__data ? $__data : null;
		$el = new visuoel_block_text( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function html ( null|visuoel__data $__data = null ) : visuoel_block_html {
		$data = $__data instanceof visuoel_block_html__data ? $__data : null;
		$el = new visuoel_block_html( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
}

class visuoel_block__data extends visuoel_group__data {
}
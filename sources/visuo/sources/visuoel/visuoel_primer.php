<?PHP

# visuoel_primer — группа primer.
# $singleton = false: каждый вызов ->text() / ->image() создаёт новый элемент.

class visuoel_primer extends visuoel_group {
	protected string $css_class = 'primer';
	public bool $singleton = false;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_primer__data( $this );
	}

	public function text ( null|visuoel__data $__data = null ) : visuoel_primer_text {
		$data = $__data instanceof visuoel_primer_text__data ? $__data : null;
		$el = new visuoel_primer_text( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function image ( null|visuoel__data $__data = null ) : visuoel_primer_image {
		$data = $__data instanceof visuoel_primer_image__data ? $__data : null;
		$el = new visuoel_primer_image( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
}

class visuoel_primer__data extends visuoel_group__data {
}
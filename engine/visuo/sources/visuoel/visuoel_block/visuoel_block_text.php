<?PHP

class visuoel_block_text extends visuoel_block__element {
	protected string $tag       = 'div';
	protected string $css_class = 'block_text';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_block_text__data( $this );
	}
}

class visuoel_block_text__data extends visuoel_block__element__data {
}
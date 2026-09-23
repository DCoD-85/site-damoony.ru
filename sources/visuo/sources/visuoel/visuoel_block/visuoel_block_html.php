<?PHP

# Сырой HTML без экранирования. Осторожно с пользовательскими данными!

class visuoel_block_html extends visuoel_block__element {
	protected string $tag       = 'div';
	protected string $css_class = 'block_html';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_block_html__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$vt = new visuotag( );
		$vt = $vt->tag( $this->tag , class : $this->css_class );

		if ( $d->content !== '' ) {
			$vt->content( $d->content );   # без экранирования
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_block_html__data extends visuoel_block__element__data {
}
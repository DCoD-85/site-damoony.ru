<?PHP

abstract class visuoel_block__element extends visuoel_group_element {
	public function data ( ) : visuoel_block__element__data {
		/** @var visuoel_block__element__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_block__element__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->data();

		$vt = new visuotag( );
		$vt = $vt->tag( $this->tag , class : $this->css_class );

		if ( $d->content !== '' ) {
			$vt->content( $d->esc( $d->content ) );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_block__element__data extends visuoel_group_element__data {
	public string $content = '';

	public function set ( string $content ) : static {
		$this->content = $content;
		return $this;
	}
}
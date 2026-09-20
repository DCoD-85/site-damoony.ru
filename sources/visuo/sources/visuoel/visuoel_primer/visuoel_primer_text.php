<?PHP

# visuoel_primer_text — элемент primer\text.
# Обёртка: <div class="text">[content]</div>.
# Лист: vt_start === vt_end.

class visuoel_primer_text extends visuoel_group_element {
	protected string $tag       = 'div';
	protected string $css_class = 'text';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_primer_text__data( $this );
	}

	protected function build ( ) : void {
		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : $this->css_class );
		if ( $this->__data->content !== '' ) {
			$vt->content( $this->__data->content );
		}
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_primer_text__data extends visuoel_group_element__data {
	# --- state ---
	public string $content = '';

	# --- methods ---
	public function set ( string $content ) : static {
		$this->content = $content;
		return $this;
	}
}
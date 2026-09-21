<?PHP

# visuoel_group_element — база для элементов групп.
# Рисует обёртку <div class="<css_class>">.
# Конкретные элементы обычно переопределяют build() (input рисует <input>, form_form — <form>).

class visuoel_group_element extends visuoel {
	protected string $css_class = 'element';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_group_element__data( $this );
	}

	protected function build ( ) : void {
		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : $this->css_class );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_group_element__data extends visuoel__data {
	# --- state ---
	public bool $readonly = false;
	public bool $disabled = false;
}
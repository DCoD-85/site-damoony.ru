<?PHP

# visuoel_group — база для групп (form, tabs, input, ...).
# Рисует обёртку <div class="<css_class>">.
# Конкретные группы переопределяют $css_class или build().

class visuoel_group extends visuoel {
	protected string $css_class = 'group';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_group__data( $this );
	}

	protected function build ( ) : void {
		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : $this->css_class );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_group__data extends visuoel__data {
}
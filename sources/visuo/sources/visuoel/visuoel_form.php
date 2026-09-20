<?PHP

# visuoel_form — группа form.
# Переопределяем build(): обёртка группы — <div class="blockform">.
# (b) — своя обёртка на уровне группы, элемент form_form идёт внутрь без своей.

class visuoel_form extends visuoel_group {
	protected string $css_class = 'blockform';

	public null|visuoel_form_form $visuoel_form_form = null;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_form__data( $this );
	}

	protected function build ( ) : void {
		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : 'blockform' );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	# Переопределяем родительский visuoel::form():
	# на группе form() создаёт не группу, а элемент form_form.
	public function form ( null|visuoel__data $__data = null ) : visuoel_form_form {
		if ( $this->visuoel_form_form === null ) {
			$data = $__data instanceof visuoel_form_form__data ? $__data : null;
			$this->visuoel_form_form = new visuoel_form_form( $this , $data );
			$this->elements = [ $this->visuoel_form_form ];
		}
		return $this->visuoel_form_form;
	}

	public function get_form_form__data ( ) : null|visuoel_form_form__data {
		return $this->visuoel_form_form?->data( );
	}
}

class visuoel_form__data extends visuoel_group__data {
}
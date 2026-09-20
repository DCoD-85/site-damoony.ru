<?PHP

# visuoel_form — группа form.
# Обёртка: <div class="form">[form_form]</div>.

class visuoel_form extends visuoel_group {
	protected string $css_class = 'form';

	public null|visuoel_form_form $visuoel_form_form = null;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_form__data( $this );
	}

	# Переопределяем родительскую фабрику visuoel::form():
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
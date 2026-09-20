<?PHP

# visuoel_form_form — элемент form\form.
# Обёртка: <form class="form_form">[children]</form>.

class visuoel_form_form extends visuoel_group_element {
	protected string $tag       = 'form';
	protected string $css_class = 'form_form';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_form_form__data( $this );
	}

	public function init ( ) : void {
		if ( $this->init ) return;
		$this->init = true;

		$vt = new visuotag( );
		$vt = $vt->tag( 'form' , class : $this->css_class );

		# TODO (этап 2.2): атрибуты (action, name, method, enctype, data-*)
		# TODO (этап 2.2): <div class="form_title">
		# TODO (этап 2.2): vt_end = <div class="form_body"></div> для детей

		foreach ( $this->elements as $el ) {
			$el->init( );
			if ( $el->vt_start === null ) continue;
			$vt->append( $el->vt_start );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_form_form__data extends visuoel_group_element__data {
	# TODO (этап 2.2): поля формы (form_name, form_method, form_action, form_enctype,
	#                  form_jquery, form_data_jquery, title_text, form_class).
}
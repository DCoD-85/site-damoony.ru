<?PHP

# visuoel_form — группа form.
# $singleton = true: один form_form на группу.

class visuoel_form extends visuoel_group {
	protected string $css_class = 'blockform';
	public bool $singleton = true;

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

	public function form ( null|visuoel__data $__data = null ) : visuoel_form_form {
		if ( $this->singleton && $this->visuoel_form_form !== null ) {
			return $this->visuoel_form_form;
		}

		$data = $__data instanceof visuoel_form_form__data ? $__data : null;
		$el = new visuoel_form_form( $this , $data );

		if ( $this->singleton ) $this->visuoel_form_form = $el;
		$this->elements = [ $el ];
		return $el;
	}

	public function get_form_form__data ( ) : null|visuoel_form_form__data {
		return $this->visuoel_form_form?->data( );
	}
}

class visuoel_form__data extends visuoel_group__data {
}
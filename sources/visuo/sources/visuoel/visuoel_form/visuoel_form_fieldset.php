<?PHP

# fieldset — контейнер полей с заголовком внутри form_body.
#   <fieldset class="form_fieldset">
#     <legend>Личные данные</legend>
#     …поля…
#   </fieldset>

class visuoel_form_fieldset extends visuoel_group_element {
	protected string $tag       = 'fieldset';
	protected string $css_class = 'form_fieldset';

	public function data ( ) : visuoel_form_fieldset__data {
		/** @var visuoel_form_fieldset__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_form_fieldset__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$vt = new visuotag( );
		$vt = $vt->tag( 'fieldset' , class : $this->css_class );

		if ( $d->legend !== '' ) {
			$vt->tag( 'legend' )->content( $d->esc( $d->legend ) )->parent( );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_form_fieldset__data extends visuoel_group_element__data {
	public string $legend = '';

	public function set ( string $legend ) : static {
		$this->legend = $legend;
		return $this;
	}
}
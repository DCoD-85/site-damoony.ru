<?PHP

# textarea — не <input>, а <textarea>value</textarea>.
# Переопределяет build() полностью.

class visuoel_input_textarea extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'textarea';
	}

	public function data ( ) : visuoel_input_textarea__data {
		/** @var visuoel_input_textarea__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_textarea__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$vt = new visuotag( );
		$vt = $vt->tag( 'textarea' );

		if ( $d->id           !== '' ) $vt->attrs_add( 'id'           , $d->id );
		if ( $d->name         !== '' ) $vt->attrs_add( 'name'         , $d->name );
		if ( $d->placeholder  !== '' ) $vt->attrs_add( 'placeholder'  , $d->placeholder );
		if ( $d->autocomplete !== '' ) $vt->attrs_add( 'autocomplete' , $d->autocomplete );
		if ( $d->rows > 0 )            $vt->attrs_add( 'rows'         , (string) $d->rows );
		if ( $d->cols > 0 )            $vt->attrs_add( 'cols'         , (string) $d->cols );
		if ( $d->required )            $vt->attrs_add( 'required'     , 'required' );
		if ( $d->readonly )            $vt->attrs_add( 'readonly'     , 'readonly' );
		if ( $d->disabled )            $vt->attrs_add( 'disabled'     , 'disabled' );

		if ( $d->value !== '' ) $vt->content( $d->esc( $d->value ) );

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_input_textarea__data extends visuoel_input__element__data {
}
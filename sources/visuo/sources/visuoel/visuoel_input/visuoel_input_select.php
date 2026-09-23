<?PHP

# visuoel_input_select — стандартный <select><option>…</select>.
# Переопределяет build() полностью — это не <input>.

class visuoel_input_select extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'select';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_select__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$vt = new visuotag( );
		$vt = $vt->tag( 'select' );

		if ( $d->id           !== '' ) $vt->attrs_add( 'id'           , $d->id );
		if ( $d->name         !== '' ) $vt->attrs_add( 'name'         , $d->name );
		if ( $d->autocomplete !== '' ) $vt->attrs_add( 'autocomplete' , $d->autocomplete );
		if ( $d->size > 0 )            $vt->attrs_add( 'size'         , (string) $d->size );
		if ( $d->multiple )            $vt->attrs_add( 'multiple'     , 'multiple' );
		if ( $d->required )            $vt->attrs_add( 'required'     , 'required' );
		if ( $d->readonly )            $vt->attrs_add( 'readonly'     , 'readonly' );
		if ( $d->disabled )            $vt->attrs_add( 'disabled'     , 'disabled' );

		# placeholder — первый option
		if ( $d->placeholder_option !== '' ) {
			$opt = $vt->tag( 'option' );
			$opt->attrs_add( 'value' , '' , true );
			$opt->content( htmlspecialchars( $d->placeholder_option , ENT_QUOTES ) );
		}

		# options
		$selected_list = is_array( $d->selected ) ? $d->selected : [ $d->selected ];

		foreach ( $d->options as $value => $label ) {
			$opt = $vt->tag( 'option' );
			$opt->attrs_add( 'value' , (string) $value );
			if ( in_array( (string) $value , array_map( 'strval' , $selected_list ) , true ) ) {
				$opt->attrs_add( 'selected' , 'selected' );
			}
			$opt->content( htmlspecialchars( (string) $label , ENT_QUOTES ) );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_input_select__data extends visuoel_input__element__data {
}
<?PHP

# field/checkbox — label оборачивает <input type="checkbox"> и текст.
#   <div class="field field_checkbox">
#     <label>
#       <input type="checkbox" name="agree" />
#       Текст подписи
#     </label>
#   </div>

class visuoel_field_checkbox extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'checkbox';
	}

	protected function make_input ( ) : visuoel_input__element {
		return new visuoel_input_checkbox( $this );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_checkbox__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$field = new visuotag( );
		$field = $field->tag( 'div' , class : 'field field_checkbox' );

		$label = $field->tag( 'label' );

		# input внутри label
		$input_el = $this->make_input( );
		$inp = $input_el->data( );
		$inp->id       = 'field_' . $d->name;
		$inp->name     = $d->name;
		$inp->value    = $d->value !== '' ? $d->value : '1';
		$inp->checked  = $d->checked;
		$inp->required = $d->required;
		$inp->disabled = $d->disabled;

		$input_el->init( );
		if ( $input_el->vt_start !== null ) {
			$label->append( $input_el->vt_start );
		}

		# текст подписи
		if ( $d->label !== '' ) {
			$label->content( ' ' . htmlspecialchars( $d->label , ENT_QUOTES ) );
		}

		# hint / error — вне label
		if ( $d->hint !== '' ) {
			$field->tag( 'div' , class : 'hint' )->content( $d->hint );
		}
		if ( $d->error !== '' ) {
			$field->tag( 'div' , class : 'error' )->content( $d->error );
		}

		$this->vt_start = $field;
		$this->vt_end   = $field;
	}
}

class visuoel_field_checkbox__data extends visuoel_field__element__data {
}
<?PHP

# visuoel_field_checkbox_group — группа чекбоксов с заголовком.
#
# <div class="field field_checkbox_group">
#   <div class="field_label">Интересы</div>
#   <div class="options">
#     <div class="option"><label><input type="checkbox" …> …</label></div>
#   </div>
#   <div class="hint">…</div>
# </div>

class visuoel_field_checkbox_group extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'checkbox_group';
	}

	protected function make_input ( ) : visuoel_input__element {
		return new visuoel_input_checkbox_group( $this );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_checkbox_group__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$field = new visuotag( );
		$field = $field->tag( 'div' , class : 'field field_checkbox_group' );

		# заголовок группы
		if ( $d->label !== '' ) {
			$label = $field->tag( 'div' , class : 'field_label' );
			$label->content( htmlspecialchars( $d->label , ENT_QUOTES ) );
			if ( $d->label_comment !== '' ) {
				$label->tag( 'span' , class : 'label_comment' )->content( htmlspecialchars( $d->label_comment , ENT_QUOTES ) );
			}
		}

		# options
		$input_el = $this->make_input( );
		$inp = $input_el->data( );
		$inp->name     = $d->name;
		$inp->options  = $d->options;
		$inp->selected = $d->selected;
		$inp->required = $d->required;
		$inp->disabled = $d->disabled;

		$input_el->init( );
		if ( $input_el->vt_start !== null ) {
			$field->append( $input_el->vt_start );
		}

		# hint / error
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

class visuoel_field_checkbox_group__data extends visuoel_field__element__data {
}
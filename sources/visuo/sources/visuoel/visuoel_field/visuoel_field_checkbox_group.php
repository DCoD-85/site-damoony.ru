<?PHP

# visuoel_field_checkbox_group — контейнер для N visuoel_field_checkbox.
#
# <div class="field field_checkbox_group">
#   <div class="box">
#     <div class="field-label">Интересы</div>
#     <div class="options">
#       <!-- сюда пришиваются дочерние field_checkbox -->
#     </div>
#     <div class="hint">…</div>
#     <div class="error">…</div>
#   </div>
# </div>
#
# Использование:
#   $g = $ve->field()->checkbox_group()->label( 'Интересы' );
#   $g->add( $ve->field()->checkbox()->label( 'Спорт' )->value( 'sport' )->checked( true ) );
#   $g->add( $ve->field()->checkbox()->label( 'Музыка' )->value( 'music' ) );

class visuoel_field_checkbox_group extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'checkbox_group';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_checkbox_group__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$field = new visuotag( );
		$field = $field->tag( 'div' , class : 'field field_checkbox_group' );

		$box = $field->tag( 'div' , class : 'box' );

		# заголовок группы
		if ( $d->show_label && $d->label !== '' ) {
			$label = $box->tag( 'div' , class : 'field-label' );
			$label->content( $d->esc( $d->label ) );
			if ( $d->label_comment !== '' ) {
				$label->tag( 'span' , class : 'label_comment' )->content( $d->esc( $d->label_comment ) );
			}
		}

		# слот для дочерних field_checkbox (vt_end)
		$options = $box->tag( 'div' , class : 'options' );

		# hint
		if ( $d->show_hint && $d->hint !== '' ) {
			$box->tag( 'div' , class : 'hint' )->content( $d->esc( $d->hint ) );
		}

		# error
		if ( $d->error !== '' ) {
			$box->tag( 'div' , class : 'error' )->content( $d->esc( $d->error ) );
		}

		$this->vt_start = $field;
		$this->vt_end   = $options;
	}
}

class visuoel_field_checkbox_group__data extends visuoel_field__element__data {
}
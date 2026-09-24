<?PHP

# visuoel_field_radio_group — контейнер для N visuoel_field_radio.
#
# <div class="field field_radio_group">
#   <div class="box">
#     <div class="field-label">Пол</div>
#     <div class="options">
#       <!-- сюда пришиваются дочерние field_radio -->
#     </div>
#     <div class="hint">…</div>
#     <div class="error">…</div>
#   </div>
# </div>
#
# Использование:
#   $g = $ve->field()->radio_group()->label( 'Пол' );
#   $g->add( $ve->field()->radio()->label( 'Мужской' )->value( 'm' )->checked( true ) );
#   $g->add( $ve->field()->radio()->label( 'Женский' )->value( 'f' ) );

class visuoel_field_radio_group extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'radio_group';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_radio_group__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$field = new visuotag( );
		$field = $field->tag( 'div' , class : 'field field_radio_group' );

		$box = $field->tag( 'div' , class : 'box' );

		# заголовок группы
		if ( $d->show_label && $d->label !== '' ) {
			$label = $box->tag( 'div' , class : 'field-label' );
			$label->content( htmlspecialchars( $d->label , ENT_QUOTES ) );
			if ( $d->label_comment !== '' ) {
				$label->tag( 'span' , class : 'label_comment' )->content( htmlspecialchars( $d->label_comment , ENT_QUOTES ) );
			}
		}

		# слот для дочерних field_radio (vt_end)
		$options = $box->tag( 'div' , class : 'options' );

		# hint
		if ( $d->show_hint && $d->hint !== '' ) {
			$box->tag( 'div' , class : 'hint' )->content( $d->hint );
		}

		# error
		if ( $d->error !== '' ) {
			$box->tag( 'div' , class : 'error' )->content( $d->error );
		}

		$this->vt_start = $field;
		$this->vt_end   = $options;
	}
}

class visuoel_field_radio_group__data extends visuoel_field__element__data {
}
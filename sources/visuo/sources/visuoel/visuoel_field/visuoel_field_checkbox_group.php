<?PHP

# visuoel_field_checkbox_group — контейнер для N visuoel_field_checkbox.
#
# <div class="field field_checkbox_group">
#   <div class="field_label">Интересы</div>
#   <div class="options">
#     <!-- сюда пришиваются дочерние field_checkbox -->
#   </div>
#   <div class="hint">…</div>
#   <div class="error">…</div>
# </div>
#
# Использование:
#   $g = $ve->field()->checkbox_group()->label( 'Интересы' );
#   $g->add( $ve->field()->checkbox()->label( 'Футбол' )->value( 'football' )->checked( true ) );
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

		# заголовок группы
		if ( $d->label !== '' ) {
			$label = $field->tag( 'div' , class : 'field_label' );
			$label->content( htmlspecialchars( $d->label , ENT_QUOTES ) );
			if ( $d->label_comment !== '' ) {
				$label->tag( 'span' , class : 'label_comment' )->content( htmlspecialchars( $d->label_comment , ENT_QUOTES ) );
			}
		}

		# слот для дочерних field_checkbox (vt_end)
		$options = $field->tag( 'div' , class : 'options' );

		# hint / error — сиблинги options, добавляются до пришивания детей
		if ( $d->hint !== '' ) {
			$field->tag( 'div' , class : 'hint' )->content( $d->hint );
		}
		if ( $d->error !== '' ) {
			$field->tag( 'div' , class : 'error' )->content( $d->error );
		}

		$this->vt_start = $field;
		$this->vt_end   = $options;
	}
}

class visuoel_field_checkbox_group__data extends visuoel_field__element__data {
}
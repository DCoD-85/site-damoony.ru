<?PHP

# visuoel_field_checkbox — label оборачивает border-box + name.
#
#   <div class="field field_checkbox">
#     <div class="box">
#       <label class="field-label">
#         <span class="border-box">
#           <input class="el-checkbox" type="checkbox" ...>
#         </span>
#         <span class="field-name">Запомнить меня</span>
#       </label>
#       <div class="hint">…</div>
#       <div class="error">…</div>
#     </div>
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

		$box = $field->tag( 'div' , class : 'box' );

		# label оборачивает input + name
		$label = $box->tag( 'label' , class : 'field-label' );

		$input_el = $this->make_input( );
		$inp = $input_el->data( );
		# id: убираем [] из name; если name групповой (с []), добавляем суффикс value
		$id_base = rtrim( $d->name , '[]' );
		$inp->id = 'field_' . $id_base . ( str_ends_with( $d->name , '[]' ) ? '_' . $d->value : '' );
		$inp->name     = $d->name;
		$inp->value    = $d->value !== '' ? $d->value : '1';
		$inp->checked  = $d->checked;
		$inp->required = $d->required;
		$inp->disabled = $d->disabled;

		$input_el->init( );

		$border = $label->tag( 'span' , class : 'border-box' );
		if ( $input_el->vt_start !== null ) {
			$border->append( $input_el->vt_start );
		}

		if ( $d->label !== '' ) {
			$label->tag( 'span' , class : 'field-name' )->content( $d->label );
		}

		# hint
		if ( $d->show_hint && $d->hint !== '' ) {
			$box->tag( 'div' , class : 'hint' )->content( $d->hint );
		}

		# error
		if ( $d->error !== '' ) {
			$box->tag( 'div' , class : 'error' )->content( $d->error );
		}

		$this->vt_start = $field;
		$this->vt_end   = $field;
	}
}

class visuoel_field_checkbox__data extends visuoel_field__element__data {
}
<?PHP

# visuoel_field_radio — label оборачивает border-box + name.
# Аналог field_checkbox, но type="radio" и id включает value.
#
#   <div class="field field_radio">
#     <div class="box">
#       <label class="field-label">
#         <span class="border-box">
#           <input class="el-radio" type="radio" id="field_{name}_{value}" name="{name}" value="{value}">
#         </span>
#         <span class="field-name">Мужской</span>
#       </label>
#       <div class="hint">…</div>
#       <div class="error">…</div>
#     </div>
#   </div>

class visuoel_field_radio extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'radio';
	}

	protected function make_input ( ) : visuoel_input__element {
		return new visuoel_input_radio( $this );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_radio__data( $this );
	}

	public function data ( ) : visuoel_field_radio__data {
		/** @var visuoel_field_radio__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function build ( ) : void {
		$d = $this->data();

		$field = new visuotag( );
		$field = $field->tag( 'div' , class : 'field field_radio' );

		$box = $field->tag( 'div' , class : 'box' );

		# label оборачивает input + name
		$label = $box->tag( 'label' , class : 'field-label' );

		$input_el = $this->make_input( );
		$inp = $input_el->data( );
		$inp->id       = 'field_' . $d->name . '_' . $d->value;
		$inp->name     = $d->name;
		$inp->value    = $d->value;
		$inp->checked  = $d->checked;
		$inp->required = $d->required;
		$inp->disabled = $d->disabled;

		$input_el->init( );

		$border = $label->tag( 'span' , class : 'border-box' );
		if ( $input_el->vt_start !== null ) {
			$border->append( $input_el->vt_start );
		}

		if ( $d->label !== '' ) {
			$label->tag( 'span' , class : 'field-name' )->content( $d->esc( $d->label ) );
		}

		# hint
		if ( $d->show_hint && $d->hint !== '' ) {
			$box->tag( 'div' , class : 'hint' )->content( $d->esc( $d->hint ) );
		}

		# error
		if ( $d->error !== '' ) {
			$box->tag( 'div' , class : 'error' )->content( $d->esc( $d->error ) );
		}

		$this->vt_start = $field;
		$this->vt_end   = $field;
	}
}

class visuoel_field_radio__data extends visuoel_field__element__data {
}
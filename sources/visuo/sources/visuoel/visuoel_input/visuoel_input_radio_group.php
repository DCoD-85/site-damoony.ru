<?PHP

# visuoel_input_radio_group — группа radio с общим name.
# Рендерит <div class="options">…</div> с N input внутри.

class visuoel_input_radio_group extends visuoel_input__element {
	protected function input_type ( ) : string {
		return 'radio_group';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input_radio_group__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : 'options' );

		if ( $d->name === '' || empty( $d->options ) ) {
			$this->vt_start = $vt;
			$this->vt_end   = $vt;
			return;
		}

		$selected = is_array( $d->selected ) ? ( $d->selected[ 0 ] ?? null ) : $d->selected;

		foreach ( $d->options as $value => $label ) {
			$row = $vt->tag( 'div' , class : 'option' );
			$lbl = $row->tag( 'label' );

			$inp = $lbl->tag( 'input' , tag_closed : false );
			$inp->attrs_add( 'type'  , 'radio' );
			$inp->attrs_add( 'name'  , $d->name );
			$inp->attrs_add( 'value' , (string) $value );
			$inp->attrs_add( 'id'    , 'field_' . $d->name . '_' . $value );
			if ( (string) $value === (string) $selected ) {
				$inp->attrs_add( 'checked' , 'checked' );
			}
			if ( $d->required ) $inp->attrs_add( 'required' , 'required' );
			if ( $d->disabled ) $inp->attrs_add( 'disabled' , 'disabled' );

			$lbl->content( ' ' . htmlspecialchars( (string) $label , ENT_QUOTES ) );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_input_radio_group__data extends visuoel_input__element__data {
}
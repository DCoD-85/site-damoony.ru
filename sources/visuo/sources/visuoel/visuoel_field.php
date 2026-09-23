<?PHP

# visuoel_field — группа полей с обёрткой (label + input + hint + error).
#
# Группа прозрачная — обёртку рисует КАЖДЫЙ элемент сам:
#   <div class="field field_text">
#     <label for="field_login">Логин</label>
#     <input type="text" name="login" id="field_login" />
#     <div class="hint">Email</div>
#   </div>

class visuoel_field extends visuoel_group {
	public bool $singleton = false;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field__data( $this );
	}

	# Прозрачная — обёртки нет, дети идут прямо в vt_end.
	protected function build ( ) : void {
		$vt = new visuotag( );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function text ( null|visuoel__data $__data = null ) : visuoel_field_text {
		$data = $__data instanceof visuoel_field_text__data ? $__data : null;
		$el = new visuoel_field_text( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}

	public function password ( null|visuoel__data $__data = null ) : visuoel_field_password {
		$data = $__data instanceof visuoel_field_password__data ? $__data : null;
		$el = new visuoel_field_password( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
}

class visuoel_field__data extends visuoel_group__data {
}

# --- база элемента ---
abstract class visuoel_field__element extends visuoel_group_element {
	abstract protected function input_type ( ) : string;
	abstract protected function make_input ( ) : visuoel_input__element;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field__element__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$field = new visuotag( );
		$field = $field->tag( 'div' , class : 'field field_' . $this->input_type( ) );

		# label
		if ( $d->label !== '' ) {
			$label = $field->tag( 'label' );
			$label->attrs_add( 'for' , 'field_' . $d->name );
			$label->content( $d->label );
			if ( $d->label_comment !== '' ) {
				$label->tag( 'span' , class : 'label_comment' )->content( $d->label_comment );
			}
		}

		# input
		$input_el = $this->make_input( );
		$inp = $input_el->data( );
		$inp->id           = 'field_' . $d->name;
		$inp->name         = $d->name;
		$inp->value        = $d->value;
		$inp->placeholder  = $d->placeholder;
		$inp->autocomplete = $d->autocomplete;
		$inp->required     = $d->required;
		$inp->readonly     = $d->readonly;
		$inp->disabled     = $d->disabled;

		$input_el->init( );
		if ( $input_el->vt_start !== null ) {
			$field->append( $input_el->vt_start );
		}

		# hint
		if ( $d->hint !== '' ) {
			$field->tag( 'div' , class : 'hint' )->content( $d->hint );
		}

		# error
		if ( $d->error !== '' ) {
			$field->tag( 'div' , class : 'error' )->content( $d->error );
		}

		$this->vt_start = $field;
		$this->vt_end   = $field;
	}
}

class visuoel_field__element__data extends visuoel_input__element__data {
	public string $label         = '';
	public string $label_comment = '';
	public string $hint          = '';
	public string $error         = '';

	public function label ( string $label , string $label_comment = '' ) : static {
		$this->label         = $label;
		$this->label_comment = $label_comment;
		return $this;
	}

	public function hint ( string $hint ) : static {
		$this->hint = $hint;
		return $this;
	}

	public function error ( string $error ) : static {
		$this->error = $error;
		return $this;
	}
}
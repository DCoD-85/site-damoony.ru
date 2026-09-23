<?PHP

# visuoel_field__element — абстрактная база для всех field-элементов.
# Конкретные типы (text, password, select, ...) наследуются от неё
# и реализуют input_type() + make_input().

abstract class visuoel_field__element extends visuoel_group_element {
	abstract protected function input_type ( ) : string;
	abstract protected function make_input ( ) : visuoel_input__element;

	public function data ( ) : visuoel_field__element__data {
		/** @var visuoel_field__element__data $data */
		$data = $this->__data;
		return $data;
	}

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
		$inp->id                 = 'field_' . $d->name;
		$inp->name               = $d->name;
		$inp->value              = $d->value;
		$inp->placeholder        = $d->placeholder;
		$inp->autocomplete       = $d->autocomplete;
		$inp->accept             = $d->accept;
		$inp->required           = $d->required;
		$inp->readonly           = $d->readonly;
		$inp->disabled           = $d->disabled;
		$inp->multiple           = $d->multiple;
		$inp->rows               = $d->rows;
		$inp->cols               = $d->cols;
		$inp->options            = $d->options;
		$inp->selected           = $d->selected;
		$inp->size               = $d->size;
		$inp->placeholder_option = $d->placeholder_option;

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
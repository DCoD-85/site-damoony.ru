<?PHP

# visuoel_field__element — абстрактная база для field-элементов.
#
# Структура (для text/password/email/textarea/select):
#   <div class="field field_{type}">
#     <div class="box">
#       <label class="field-label" for="field_{name}">Текст</label>
#       <div class="border-box"><input class="el-{type}" ...></div>
#       <div class="hint">Комментарий</div>
#       <div class="error">Ошибка</div>
#     </div>
#   </div>
#
# Три слота (показ через флаги show_label / show_hint):
#   label   — название поля
#   element — border-box + голый input (рисует build_element)
#   hint    — комментарий
#
# Чекбоксы/радио переопределяют build() целиком (label оборачивает).
# Группы (checkbox_group/radio_group) рисуют свой контейнер.

abstract class visuoel_field__element extends visuoel_group_element {
	abstract protected function input_type ( ) : string;

	# Группы могут не переопределять — им один input не нужен.
	protected function make_input ( ) : null|visuoel_input__element {
		return null;
	}

	# Слот "element": по умолчанию border-box + input.
	protected function build_element ( visuotag $box ) : void {
		$input_el = $this->make_input( );
		if ( $input_el === null ) return;

		$d   = $this->__data;
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
		if ( $input_el->vt_start === null ) return;

		$border = $box->tag( 'div' , class : 'border-box' );
		$border->append( $input_el->vt_start );
	}

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

		$box = $field->tag( 'div' , class : 'box' );

		# слот 1 — label
		if ( $d->show_label && $d->label !== '' ) {
			$label = $box->tag( 'label' , class : 'field-label' );
			$label->attrs_add( 'for' , 'field_' . $d->name );
			$label->content( $d->esc( $d->label ) );
			if ( $d->label_comment !== '' ) {
				$label->tag( 'span' , class : 'label_comment' )->content( $d->esc( $d->label_comment ) );
			}
		}

		# слот 2 — element (рисует подкласс)
		$this->build_element( $box );

		# слот 3 — hint
		if ( $d->show_hint && $d->hint !== '' ) {
			$box->tag( 'div' , class : 'hint' )->content( $d->esc( $d->hint ) );
		}

		# error — всегда, если задан
		if ( $d->error !== '' ) {
			$box->tag( 'div' , class : 'error' )->content( $d->esc( $d->error ) );
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

	public bool $show_label = true;
	public bool $show_hint  = true;

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

	public function hide_label ( ) : static { $this->show_label = false; return $this; }
	public function hide_hint  ( ) : static { $this->show_hint  = false; return $this; }
}
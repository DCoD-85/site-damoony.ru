<?PHP

# visuoel_field__button — абстрактная база для field-обёрток над button.
#
# Структура:
#   <div class="field field_{type}">
#     <div class="box">
#       <div class="field-label">…</div>      ← если label задан и show_label
#       <div class="border-box">
#         <button class="button button_{type}">…</button>
#       </div>
#       <div class="hint">…</div>             ← если hint задан и show_hint
#       <div class="error">…</div>            ← если error задан
#     </div>
#   </div>
#
# Конкретные типы (submit/reset/button) переопределяют make_button().

abstract class visuoel_field__button extends visuoel_field__element {
	abstract protected function make_button ( ) : visuoel_button__element;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field__button__data( $this );
	}

	public function data ( ) : visuoel_field__button__data {
		/** @var visuoel_field__button__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function build ( ) : void {
		$d = $this->data();

		$field = new visuotag( );
		$field = $field->tag( 'div' , class : 'field field_' . $this->input_type( ) );

		$box = $field->tag( 'div' , class : 'box' );

		# слот 1 — label
		if ( $d->show_label && $d->label !== '' ) {
			$label = $box->tag( 'div' , class : 'field-label' );
			$label->content( $d->esc( $d->label ) );
			if ( $d->label_comment !== '' ) {
				$label->tag( 'span' , class : 'label_comment' )->content( $d->esc( $d->label_comment ) );
			}
		}

		# слот 2 — border-box + button
		$border = $box->tag( 'div' , class : 'border-box' );

		$btn = $this->make_button( );
		$btn->data( )->text      = $d->text;
		$btn->data( )->name      = $d->name;
		$btn->data( )->value     = $d->value;
		$btn->data( )->css_class = $d->css_class;
		$btn->data( )->disabled  = $d->disabled;
		$btn->init( );

		if ( $btn->vt_start !== null ) {
			$border->append( $btn->vt_start );
		}

		# слот 3 — hint
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

class visuoel_field__button__data extends visuoel_field__element__data {
	public string $text      = '';
	public string $css_class = '';

	public function text ( string $text ) : static {
		$this->text = $text;
		return $this;
	}

	public function css_class ( string $class ) : static {
		$this->css_class = $class;
		return $this;
	}
}
<?PHP

# visuoel_field_block — поле-обёртка над текстом/HTML.
# Даёт ту же структуру, что и остальные field'ы:
#
#   <div class="field field_block">
#     <div class="box">
#       <div class="field-content">…</div>
#     </div>
#   </div>
#
# Использование (в ячейке таблицы — вместо set()):
#   $f = new \visuoel_field_block( );
#   $f->data( )->set( 'Латиница, цифры, 3–32 символа' );
#   $row->cell( width : 43 )->add( $f );
#
# HTML-вариант (если нужно отдать разметку как есть):
#   $f->data( )->html( '<b>жирный</b> текст' );

class visuoel_field_block extends visuoel_field__element {
	protected function input_type ( ) : string {
		return 'block';
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_field_block__data( $this );
	}

	public function data ( ) : visuoel_field_block__data {
		/** @var visuoel_field_block__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function build ( ) : void {
		$d = $this->data();

		$field = new visuotag( );
		$field = $field->tag( 'div' , class : 'field field_block' );

		$box = $field->tag( 'div' , class : 'box' );

		# label (не рисуется, если пусто или hide_label)
		if ( $d->show_label && $d->label !== '' ) {
			$label = $box->tag( 'label' , class : 'field-label' );
			$label->content( $d->esc( $d->label ) );
		}

		# content (html = raw, text = esc)
		$content = $box->tag( 'div' , class : 'field-content' );
		$content->content( $d->is_html ? $d->content : $d->esc( $d->content ) );

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

class visuoel_field_block__data extends visuoel_field__element__data {
	public string $content = '';
	public bool   $is_html = false;

	public function text ( string $text ) : static {
		$this->content = $text;
		$this->is_html = false;
		return $this;
	}

	public function html ( string $html ) : static {
		$this->content = $html;
		$this->is_html = true;
		return $this;
	}
}
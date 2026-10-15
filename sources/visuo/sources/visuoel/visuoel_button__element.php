<?PHP

# visuoel_button__element — абстрактная база для всех button-элементов.
#
# Рендер:
#   <button type="submit" name="…" value="…" class="button button_submit">Текст</button>
#
# Без обёртки — кнопка сама себе контейнер.

abstract class visuoel_button__element extends visuoel_group_element {
	abstract protected function button_type ( ) : string;

	public function data ( ) : visuoel_button__element__data {
		/** @var visuoel_button__element__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_button__element__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$vt = new visuotag( );
		$vt = $vt->tag( 'button' );
		$vt->attrs_add( 'type' , $this->button_type( ) );
		$vt->attrs_class_add( 'button button_' . $this->button_type( ) );

		if ( $d->name  !== '' ) $vt->attrs_add( 'name'  , $d->name );
		if ( $d->value !== '' ) $vt->attrs_add( 'value' , $d->value );
		if ( $d->css_class !== '' ) $vt->attrs_class_add( ' ' . $d->css_class );
		if ( $d->disabled ) $vt->attrs_add( 'disabled' , 'disabled' );

		if ( $d->text !== '' ) {
			$vt->content( $d->esc( $d->text ) );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_button__element__data extends visuoel_group_element__data {
	public string $text      = '';
	public string $name      = '';
	public string $value     = '';
	public string $css_class = '';

	public function set ( string $text ) : static {
		$this->text = $text;
		return $this;
	}

	public function text ( string $text ) : static {
		$this->text = $text;
		return $this;
	}

	public function name ( string $name ) : static {
		$this->name = $name;
		return $this;
	}

	public function value ( string $value ) : static {
		$this->value = $value;
		return $this;
	}

	public function css_class ( string $class ) : static {
		$this->css_class = $class;
		return $this;
	}

	public function disabled ( bool $v = true ) : static {
		$this->disabled = $v;
		return $this;
	}
}
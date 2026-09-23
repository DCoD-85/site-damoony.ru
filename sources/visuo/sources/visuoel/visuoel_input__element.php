<?PHP

# visuoel_input__element — абстрактная база для всех input-элементов.
# Конкретные типы (text, hidden, password, ...) наследуются от неё
# и переопределяют input_type().

abstract class visuoel_input__element extends visuoel_group_element {
	abstract protected function input_type ( ) : string;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_input__element__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$vt = new visuotag( );
		$vt = $vt->tag( 'input' , tag_closed : false );
		$vt->attrs_add( 'type' , $this->input_type( ) );

		if ( $d->id           !== '' ) $vt->attrs_add( 'id'           , $d->id );
		if ( $d->name         !== '' ) $vt->attrs_add( 'name'         , $d->name );
		if ( $d->value        !== '' ) $vt->attrs_add( 'value'        , $d->value );
		if ( $d->placeholder  !== '' ) $vt->attrs_add( 'placeholder'  , $d->placeholder );
		if ( $d->autocomplete !== '' ) $vt->attrs_add( 'autocomplete' , $d->autocomplete );
		if ( $d->accept !== '' )       $vt->attrs_add( 'accept'       , $d->accept );
		if ( $d->multiple )            $vt->attrs_add( 'multiple'     , 'multiple' );
		if ( $d->required )            $vt->attrs_add( 'required'     , 'required' );
		if ( $d->readonly )            $vt->attrs_add( 'readonly'     , 'readonly' );
		if ( $d->disabled )            $vt->attrs_add( 'disabled'     , 'disabled' );

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_input__element__data extends visuoel_group_element__data {
	public string $id           = '';
	public string $name         = '';
	public string $value        = '';
	public string $placeholder  = '';
	public string $autocomplete = '';
	public string $accept       = '';
	public bool   $required     = false;
	public bool   $multiple     = false;
	public int    $rows         = 0;
	public int    $cols         = 0;

	public function set ( string $name , string $value = '' , string $placeholder = '' ) : static {
		$this->name        = $name;
		$this->value       = $value;
		$this->placeholder = $placeholder;
		return $this;
	}

	public function id ( string $id ) : static {
		$this->id = $id;
		return $this;
	}

	public function value ( string $value ) : static {
		$this->value = $value;
		return $this;
	}

	public function placeholder ( string $placeholder ) : static {
		$this->placeholder = $placeholder;
		return $this;
	}

	public function autocomplete ( string $autocomplete ) : static {
		$this->autocomplete = $autocomplete;
		return $this;
	}

	public function accept ( string $accept ) : static {
		$this->accept = $accept;
		return $this;
	}

	public function multiple ( bool $v = true ) : static {
		$this->multiple = $v;
		return $this;
	}

	public function rows ( int $rows ) : static {
		$this->rows = $rows;
		return $this;
	}

	public function cols ( int $cols ) : static {
		$this->cols = $cols;
		return $this;
	}

	public function required ( bool $v = true ) : static {
		$this->required = $v;
		return $this;
	}

	public function readonly ( bool $v = true ) : static {
		$this->readonly = $v;
		return $this;
	}

	public function disabled ( bool $v = true ) : static {
		$this->disabled = $v;
		return $this;
	}
}
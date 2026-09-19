<?PHP

class visuoattrs {
	# --- refs ---
	private null|\visuotag $visuotag = null;

	# --- sub-classes ---
	private \visuoattrsclass   $class;
	private \visuoattrsstyle   $style;
	private \visuoattrsactions $actions;

	# --- state ---
	private array $elements = [ ];   # [ 'id' => 'x' , 'href' => '...' , ... ]

	# --- methods ---
	public function __construct ( null|\visuotag $visuotag = null ) {
		$this->visuotag = $visuotag;
		$this->class    = new \visuoattrsclass( $this );
		$this->style    = new \visuoattrsstyle( $this );
		$this->actions  = new \visuoattrsactions( $this );
	}

	public function visuotag ( ) : null|\visuotag {
		return $this->visuotag;
	}

	# --- generic attributes ---
	public function add ( string $name , string $value , bool $add_empty = false ) : visuoattrs {
		if ( $value !== '' || $add_empty === true ) {
			$this->elements[ $name ] = $value;
		}
		return $this;
	}

	public function remove ( string $name ) : visuoattrs {
		unset( $this->elements[ $name ] );
		return $this;
	}

	public function get ( string $name ) : mixed {
		return array_key_exists( $name , $this->elements )
			? $this->elements[ $name ]
			: null;
	}

	public function has ( string $name ) : bool {
		return array_key_exists( $name , $this->elements );
	}

	public function all ( ) : array {
		return $this->elements;
	}

public function clear ( ) : visuoattrs {
	$this->elements = [ ];
	$this->class->clear( );
	$this->style->clear( );
	$this->actions->clear( );
	return $this;
}

	# --- sub-classes accessors ---
	public function class   ( ) : \visuoattrsclass   { return $this->class;   }
	public function style   ( ) : \visuoattrsstyle   { return $this->style;   }
	public function actions ( ) : \visuoattrsactions { return $this->actions; }

	public function class_add    ( string $expr ) : visuoattrs { $this->class->add( $expr );    return $this; }
	public function class_remove ( string $expr ) : visuoattrs { $this->class->remove( $expr ); return $this; }
	public function style_add    ( string $expr ) : visuoattrs { $this->style->add( $expr );    return $this; }
	public function style_remove ( string $expr ) : visuoattrs { $this->style->remove( $expr ); return $this; }
	public function actions_add    ( string $action , string $value ) : visuoattrs { $this->actions->add( $action , $value );    return $this; }
	public function actions_remove ( string $action ) : visuoattrs { $this->actions->remove( $action ); return $this; }

	# --- release ---
	# всё: generic + class + style + actions
	public function release ( ) : string {
		$html  = $this->class->release( );
		$html .= $this->style->release( );
		$html .= $this->actions->release( );
		$html .= $this->attributes_release( );
		return $html;
	}

	# только generic-атрибуты (без class/style/actions) — для releasetree()
	public function attributes_release ( ) : string {
		if ( empty( $this->elements ) ) return '';

		$html = '';
		foreach ( $this->elements as $name => $value ) {
			$html .= ' ' . $name . '="' . htmlspecialchars( (string) $value , ENT_QUOTES ) . '"';
		}
		return $html;
	}
}
<?PHP

# visuoel — база и корень дерева UI-элементов.
# Обёртка: <tag class="css_class">[children]</tag>.
# Наследники переопределяют $tag / $css_class, init() и фабрики.

class visuoel {
	# --- refs ---
	public null|visuoel $parent  = null;
	public null|visuoel $visuoel = null;

	# --- state ---
	public bool $init = false;
	public null|visuoel__data $__data = null;
	public null|visuotag $vt_start = null;
	public null|visuotag $vt_end   = null;
	public array $elements = [ ];

	# --- groups (lazy) ---
	public null|visuoel_group $visuoel_group = null;
	public null|visuoel_form  $visuoel_form  = null;

	# --- wrap config (override in subclasses) ---
	protected string $tag       = 'div';
	protected string $css_class = 'visuoel';

	# --- methods ---
	public function __construct ( null|visuoel $parent = null , null|visuoel__data $__data = null ) {
		$this->parent  = $parent;
		$this->visuoel = $parent?->visuoel ?? $this;
		$this->__data  = $__data ?? $this->make_default_data( );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel__data( $this );
	}

	public function init ( ) : void {
		if ( $this->init ) return;
		$this->init = true;

		$vt = new visuotag( );
		$vt = $vt->tag( $this->tag , class : $this->css_class );

		foreach ( $this->elements as $el ) {
			$el->init( );
			if ( $el->vt_start === null ) continue;
			$vt->append( $el->vt_start );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function add ( visuoel $el ) : static {
		$el->parent = $this;
		$this->elements[ ] = $el;
		return $this;
	}

	# --- lazy factories ---
	# Общий параметр null|visuoel__data — чтобы наследники могли сузить возврат,
	# не нарушая контравариантность параметра.
	public function form ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->visuoel_form === null ) {
			$this->visuoel_form  = new visuoel_form( $this , $__data );
			$this->visuoel_group = $this->visuoel_form;
			$this->elements[ ]   = $this->visuoel_form;
		}
		return $this->visuoel_form;
	}

	# --- getters ---
	public function data ( ) : null|visuoel__data {
		return $this->__data;
	}

	public function css_class ( ) : string {
		return $this->css_class;
	}

	public function tag ( ) : string {
		return $this->tag;
	}

	public function is_root ( ) : bool {
		return $this->parent === null;
	}

	public function release ( ) : string {
		if ( ! $this->init ) $this->init( );
		if ( $this->vt_start === null ) return '';
		return $this->vt_start->release( );
	}

	public function show ( ) : string {
		return $this->release( );
	}
}

class visuoel__data {
	# --- refs ---
	public visuoel $node;

	# --- methods ---
	public function __construct ( visuoel $node ) {
		$this->node = $node;
	}

	public function add ( visuoel $el ) : static {
		$this->node->add( $el );
		return $this;
	}
}
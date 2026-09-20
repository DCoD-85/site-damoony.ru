<?PHP

# visuoel — база и корень дерева UI-элементов.
# ...
# $singleton — режим фабрик:
#   true  — повторный вызов возвращает тот же элемент (одна форма на корень);
#   false — каждый вызов создаёт новый элемент (много элементов в группе).

class visuoel {
	# --- refs ---
	public null|visuoel $parent  = null;
	public null|visuoel $visuoel = null;

	# --- state ---
	public bool $init = false;
	public bool $singleton = true;
	public null|visuoel__data $__data = null;
	public null|visuotag $vt_start = null;
	public null|visuotag $vt_end   = null;
	public array $elements = [ ];

	# --- groups (lazy) ---
	public null|visuoel_group  $visuoel_group  = null;
	public null|visuoel_form   $visuoel_form   = null;
	public null|visuoel_primer $visuoel_primer = null;

	# --- wrap config ---
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

	protected function build ( ) : void {
		$vt = new visuotag( );
		$vt = $vt->tag( $this->tag , class : $this->css_class );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function init ( ) : void {
		if ( $this->init ) return;
		$this->init = true;

		$this->build( );

		if ( $this->vt_end === null ) return;

		foreach ( $this->elements as $el ) {
			if ( $el instanceof visuoel ) {
				$el->init( );
				if ( $el->vt_start === null ) continue;
				$this->vt_end->append( $el->vt_start );
			} elseif ( $el instanceof visuotag ) {
				$this->vt_end->append( $el );
			}
		}
	}

	public function add ( visuoel|visuotag $el ) : static {
		if ( $el instanceof visuoel ) {
			$el->parent = $this;
		}
		$this->elements[ ] = $el;
		return $this;
	}

	public function remove ( visuoel|visuotag $el ) : bool {
		foreach ( $this->elements as $i => $child ) {
			if ( $child === $el ) {
				if ( $child instanceof visuoel ) $child->parent = null;
				unset( $this->elements[ $i ] );
				$this->elements = array_values( $this->elements );
				return true;
			}
		}
		return false;
	}

	public function remove_class ( string $class ) : int {
		$removed = 0;
		foreach ( $this->elements as $i => $child ) {
			if ( $child instanceof $class ) {
				if ( $child instanceof visuoel ) $child->parent = null;
				unset( $this->elements[ $i ] );
				$removed++;
			}
		}
		$this->elements = array_values( $this->elements );
		return $removed;
	}

	public function clear ( ) : static {
		foreach ( $this->elements as $child ) {
			if ( $child instanceof visuoel ) $child->parent = null;
		}
		$this->elements = [ ];
		return $this;
	}

	# --- lazy factories ---
	public function form ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_form !== null ) return $this->visuoel_form;

		$el = new visuoel_form( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_form = $el;
		$this->elements[ ]  = $el;
		return $el;
	}

	public function primer ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_primer !== null ) return $this->visuoel_primer;

		$el = new visuoel_primer( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_primer = $el;
		$this->elements[ ]    = $el;
		return $el;
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

	public function add ( visuoel|visuotag $el ) : static {
		$this->node->add( $el );
		return $this;
	}

	public function remove ( visuoel|visuotag $el ) : bool {
		return $this->node->remove( $el );
	}

	public function remove_class ( string $class ) : int {
		return $this->node->remove_class( $class );
	}

	public function clear ( ) : static {
		$this->node->clear( );
		return $this;
	}
}
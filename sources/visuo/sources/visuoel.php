<?PHP

# visuoel — база и корень дерева UI-элементов.
#
# Инициализация:
#   init()
#     ├── build()                       ← фаза 1: собственный скелет
#     └── foreach elements:             ← фаза 2: дети пришиваются в vt_end
#           visuoel  → $el->init(); $this->vt_end->append( $el->vt_start )
#           visuotag → $this->vt_end->append( $el )
#
# vt_start — верх поддерева (attach point для родителя).
# vt_end   — узел-слот: дети пришиваются сюда.
#            у одиночных элементов (input, link_simple) vt_start === vt_end.
#            у контейнеров (form, table) vt_end — вложенный узел (form_body, tbody).
#
# elements[] — список детей: visuoel | visuotag.

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

	# Фаза 1: скелет. Переопределяется в наследниках.
	protected function build ( ) : void {
		$vt = new visuotag( );
		$vt = $vt->tag( $this->tag , class : $this->css_class );
		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	# Фаза 1 + фаза 2.
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

	# --- lazy factories ---
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

	public function add ( visuoel|visuotag $el ) : static {
		$this->node->add( $el );
		return $this;
	}
}
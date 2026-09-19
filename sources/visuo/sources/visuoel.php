<?PHP

# visuoel — база и корень дерева UI-элементов.
# Держит ссылки на родителя и корень, свои данные, флаг init.
# Наследники (visuoel_group, visuoel_group_element) переопределяют $css_class
# и make_default_data().

class visuoel {
	# --- refs ---
	public null|visuoel $parent = null;
	public null|visuoel $visuoel = null;

	# --- state ---
	public bool $init = false;
	public null|visuoel__data $__data = null;

	# --- wrap config (override in subclasses) ---
	protected string $tag = 'div';
	protected string $css_class = 'visuoel';

	# --- methods ---
	public function __construct ( null|visuoel $parent = null , null|visuoel__data $__data = null ) {
		$this->parent = $parent;
		$this->visuoel = $parent?->visuoel ?? $this;
		$this->__data  = $__data ?? $this->make_default_data( );
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel__data( $this );
	}

	public function init ( ) : void {
		$this->init = true;
	}

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
}

class visuoel__data {
	# --- refs ---
	public visuoel $node;

	# --- methods ---
	public function __construct ( visuoel $node ) {
		$this->node = $node;
	}
}
<?PHP

# visuoel — база и корень дерева UI-элементов.
#
# Инициализация:
#   init()
#     ├── build()                       ← фаза 1: собственный скелет
#     └── foreach elements:             ← фаза 2: дети пришиваются в vt_end
#           visuoel  → $el->init(); $this->vt_end->append( $el->vt_start )
#           visuotag → $el->prepare(); $this->vt_end->append( $el )
#
# vt_start — верх поддерева (attach point для родителя).
# vt_end   — узел-слот: дети пришиваются сюда.
#
# $singleton — режим фабрик:
#   true  — повторный вызов возвращает тот же элемент;
#   false — каждый вызов создаёт новый.

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

	public null|visuoel_group   $visuoel_group   = null;
	public null|visuoel_form    $visuoel_form    = null;
	public null|visuoel_tabs    $visuoel_tabs    = null;
	public null|visuoel_input   $visuoel_input   = null;
	public null|visuoel_field   $visuoel_field   = null;
	public null|visuoel_button  $visuoel_button  = null;
	public null|visuoel_block   $visuoel_block   = null;
	public null|visuoel_message $visuoel_message = null;
	public null|visuoel_table   $visuoel_table   = null;

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

	# Корень — прозрачный. Обёртку рисуют только группа и элемент группы.
	protected function build ( ) : void {
		$vt = new visuotag( );
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
				$el->prepare( );
				$this->vt_end->append( $el );
			}
		}
	}

	public function add ( visuoel|visuotag $el ) : static {
		if ( $el instanceof visuoel ) {
			# элемент может быть только у одного родителя: снимаем со старого
			if ( $el->parent !== null && $el->parent !== $this ) {
				$el->parent->remove( $el );
			}
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

	/** @return visuoel_form */
	public function form ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_form !== null ) return $this->visuoel_form;

		$el = new visuoel_form( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_form = $el;
		$this->elements[ ]  = $el;
		return $el;
	}

	/** @return visuoel_tabs */
	public function tabs ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_tabs !== null ) return $this->visuoel_tabs;

		$el = new visuoel_tabs( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_tabs = $el;
		$this->elements[ ]  = $el;
		return $el;
	}

	/** @return visuoel_input */
	public function input ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_input !== null ) return $this->visuoel_input;

		$el = new visuoel_input( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_input = $el;
		$this->elements[ ]   = $el;
		return $el;
	}

	/** @return visuoel_field */
	public function field ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_field !== null ) return $this->visuoel_field;

		$el = new visuoel_field( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_field = $el;
		$this->elements[ ]   = $el;
		return $el;
	}

	/** @return visuoel_button */
	public function button ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_button !== null ) return $this->visuoel_button;

		$el = new visuoel_button( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_button = $el;
		$this->elements[ ]    = $el;
		return $el;
	}

	/** @return visuoel_block */
	public function block ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_block !== null ) return $this->visuoel_block;

		$el = new visuoel_block( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_block = $el;
		$this->elements[ ]   = $el;
		return $el;
	}

	/** @return visuoel_message */
	public function message ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_message !== null ) return $this->visuoel_message;

		$el = new visuoel_message( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_message = $el;
		$this->elements[ ]     = $el;
		return $el;
	}

	/** @return visuoel_table */
	public function table ( null|visuoel__data $__data = null ) : visuoel {
		if ( $this->singleton && $this->visuoel_table !== null ) return $this->visuoel_table;

		$el = new visuoel_table( $this , $__data );
		if ( $this->visuoel_group === null ) $this->visuoel_group = $el;
		$this->visuoel_table = $el;
		$this->elements[ ]   = $el;
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

	public function get_input_text__data ( ) : null|visuoel_input_text__data {
		return $this->visuoel_input?->get_text__data( );
	}

	public function get_input_hidden__data ( ) : null|visuoel_input_hidden__data {
		return $this->visuoel_input?->get_hidden__data( );
	}

	public function get_input_password__data ( ) : null|visuoel_input_password__data {
		return $this->visuoel_input?->get_password__data( );
	}

	public function get_input_submit__data ( ) : null|visuoel_input_submit__data {
		return $this->visuoel_input?->get_submit__data( );
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

	# html-экранирование значений (единая точка для всего visuo)
	public function esc ( null|string $s ) : string {
		return htmlspecialchars( (string) $s , ENT_QUOTES , 'UTF-8' );
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
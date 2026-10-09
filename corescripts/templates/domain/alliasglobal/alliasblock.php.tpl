<?PHP

namespace <domain_ns>;

abstract class alliasblock {
	# --- refs ---
	public alliasglobal $alliasglobal;
	public \main $main;
	public \visuorelease $visuorelease;

	# --- state ---
	public string $alliasglobal_name = '';
	public string $allias_accepted   = '';

	# --- switchers ---
	public bool $enabled        = true;
	public bool $filler_enabled = true;

	# --- filler ---
	public null|object $filler = null;

	# --- content ---
	public null|alliasglobal_menu $menu = null;
	public string $wrapper_tag   = '';
	public array  $wrapper_attrs = [ ];
	public array  $sections      = [ ];
	public bool   $dirty         = false;

	# --- output ---
	public \visuotag $visuotag;

	# --- methods ---
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal    = $alliasglobal;
		$this->main            = $alliasglobal->main;
		$this->visuorelease    = $this->main->visuorelease;
		$this->visuotag        = new \visuotag( $this->visuorelease );
		$this->allias_accepted = $this->main->main_settings->allias_accepted( );
		$this->menu            = new alliasglobal_menu( $alliasglobal , $this->alliasglobal_name );

		$this->init( );
	}

	protected function init ( ) : void {
		$this->algoritm( );
	}

	public function algoritm ( ) : void {
		if ( $this->enabled === false ) return;

		$this->data( );
		$this->assemble( );
	}

	protected function data ( ) : void {
		if ( $this->try_filler( ) ) return;
		$this->default_data( );
	}

	protected function default_data ( ) : void { }

	protected function try_filler ( ) : bool {
		if ( $this->filler_enabled === false ) return false;

		# --- контекст ---
		$domain    = $this->main->main_settings->domain_real_name ?? '';   # '<domain>' — для конфига
		$domain_ns = $this->main->main_settings->domain_accepted( false ); # '<domain_ns>'
		$allias    = $this->main->main_settings->allias_real_name ?? '';   # 'visuo'

		if ( $domain === '' || $allias === '' || $domain_ns === null ) return false;

		# --- проверка конфига ---
		$fillers = $this->main->main_settings->fillers;
		if ( empty( $fillers[ $domain ][ $allias ][ $this->alliasglobal_name ] ) ) return false;

		# --- путь и класс ---
		$rc  = new \ReflectionClass( $this );
		$dir = dirname( $rc->getFileName( ) );

		$path  = $dir . DS . $this->alliasglobal_name . '_' . $allias . '.php';
		$class = '\\' . $domain_ns . '\\alliasglobal_' . $this->alliasglobal_name . '_' . $allias;

		# --- переиспользование ---
		if ( $this->filler === null ) {
			if ( ! is_file( $path ) ) return false;
			if ( ! include_once $path ) return false;
			if ( ! class_exists( $class ) ) return false;

			$this->filler = new $class( $this );
		}

		# --- runtime-выключатель ---
		if ( method_exists( $this->filler , 'is_enabled' ) && ! $this->filler->is_enabled( ) ) return false;

		# --- применяем ---
		if ( method_exists( $this->filler , 'data' ) ) $this->filler->data( );

		return true;
	}

	# --- assemble: собирает дерево с нуля каждый раз ---
	protected function assemble ( ) : void {
		$root = new \visuotag( $this->visuorelease );

		$parent = $root;

		if ( $this->wrapper_tag !== '' ) {
			$wrapper = $root->tag( $this->wrapper_tag );

			foreach ( $this->wrapper_attrs as $key => $val ) {
				$wrapper->attrs_add( $key , (string) $val );
			}

			$parent = $wrapper;
		}

		foreach ( $this->sections as $vt ) {
			$parent->append( $vt );
		}

		$this->visuotag = $root;
		$this->dirty    = false;
	}

	# --- sections API ---
	public function section_add ( string $name , \visuotag $vt ) : static {
		$this->sections[ $name ] = $vt;
		$this->dirty = true;
		return $this;
	}

	public function section_get ( string $name ) : null|\visuotag {
		return $this->sections[ $name ] ?? null;
	}

	public function section_has ( string $name ) : bool {
		return isset( $this->sections[ $name ] );
	}

	public function section_remove ( string $name ) : static {
		unset( $this->sections[ $name ] );
		$this->dirty = true;
		return $this;
	}

	public function section_names ( ) : array {
		return array_keys( $this->sections );
	}

	public function sections_clear ( ) : static {
		$this->sections = [ ];
		$this->dirty = true;
		return $this;
	}

	# --- wrapper API ---
	public function wrapper_set ( string $tag , array $attrs = [ ] ) : static {
		$this->wrapper_tag   = $tag;
		$this->wrapper_attrs = $attrs;
		$this->dirty = true;
		return $this;
	}

	public function wrapper_clear ( ) : static {
		$this->wrapper_tag   = '';
		$this->wrapper_attrs = [ ];
		$this->dirty = true;
		return $this;
	}

	# --- switchers ---
	public function show ( ) : static {
		$this->enabled = true;
		return $this;
	}

	public function hide ( ) : static {
		$this->enabled = false;
		return $this;
	}

	public function set_enabled ( bool $enabled = true ) : static {
		$this->enabled = $enabled;
		return $this;
	}

	public function set_filler_enabled ( bool $enabled = true ) : static {
		$this->filler_enabled = $enabled;
		return $this;
	}

	# --- rebuild ---
	public function rebuild ( ) : void {
		$this->menu?->clear( );
		# $this->filler = null;      ← НЕ сбрасываем — сохраняем решение runtime
		$this->wrapper_tag   = '';
		$this->wrapper_attrs = [ ];
		$this->sections      = [ ];
		$this->visuotag      = new \visuotag( $this->visuorelease );
		$this->algoritm( );
	}

	# --- accessors (с ленивой пересборкой) ---
	public function as_tag ( ) : \visuotag {
		if ( $this->enabled === false ) {
			return ( new \visuotag( $this->visuorelease ) )->root( );
		}
		if ( $this->dirty ) $this->assemble( );
		return $this->visuotag->root( );
	}

	public function visuotag ( ) : \visuotag {
		if ( $this->enabled === false ) {
			return new \visuotag( $this->visuorelease );
		}
		if ( $this->dirty ) $this->assemble( );
		return $this->visuotag;
	}
}
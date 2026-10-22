<?PHP

class algoritm_ {
	# --- core references ---
	public main $main;
	public array $args = [ ];
	public null|algoritmdata_ $algoritmdata = null;

	# --- page settings (override in child) ---
	public bool $logged = false;

	public function __construct ( main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritmdata = new algoritmdata_( );
		$this->load_data( );
	}

	public function args ( array $args ) : void {
		$this->args = $args;
	}

	protected function load_vars ( ) : void {
		$rc   = new \ReflectionClass( $this );
		$file = dirname( $rc->getFileName( ) ) . DIRECTORY_SEPARATOR
		      . $rc->getShortName( ) . '.vars.php';

		if ( is_file( $file ) ) {
			include $file;
		}
	}

	protected function load_data ( ) : void {
		$rc    = new \ReflectionClass( $this );
		$dir   = dirname( $rc->getFileName( ) );
		$short = $rc->getShortName( );

		$file = $dir . DIRECTORY_SEPARATOR . $short . '.data.json';
		if ( ! is_file( $file ) ) return;

		$raw = @file_get_contents( $file );
		if ( $raw === false ) return;

		$data = @json_decode( $raw , true );
		if ( ! is_array( $data ) ) return;

		$vars = $data[ 'default' ] ?? [ ];

		foreach ( $vars as $k => $v ) {
			if ( $v === null ) continue;
			if ( property_exists( $this , $k ) ) {
				$this->{$k} = $v;
			}
		}
	}

	public function render_json_node ( array $node ) : mixed {
		if ( ! empty( $node[ 'el' ] ) ) {
			$el_type = strtolower( $node[ 'el' ] );
			$class = '\\visuoel_' . $el_type;
			if ( ! class_exists( $class ) ) {
				$class = '\\visuoel';
			}

			try {
				$el = new $class();
				if ( ! empty( $node[ 'data' ] ) && method_exists( $el, 'data' ) ) {
					$el->data()->set( ...(array)$node[ 'data' ] );
				}
				return $el;
			} catch ( Throwable $e ) {
				return null;
			}
		}

		$tag     = $node[ 'tag' ]     ?? 'div';
		$class   = $node[ 'class' ]   ?? '';
		$id      = $node[ 'id' ]      ?? '';
		$content = $node[ 'content' ] ?? '';
		$href    = $node[ 'href' ]    ?? '';
		
		$vt = new \visuotag( );
		$el = $vt->tag( $tag , class : $class , id : $id );
		
		if ( $content !== '' ) {
			$el->content( $content );
		}
		
		if ( !empty( $node[ 'children' ] ) && is_array( $node[ 'children' ] ) ) {
			foreach ( $node[ 'children' ] as $child ) {
				$res = $this->render_json_node( $child );
				if ( $res instanceof \visuotag ) {
					$el->append( $res );
				} elseif ( $res instanceof \visuoel ) {
					$el->el( $res );
				}
			}
		}
		
		return $vt;
	}

	public function is_ajax ()    : bool { return (bool) $this->main->render->getdata( 'render_ajax' ); }
	public function is_guest ()   : bool { return $this->main->is_guest( ); }
	public function is_post ()    : bool { return ! empty( $_POST ); }

	public function default () {
		if ( $this->is_ajax() ) return $this->ajax();
		return $this->role();
	}
	public function ajax () {
		if ( $this->is_guest() ) return $this->ajax_guest();
		return $this->ajax_logged();
	}
	public function role () {
		if ( $this->is_guest() ) return $this->guest();
		return $this->logged();
	}

	public function algoritm ()    { }
	public function guest ()       { return false; }
	public function logged ()      { return false; }
	public function ajax_guest ()  { return false; }
	public function ajax_logged () { return false; }

	public function clear ( ) { }
	public function start ( ) { }
	public function debug ( ) { }
}
<?PHP

class render {
	# --- refs ---
	public null|\main $main = null;
	public null|render__data $render__data = null;

	# --- methods ---
	public function __construct ( null|\main $main = null , null|render__data $render__data = null ) {
		$this->main = $main;
		$this->render__data = $render__data ?? new render__data( $this );
	}

	public function init ( ) : void {
		# no-op, оставлен для обратной совместимости
	}

	public function data ( ) : render__data {
		return $this->render__data;
	}

	public function getdata ( string $name ) : mixed {
		return $this->render__data->get( $name );
	}

	public function setdata ( string $varname , mixed $varvalue ) : void {
		$this->render__data->set( $varname , $varvalue );
	}

	public function release ( bool $log = false , bool $debug = false ) : void {
		if ( $this->main === null ) return;
		if ( ! isset( $this->main->visuorelease ) ) return;

		$ajax = $this->render__data->render_ajax;

		if ( $ajax ) {
			$this->main->visuorelease->final_ajax( $log , $debug );
		} else {
			$this->main->visuorelease->final( $log , $debug );
		}
	}
}

class render__data {
	# --- refs ---
	public render $render;

	# --- state ---
	public bool $render_ajax = false;
	public bool $min         = false;   # используется visuorelease::final/final_ajax

	# --- methods ---
	public function __construct ( render $render ) {
		$this->render = $render;
	}

	public function init ( bool $render_ajax ) : void {
		$this->render_ajax = $render_ajax;
	}

	public function set ( string $varname , mixed $varvalue ) : void {
		if ( ! property_exists( $this , $varname ) ) return;
		$this->{$varname} = $varvalue;
	}

	public function get ( string $varname ) : mixed {
		if ( ! property_exists( $this , $varname ) ) return null;
		return $this->{$varname};
	}
}
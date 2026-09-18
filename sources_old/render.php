<?PHP

# 
# 
# 
# 

class render {
	# vars
	public main $main;
	public render__data $render__data;

	# methods
	public function __construct ( main $main , null|render__data $render__data = null ) {
		$this->main = $main;
		$this->render__data = $render__data ? : new render__data( $this );
	}

	# init
	public function init ( ) {
	}
	public function data ( ) {
		return $this->render__data;
	}

	public function getdata ( string $name ) {
		return $this->render__data->get( $name ) ?: null;
	}
	public function setdata ( string $varname , mixed $varvalue ) {
		$this->render__data->set( $varname , $varvalue );
	}

	public function release ( bool $log = false , bool $debug = false ) {
		$data = $this->render__data;
		$ajax = $data->render_ajax;

		if( $ajax ) {
			$this->main->visuorelease->final_ajax( $log , $debug );
		} else {
			$this->main->visuorelease->final( $log , $debug );
		}
	}
}

class render__data {
	# vars
	public render $render;
	public bool $render_ajax = false;
	public bool $render_min = false;
	public function __construct ( render $render ) {
		$this->render = $render;
	}
	public function init ( bool $render_ajax ) {
		$this->render_ajax = $render_ajax;
	}
	public function set ( string $varname , mixed $varvalue) {
		$this->{$varname} = $varvalue;
	}

	public function get( string $varname ){
		return $this->{$varname} ?: null;
	}
}
?>
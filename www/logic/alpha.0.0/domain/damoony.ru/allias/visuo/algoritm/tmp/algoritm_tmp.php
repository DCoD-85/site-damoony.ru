<?PHP

class algoritm_tmp {
	# vars
	public algoritm $algoritm;
	public visuotag $vt;
	public array $args = [ ];
	public string $path_object;
	# public array $available = [ ];
	# methods
	public function __construct ( algoritm $algoritm , array $args = [ ] ) {
		$this->algoritm = $algoritm;
		$this->args = $args;
	}
	
	public function args ( array $args ) {
		$this->args = $args;
	}
	
	public function algoritm ( ) {
	}

	public function release ( ) {
		$this->available = $this->available ?: [ 'pagelogo' , 'index' , 'documents' , 'policy' ];
		$this->search = false;
		$this->default = true;

		print 'OK';
		$this->algoritm->release_( $this );
	}
	
	public function default ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		$center = $main->alliasglobal->center;
		
		$headers = $main->alliasglobal->headers;
		$scheme = $main->alliasglobal->scheme;
		
	}
	
}

?>
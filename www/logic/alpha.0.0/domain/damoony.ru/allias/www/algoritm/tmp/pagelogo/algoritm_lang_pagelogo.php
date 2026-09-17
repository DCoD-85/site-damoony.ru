<?PHP

class algoritm_lang_pagelogo {
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
		$this->available = [ ];
		$this->search = false;
		$this->default = true;

		$this->algoritm->release_( $this );
	}
	
	public function default ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		$lang = $main->language;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		$left = $main->alliasglobal->left;
		$center = $main->alliasglobal->center;
		$right = $main->alliasglobal->right;
		$left->showasmenu( false );
		$right->showasmenu( false );
		
		$headers = $main->alliasglobal->headers;
		$scheme = $main->alliasscheme;

		header( 'Content-Type: image/png' );
		$path = 'D:\OSPanel543\domains\damoony2.ru\www\\';
		$path .= 'mediaoxydog\avatars\1\fon\1.png';
		readfile( $path );
		die( $path );
	}
	
}

?>
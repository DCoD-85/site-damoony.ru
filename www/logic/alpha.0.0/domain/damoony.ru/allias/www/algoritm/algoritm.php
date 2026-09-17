<?PHP

namespace damoony\www;

class algoritm
	extends \algoritm_ {
	# vars
	public \main $main;
	public \algoritmlogic $logic;
	
	# path and classes
	public string $basepath = __DIR__ . DS;
	public string $baseclass = 'algoritm';
	public string $errorpath = __DIR__ . DS;
	public string $errorclass = 'algoritm_error';
	public string $authpath = __DIR__ . DS;
	public string $authclass = 'algoritm_auth';

	# methods
	public function __construct( \main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritmdata = new \algoritmdata_( );
	}
	
	public function start ( ) {
		$main = $this->main;
		
		$headers = $main->alliasglobal->headers;
		$scheme = $main->alliasscheme;

		$scheme->load( 'www' );

		$this->logic( );
		$this->release( );
		
		$scheme->reload( );
	}
	
	public function logic ( ) {
		$main = $this->main;
	}
	
	public function release ( ) {
		$this->algoritmdata->set( [ '([a-z]{2}-[a-z]{2})|lang' , 'tmp' ] , false , true , true );
		$this->release_( $this );
	}
	
	public function default ( ) {
		$main = $this->main;
		$visuorelease = $main->visuorelease;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
	}
}

?>
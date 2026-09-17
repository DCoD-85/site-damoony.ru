<?PHP

namespace damoony\login;

class algoritm
	extends \algoritm_ {
	# vars
	public \main $main;
	public \algoritmlogic $logic;
	
	# path and classes
	public array $available = [ 'index' , '([a-z]{2}-[a-z]{2})|lang' ];
	public bool $search = true;
	public bool $default = false;
	public bool $auth = false;
	public array $algoritm_classes = [ ];
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

		$scheme->load( 'login' );

		$this->logic( );
		$this->release( );
		
		$scheme->reload( );
	}
	
	public function logic ( ) {
		$main = $this->main;
		
		$library = $main->library;
		$vars = $library->vars;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		$center = $main->alliasglobal->center;
		
		

		$access_origin = '*';
		# $access_origin = 'https://www.damoony2.ru';
		# $access_origin = $link->href( [ 'login' , $url_allias[ 0 ] ] , false , [ ] );
		# unset( $access_origin[ strlen( $access_origin ) - 1 ] );
		$access_origin = substr( $_SERVER[ 'HTTP_REFERER' ] , 0 , strlen( $_SERVER[ 'HTTP_REFERER' ] ) - 1 );
		header( 'Access-Control-Allow-Origin: ' . $access_origin );
		# header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE' );
		header( 'Access-Control-Allow-Credentials: true' );
		header( 'Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Accept, X-PINGOTHER, Content-Type' );
		# header( 'Content-Type: application/json' );
		# header( 'sec-fetch-site: same-origin' );
	}
	
	public function release ( ) {
		$this->algoritmdata->set( [ '([a-z]{2}-[a-z]{2})|lang' ] , false , true , true );
		
		# 
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
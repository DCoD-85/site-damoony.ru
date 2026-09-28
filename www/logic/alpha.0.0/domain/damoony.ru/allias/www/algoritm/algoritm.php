<?PHP

namespace damoony\www;

class algoritm
	extends \algoritm_ {

	# path and classes
	public array $available = [ 'index' , '([a-z]{2}-[a-z]{2})|lang' , 'tests' ];
	public string $basepath  = __DIR__ . DS;
	public string $baseclass = 'algoritm';
	public string $errorpath = __DIR__ . DS;
	public string $errorclass = 'algoritm_error';
	public string $authpath  = __DIR__ . DS;
	public string $authclass = 'algoritm_auth';

	# ---------------------------------------------------------------
	#   CONSTRUCTOR — переопределён, чтобы не падал на $this->algoritm
	#   (в момент new $main->algoritm ещё null)
	# ---------------------------------------------------------------
	public function __construct ( \main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritmdata = new \algoritmdata_( );
	}

	# ---------------------------------------------------------------
	#   START — вызывается из main::logic() -> algoritm->start()
	# ---------------------------------------------------------------
	public function start ( ) {
		$main = $this->main;

		$headers = $main->alliasglobal->headers;
		$scheme = $main->alliasscheme;

		$scheme->load( 'www' );

		$this->logic( );
		$this->release( );

		$scheme->reload( );
	}

	# ---------------------------------------------------------------
	#   LOGIC — CORS-заголовки
	# ---------------------------------------------------------------
	public function logic ( ) {
		$main = $this->main;

		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		$center = $main->alliasglobal->center;

		$access_origin = '*';
		if ( ! empty( $_SERVER[ 'HTTP_REFERER' ] ) ) {
			$access_origin = substr( $_SERVER[ 'HTTP_REFERER' ] , 0 , strlen( $_SERVER[ 'HTTP_REFERER' ] ) - 1 );
		}
		header( 'Access-Control-Allow-Origin: ' . $access_origin );
		header( 'Access-Control-Allow-Credentials: true' );
		header( 'Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Accept, X-PINGOTHER, Content-Type' );
	}

	# ---------------------------------------------------------------
	#   RELEASE — свой набор данных для algoritmdata
	# ---------------------------------------------------------------
	public function release ( ) {
		$this->algoritmdata->set( [ '([a-z]{2}-[a-z]{2})|lang' , 'tests' ] , false , true , true );

		$this->release_( $this );
	}

	# ---------------------------------------------------------------
	#   DEFAULT — стаб
	# ---------------------------------------------------------------
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
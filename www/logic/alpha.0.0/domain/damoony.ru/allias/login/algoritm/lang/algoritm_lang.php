<?PHP

namespace damoony\login;

class algoritm_lang
	extends \algoritm_ {
	# vars
	public null|\algoritmdata_ $algoritmdata = null;
	public \visuotag $vt;
	public array $args = [ ];
	
	# methods
	public function __construct( \main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritm = $main->algoritm;
		$this->algoritmdata = new \algoritmdata_( );
	}
	
	public function args ( array $args ) {
		$this->args = $args;
	}
	
	public function algoritm ( ) {
	}

	public function release ( ) {		
		$main = $this->algoritm->main;
		if( $main->user->is_guest( ) ){
			$this->algoritmdata->set( );
		} else {
			$this->algoritmdata->set( );
		}

		$this->algoritmdata->set( [ '' , 'index' , 'form' , 'login' , 'logout' , 'redirect' , 'profile' , 'agreement' , 'registration' ] , false , true , $main->library->url->static_base[ 1 ] == '' ? true : false );
		
		# release
		$this->release_( $this );

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
		$scheme = $main->alliasscheme;
		
		$href = $link->href( $url_domain , [ $url_base[ 0 ] , 'form' ] , $url_vars );
		http_response_code( 307 );
		header( 'Location: ' . $href );
	}
	
}

?>
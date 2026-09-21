<?PHP

namespace damoony\www;

class algoritm_lang
	extends \algoritm_ {
	
	# --- page settings ---
	public array $available = [ '' , 'index' , 'form' , 'login' , 'logout' , 'redirect' , 'profile' , 'agreement' , 'registration' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	
	# --- release: dynamic default, so custom set ---
	public function release ( ) {
		$default = $this->main->library->url->static_base[ 1 ] == '' ? true : false;
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $default );
		$this->release_( $this );
	}
	
	# --- default: redirect to form ---
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
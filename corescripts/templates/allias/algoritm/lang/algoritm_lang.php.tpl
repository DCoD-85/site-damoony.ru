<?PHP

namespace <domain_ns>\<name>;

class algoritm_lang
	extends \algoritm_ {

	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	public function release ( ) {
		$this->load_vars( );

		$default = ( $this->main->library->url->static_base[ 1 ] ?? '' ) === '';
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $default );
		$this->release_( $this );
	}

	public function default ( ) {
		$main     = $this->main;
		$link     = $main->library->link( );
		$url      = $main->library->url( );
		$url_base = $url->static_base;

		$href = $link->href( false , [ $url_base[ 0 ] , 'index' ] , use_all_adress_data : false );

		http_response_code( 307 );
		header( 'Location: ' . $href );
		exit;
	}
}
<?PHP

namespace hert_online\test;

class algoritm_lang
	extends \algoritm_ {

	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	

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
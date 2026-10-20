<?PHP

namespace damoony_ru\www;

class algoritm_lang
	extends \algoritm_ {
	

	public function default ( ) {
		$main = $this->main;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$href = $link->href( $url_domain , [ $url_base[ 0 ] , 'form' ] , $url_vars );
		http_response_code( 307 );
		header( 'Location: ' . $href );
	}
}
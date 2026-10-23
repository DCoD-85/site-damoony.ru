<?PHP

namespace damoony_ru\oxydog;

class algoritm_lang extends \algoritm_ {
	public function guest  ( ) { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		$main = $this->main;
		$url = $main->library->url( );
		$domain = implode( '.', $url->static_domain );
		$lang = $url->static_base[ 0 ] ?? 'ru-ru';
		$scheme = $main->main_settings->get( 'connect_type' ) ?? 'https://';
		
		header( 'Location: ' . $scheme . $domain . '/' . $lang . '/index', true, 307 );
		exit;
	}
}
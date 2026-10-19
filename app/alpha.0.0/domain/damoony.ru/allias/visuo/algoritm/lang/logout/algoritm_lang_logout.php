<?PHP

namespace damoony_ru\visuo;

class algoritm_lang_logout
	extends \algoritm_ {

	# ---------------------------------------------------------------
	public function release ( ) {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->redirect_home( ); }
	public function logged ( ) { return $this->do_logout( ); }

	# --- AJAX ---
	public function ajax_guest  ( ) { return $this->redirect_home( ); }
	public function ajax_logged ( ) { return $this->do_logout( ); }

	# ---------------------------------------------------------------
	private function do_logout ( ) {
		$main = $this->main;
		$main->user->logout( );

		// после logout — редирект на главную
		$library  = $main->library;
		$link     = $library->link( );
		$url      = $library->url( );
		$url_base = $url->static_base;

		$href = $link->href( false , [ $url_base[ 0 ] , 'index' ] , use_all_adress_data : false );
		redirect( $href );
	}

	# ---------------------------------------------------------------
	private function redirect_home ( ) {
		$main = $this->main;
		$library  = $main->library;
		$link     = $library->link( );
		$url      = $library->url( );
		$url_base = $url->static_base;

		$href = $link->href( false , [ $url_base[ 0 ] , 'index' ] , use_all_adress_data : false );
		redirect( $href );
	}
}
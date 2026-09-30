<?PHP

namespace damoony\visuo;

class algoritm_lang_logout
	extends \algoritm_ {

	public array $available = [ 'logout' ];
	public bool  $auth      = false;
	public bool  $search    = false;
	public bool  $default   = true;

	# ---------------------------------------------------------------
	public function release ( ) {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->redirect_home( ); }
	public function logged ( ) { return $this->do_logout( ); }

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
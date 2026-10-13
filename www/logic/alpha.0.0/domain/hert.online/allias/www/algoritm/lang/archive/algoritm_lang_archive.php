<?PHP

namespace hert_online\www;

class algoritm_lang_lang_archive
	extends \algoritm_ {

	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	public function release ( ) {
		$this->load_vars( );
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		$main = $this->main;
		$lang = $main->language;

		$main->alliasglobal->head->title(
			$lang->get( 'LANG_ARCHIVE_TITLE' , [ ] , $main->main_settings->title( ) ) . ' — ' . $lang->get( 'PAGE_HOME' , [ ] , 'Главная' )
		);

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'visuo visuo-lang_archive' )
			->tag( 'h1' )->content( $lang->get( 'LANG_ARCHIVE_TITLE' , [ ] , 'lang_archive' ) );

		$main->alliasglobal->center->visuotag( )->append( $vt );

		return true;
	}
}
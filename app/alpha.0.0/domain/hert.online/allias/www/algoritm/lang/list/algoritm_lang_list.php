<?PHP

namespace hert_online\www;

class algoritm_lang_lang_list
	extends \algoritm_ {

	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	

	public function guest  ( ) { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		$main = $this->main;
		$lang = $main->language;

		$main->alliasglobal->head->title(
			$lang->get( 'LANG_LIST_TITLE' , [ ] , $main->main_settings->title( ) ) . ' — ' . $lang->get( 'PAGE_HOME' , [ ] , 'Главная' )
		);

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'visuo visuo-lang_list' )
			->tag( 'h1' )->content( $lang->get( 'LANG_LIST_TITLE' , [ ] , 'lang_list' ) );

		$main->alliasglobal->center->visuotag( )->append( $vt );

		return true;
	}
}
<?PHP

namespace damoony_ru\www;

class algoritm_lang_index
	extends \algoritm_ {

	public function guest  ( ) { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		$main = $this->main;
		$lang = $main->language;
		
		$main->alliasglobal->head->title(
			$lang->get( 'VISUO_TITLE' , [ ] , 'visuo' ) . ' — ' . $lang->get( 'PAGE_HOME' , [ ] , 'Главная' )
		);

		

		return true;
	
		$el = new \\visuoel_table();
		$el->data()->set( "aeeaea" );
		$this->main->alliasglobal->center->visuotag()->el( $el );
	}

	public function algoritm ( ) {
	
}

	public function default ( ) {
print '222';
	return parent::default();
}

}
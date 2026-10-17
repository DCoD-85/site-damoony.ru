<?PHP

namespace damoony\visuo;

class algoritm_lang_index
	extends \algoritm_ {

	public function release ( ) {
		$default = $this->is_default( );
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		$main = $this->main;
		$lang = $main->language;
		
		$main->alliasglobal->head->title(
			$lang->get( 'VISUO_TITLE' , [ ] , 'visuo' ) . ' — ' . $lang->get( 'PAGE_HOME' , [ ] , 'Главная' )
		);

		$vt = new \visuotag( );

		$vt->tag( 'div' , class : 'visuo visuo-index' )
			->tag( 'h1' )->content( $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' ) )->parent( )
			->tag( 'p'  )->content( $lang->get( 'VISUO_INTRO' , [ ] , '' ) )->parent( )
			->tag( 'p'  )->content( $lang->get( 'VISUO_TAGLINE' , [ ] , '' ) )->parent( )
			->content( time( ) );

		$center = $main->alliasglobal->center;
		$center->wrapper_set( 'div' );
		$center->section_add( 'test' , $vt ); // ->visuotag( )->append( $vt );
		$center->wrapper_set( 'div' );
		$center->section_add( 'test' , $vt ); // ->visuotag( )->append( $vt );
		$center->wrapper_set( 'div' );
		$center->section_add( 'test3' , $vt ); // ->visuotag( )->append( $vt );

		return true;
	}
}
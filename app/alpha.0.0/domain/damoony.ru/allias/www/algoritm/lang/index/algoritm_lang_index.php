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

		$vt = new \visuotag( );

		$vt->tag( 'div' , class : 'visuo visuo-index' )
			->tag( 'h1' )->content( $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' ) )->parent( )
			->tag( 'p'  )->content( $lang->get( 'VISUO_INTRO' , [ ] , '' ) )->parent( )
			->tag( 'p'  )->content( $lang->get( 'VISUO_TAGLINE' , [ ] , '' ) )->parent( )
			->content( time( ) );

		$table = new \visuoel();
		$table = $table->table( )->table( );
		$table->html_mode(true);
		$head = $table->head();
		$body = $table->body();
		$row = $head->row();
		$row->cell()->set('Заголовок 1');
		$row->cell()->set('Заголовок 2');
		$row = $body->row();
		$row->cell()->set('Значение 1');
		$row->cell()->set('Значение 2');
		$center = $main->alliasglobal->center;

		$vt = new \visuotag( );
		$vt->el( $table );
		
		$center->wrapper_set( 'div' );
		$center->section_add( 'test' , $vt );
	
		$table = new \visuoel();
		$table = $table->table()->table();
		$table->html_mode(true);
		$head = $table->head();
		$body = $table->body();
		$row = $head->row();
		$row->cell()->set('Заголовок 1');
		$row->cell()->set('Заголовок 2');
		$row = $body->row();
		$row->cell()->set('Значение 11');
		$row->cell()->set('Значение 22');
		$center = $main->alliasglobal->center;

		$vt = new \visuotag();
		$vt->el( $table );

		$center->wrapper_set( 'div' );
		$center->section_add( 'test_1792547874098' , $vt );


		return true;
	
	}
}
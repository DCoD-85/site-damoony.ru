<?PHP

namespace hert_online\www;

class algoritm_lang_index
	extends \algoritm_ {

	

	public function guest ( )  { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		$main = $this->main;
		$lang = $main->language;

		$main->alliasglobal->head->title(
			$lang->get( 'WWW_TITLE' , [ ] , $main->main_settings->title( ) ) . ' — ' . $lang->get( 'PAGE_HOME' , [ ] , 'Главная' )
		);

		$center = $main->alliasglobal->center;

		# --- POST: JSON min ---
		if ( $this->is_post( ) ) {
			$struct = $main->visuostruct->min( );
			$struct->error( )->set( false );

			$name = trim( (string) ( $_POST[ 'name' ] ?? '' ) );

			$messages = $main->messages;
			$messages->add( 'success' , 'OK' , 'Hello, ' . htmlspecialchars( $name , ENT_QUOTES ) . '!' );
			$struct->messages( )->set( $messages->as_tag( ) );

			return true;
		}

		# --- GET: HTML через visuoel ---
		$ve = new \visuoel( );

		$form = $ve->form( )->form( );
		$form->data( )->set(
			'test_form' ,
			'post' ,
			'' ,
			form_jquery : true
		);

		$field_name = new \visuoel_field_text( );
		$field_name->data( )->set( 'name' )->placeholder( 'Ваше имя' )->label( 'Имя' );
		$form->add( $field_name );

		$btn = new \visuoel_field_submit( );
		$btn->data( )->text( 'Отправить' );
		$form->add( $btn );

		$vt = new \visuotag( );
		$vt->tag( 'h1' )->content( 'Эталонный аллиас ' . $main->main_settings->title( ) );
		$vt->tag( 'p' )->content( 'Одна страница — lang/index. Форма ниже — пример работы с visuoel.' );
		$vt->el( $ve );

		$center->visuotag( )->append( $vt );

		return true;
	}
}
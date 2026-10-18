<?PHP

namespace damoony_ru\visuo;

class algoritm_profile_user
	extends \algoritm_ {

	public function release ( ) {
		// Эмулируем установку данных для схемы
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		die('DEBUG: PROFILE RENDER CALLED'); // <--- ВРЕМЕННО
		$main = $this->main;
		
		// ПОЛУЧАЕМ ПАРАМЕТР ИЗ URL
		$login = $this->args['login'] ?? 'Неизвестно';
		$lang  = $this->args['lang'] ?? 'ru-ru';

		$vt = new \visuotag( );

		$vt->tag( 'div' , class : 'visuo visuo-profile' )
			->tag( 'h1' )->content( 'Профиль пользователя: ' . $login )->parent( )
			->tag( 'p'  )->content( 'Язык интерфейса: ' . $lang )->parent( );

		$center = $main->alliasglobal->center;
		$center->wrapper_set( 'div' );
		$center->section_add( 'test' , $vt );

		return true;
	}
}
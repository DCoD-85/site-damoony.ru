<?PHP

namespace damoony;

# ---------------------------------------------------------------
#   alliasglobal_left_visuo — основное меню аллиаса visuo.
#   Только навигация по разделам. Контекст — в right_visuo.
# ---------------------------------------------------------------

class alliasglobal_left_visuo {

	# --- refs ---
	public \main $main;
	public alliasglobal_left $left;

	# --- methods ---
	public function __construct ( alliasglobal_left $left ) {
		$this->main = $left->main;
		$this->left = $left;
		$this->data( );
	}

	public function data ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base     = $url->static_base;
		$current_page = (string) ( $url_base[ 1 ] ?? '' );

		# --- брендинг ---
		$this->left->menu->text(
			'nav' ,
			$lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		# --- основные разделы ---
		$this->left->menu->link(
			'nav' ,
			$lang->get( 'MENU_HOME' , [ ] , 'Главная' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'index' ] , use_all_adress_data : false ) ,
			$current_page === 'index' || $current_page === ''
		);
		$this->left->menu->link(
			'nav' ,
			$lang->get( 'MENU_DOCS' , [ ] , 'Документация' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'docs' ] , use_all_adress_data : false ) ,
			$current_page === 'docs'
		);
		$this->left->menu->link(
			'nav' ,
			$lang->get( 'MENU_NEWS' , [ ] , 'Новости' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'news' ] , use_all_adress_data : false ) ,
			$current_page === 'news'
		);
		$this->left->menu->link(
			'nav' ,
			$lang->get( 'MENU_DOWNLOADS' , [ ] , 'Загрузки' ) ,
			$link->href( false , [ $url_base[ 0 ] , 'downloads' ] , use_all_adress_data : false ) ,
			$current_page === 'downloads'
		);

		# --- гость / авторизованный ---
		if ( $main->user->is_guest( ) ) {
			$this->left->menu->link(
				'nav' ,
				$lang->get( 'MENU_LOGIN' , [ ] , 'Вход' ) ,
				$link->href( false , [ $url_base[ 0 ] , 'login' ] , use_all_adress_data : false ) ,
				$current_page === 'login'
			);
			$this->left->menu->link(
				'nav' ,
				$lang->get( 'MENU_REGISTRATION' , [ ] , 'Регистрация' ) ,
				$link->href( false , [ $url_base[ 0 ] , 'registration' ] , use_all_adress_data : false ) ,
				$current_page === 'registration'
			);
		} else {
			$this->left->menu->link(
				'nav' ,
				$lang->get( 'MENU_PROFILE' , [ ] , 'Профиль' ) ,
				$link->href( false , [ $url_base[ 0 ] , 'profile' ] , use_all_adress_data : false ) ,
				$current_page === 'profile'
			);
			$this->left->menu->link(
				'nav' ,
				$lang->get( 'MENU_LOGOUT' , [ ] , 'Выход' ) ,
				$link->href( false , [ $url_base[ 0 ] , 'logout' ] , use_all_adress_data : false ) ,
				false
			);
		}
	}
}
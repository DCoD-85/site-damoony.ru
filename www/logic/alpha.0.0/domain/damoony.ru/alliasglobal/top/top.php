<?PHP

namespace damoony;

class alliasglobal_top extends alliasblock {
	public string $alliasglobal_name = 'top';

	# --- custom ---
	public string $top_sitename  = 'DAMὸὸNY<span>На страже вашей стабильности</span>';
	public string $top_siteabout = '&nbsp;';

	# --- data: только подключение ---
	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'top.data.php';
		if ( is_file( $file ) ) include $file;
	}

	# ===============================================================
	#   СТРУКТУРА
	# ===============================================================

	private function make_tophead ( ) : \visuotag {
		$menu = $this->menu;

		$vt   = new \visuotag( );
		$head = $vt->tag( 'div' , class : 'tophead' );

		$head->tag( 'div' , class : 'topheaddomain' )->append( $menu->as_list( 'projects' ) );
		$head->tag( 'div' , class : 'topheadauth'   )->append( $menu->as_list( 'login' ) );
		$head->tag( 'div' , class : 'topheadskin'   )->append( $menu->as_list( 'skins' ) );
		$head->tag( 'div' , class : 'topheadlang'   )->append( $menu->as_list( 'languages' ) );

		return $vt->root( );
	}

	private function make_toppro ( ) : \visuotag {
		$menu = $this->menu;
		$main = $this->main;
		$lib  = $main->library;
		$link = $lib->link( );
		$url  = $lib->url( );

		$vt  = new \visuotag( );
		$pro = $vt->tag( 'div' , class : 'toppro' );

		$pro->tag( 'div' , class : 'topproname' )
			->content( $this->top_sitename );

		$logo = $pro->tag( 'div' , class : 'topprologo' );

		$logo->tag( 'div' , class : 'topprologopro' );

		$logo->tag( 'div' , class : 'toppronav' )
			->append( $menu->as_list( 'navigate' ) );

		return $vt->root( );
	}

	private function make_toplinks ( ) : \visuotag {
		$menu = $this->menu;

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'toplinks' )
			->append( $menu->as_list( 'links' ) );

		return $vt->root( );
	}

	# ===============================================================
	#   НАПОЛНЕНИЕ МЕНЮ
	# ===============================================================

	private function fill_projects ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;

		$cur_proj = $url->static_allias[ 1 ] ?? '';
		$damoony  = $lang->get( 'TOP_PROJECTS_DAMOONY' , [ ] , 'Damoony' );

		$cur_com  = $lang->get( 'TOP_PROJECTS_DAMOONY_COMMENT' , [
			$link->href( [ 'www'      , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
			$link->href( [ 'login'    , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
			$link->href( [ 'football' , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
			$link->href( [ 'visuo'    , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
			$link->href( [ 'oxydog'   , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
		] , '' );

		$dam_com  = $lang->get( 'TOP_PROJECTS_DOMAIN_COMMENT' , [
			$link->href( [ 'www'    , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
			$link->href( [ 'www'    , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
			$link->href( [ 'www'    , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
			$link->href( [ 'oxydog' , $url_allias[ 0 ] ] , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ,
		] , '' );

		$this->menu->link( 'projects' , $cur_proj , 'javascript:void(0)' , ( $url_base[ 1 ] ?? '' ) === 'main' , false , $cur_com );
		$this->menu->link( 'projects' , $damoony  , 'javascript:void(0)' , ( $url_base[ 1 ] ?? '' ) === 'news' , false , $dam_com );
	}

	private function fill_login ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;

		if ( $main->user->is_guest( ) === true ) {
			$login = $lang->get( 'TOP_LOGIN_LOGIN'        , [ ] , 'Авторизация' );
			$reg   = $lang->get( 'TOP_LOGIN_REGISTRATION' , [ ] , 'Регистрация' );
			$help  = $lang->get( 'TOP_LOGIN_HELP'         , [ ] , 'Помощь'      );

			$this->menu->link( 'login' , $login ,
				$link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , [ 'login' ] , use_all_adress_data : false ) ,
				false , false , '' , true , false );

			$this->menu->link( 'login' , $reg ,
				$link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , [ 'registration' ] , use_all_adress_data : false ) ,
				false , false , '' , true , false );

			$this->menu->link( 'login' , $help ,
				$link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , [ $url_base[ 0 ] , 'help' ] , use_all_adress_data : false ) ,
				false , false , '' , true , false );

			return;
		}

		$items = [
			[ 'messages'      , 'Сообщения' , 'TOP_MENU_MESSAGES' ] ,
			[ 'tasks'         , 'Задачи'    , 'TOP_MENU_TASKS'    ] ,
			[ 'notifications' , 'Новости'   , 'TOP_MENU_NEWS'     ] ,
			[ 'friends'       , 'Друзья'    , 'TOP_MENU_FRIENDS'  ] ,
			[ 'profile'       , 'Профиль'   , 'TOP_MENU_PROFILE'  ] ,
			[ 'logout'        , 'Выход'     , 'TOP_MENU_LOGOUT'   ] ,
		];

		foreach ( $items as $it ) {
			[ $slug , $default , $key ] = $it;

			$title = $lang->get( $key , [ ] , $default );

			$row = $this->menu->link(
				'login' ,
				$title ,
				$link->href( false , [ $url_base[ 0 ] , $slug ] , use_all_adress_data : false )
			);

			$row->icon( $slug );
		}
	}

	private function fill_skins ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_domain = $url->static_domain;
		$url_base   = $url->static_base;

		$light = $lang->get( 'TOP_SKINS_LIGHT' , [ ] , 'Светлый' );
		$dark  = $lang->get( 'TOP_SKINS_DARK'  , [ ] , 'Темный'  );

		$current = $_COOKIE[ 'skin' ] ?? 'default';

		$this->menu->link( 'skins' , $light , $link->href( $url_domain , $url_base , [ 'skin'=>'default' ] , use_all_adress_data : false ) , $current === 'default' );
		$this->menu->link( 'skins' , $dark  , $link->href( $url_domain , $url_base , [ 'skin'=>'dark'    ] , use_all_adress_data : false ) , $current === 'dark' );
	}

	private function fill_languages ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;

		$url_ru = $url_base; $url_ru[ 0 ] = 'ru-ru';
		$url_en = $url_base; $url_en[ 0 ] = 'en-en';
		$url_ch = $url_base; $url_ch[ 0 ] = 'ch-ch';

		$rus = $lang->get( 'TOP_LANGUAGES_RUS'   , [ ] , 'Русский'    );
		$eng = $lang->get( 'TOP_LANGUAGES_ENG'   , [ ] , 'Английский' );
		$chi = $lang->get( 'TOP_LANGUAGES_CHINA' , [ ] , 'Китайский'  );

		$current = $url_base[ 0 ] ?? 'ru-ru';

		$this->menu->link( 'languages' , $rus , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_ru , use_all_adress_data : false ) , $current === 'ru-ru' );
		$this->menu->link( 'languages' , $eng , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_en , use_all_adress_data : false ) , $current === 'en-en' );
		$this->menu->link( 'languages' , $chi , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_ch , use_all_adress_data : false ) , $current === 'ch-ch' );
	}

	private function fill_navigate ( ) : void {
		$main = $this->main;
		$lang = $main->language;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_base     = $url->static_base;
		$current_page = (string) ( $url_base[ 1 ] ?? '' );

		$items = [
			[ 'MENU_HOME'      , 'index'     , 'Главная'      ] ,
			[ 'MENU_DOCS'      , 'docs'      , 'Документация' ] ,
			[ 'MENU_NEWS'      , 'news'      , 'Новости'      ] ,
			[ 'MENU_DOWNLOADS' , 'downloads' , 'Загрузки'     ] ,
		];

		foreach ( $items as $it ) {
			[ $key , $slug , $default ] = $it;

			$this->menu->link(
				'navigate' ,
				$lang->get( $key , [ ] , $default ) ,
				$link->href( false , [ $url_base[ 0 ] , $slug ] , use_all_adress_data : false ) ,
				$current_page === $slug
			);
		}
	}
}
<?PHP

namespace damoony;

class alliasglobal_top extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'top';
	public bool   $settings_asmenu   = false;

	# --- custom ---
	public string $top_sitename  = 'DAMὸὸNY<span>На страже вашей стабильности</span>';
	public string $top_siteabout = '&nbsp;';

	# --- methods ---
	protected function scheme ( ) : void {
		$this->visuotag
			->tag( 'div' , class : 'toplogo' )
			->tag( 'div' , class : 'toplogo_logo' , return : false )
			->tag( 'div' , class : 'toplogo_title' )
				->tag( 'div' , class : 'siteprojects' , return : false )
				->tag( 'div' , class : 'sitename' , return : false )
				->tag( 'div' , class : 'siteabout' , return : false )
			->parent( )
			->tag( 'div' , class : 'toplogo_links' )
				->tag( 'div' , class : 'toplogo_links_top' )
					->tag( 'div' , class : 'toplogo_links_top_login' , return : false )
					->tag( 'div' , class : 'toplogo_links_top_skins' , return : false )
					->tag( 'div' , class : 'toplogo_links_top_languages' , return : false )
				->parent( )
				->tag( 'div' , class : 'toplogo_links_pagelogo' )
				->parent( )
				->tag( 'div' , class : 'toplogo_links_navigate' )
			->parent( 2 )
			->tag( 'div' , class : 'topsubmenu' )
				->tag( 'div' , class : 'topsubmenu_title' );
	}
	
	protected function data ( ) : void {
		$main  = $this->main;
		$lang  = $main->language;
		$lib   = $main->library;
		$url   = $lib->url( );
		$link  = $lib->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;
		$url_vars   = $url->static_vars;

		# ---- projects ----
		$top_projects_current  = $url_allias[ 1 ] ?? '';
		$top_projects_damoony  = $lang->get( 'TOP_PROJECTS_DAMOONY' , [ ] , 'Damoony' );
		$top_projects_asptour  = $lang->get( 'TOP_PROJECTS_ASPTOUR' , [ ] , 'Asptour' );
		$top_projects_current_comment = $lang->get( 'TOP_PROJECTS_CURRENT_COMMENT' , [ ] , '' );
		$top_projects_damoony_comment = $lang->get( 'TOP_PROJECTS_DAMOONY_COMMENT' , [ ] , '' );
		$top_projects_asptour_comment = '';

		$this->menu->link( 'projects' , $top_projects_current , $link->href( false , [ $url_base[ 0 ] , 'main' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'main' , false , $top_projects_current_comment );
		$this->menu->link( 'projects' , $top_projects_damoony , $link->href( false , [ $url_base[ 0 ] , 'news' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'news' , false , $top_projects_damoony_comment );
		$this->menu->link( 'projects' , $top_projects_asptour , $link->href( false , [ $url_base[ 0 ] , 'profile' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'reports' , false , $top_projects_asptour_comment );

		# ---- skins ----
		$title_skins_light = $lang->get( 'TOP_SKINS_LIGHT' , [ ] , 'Светлый' );
		$title_skins_dark  = $lang->get( 'TOP_SKINS_DARK' , [ ] , 'Темный' );

		$this->menu->link( 'skins' , $title_skins_light , $link->href( $url_domain , $url_base , [ 'skin'=>'default' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'news' , true );
		$this->menu->link( 'skins' , $title_skins_dark  , $link->href( $url_domain , $url_base , [ 'skin'=>'dark' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'reports' , true );

		# ---- login ----
		$top_login_profile    = $lang->get( 'TOP_LOGIN_PROFILE' , [ $main->user->profile->data[ 'login' ] ?? '' ] , '%s' );
		$top_login_settings   = $lang->get( 'TOP_LOGIN_SETTINGS' , [ ] , 'Настройки' );
		$top_login_logout     = $lang->get( 'TOP_LOGIN_LOGOUT' , [ ] , 'Выход' );
		$top_login_login      = $lang->get( 'TOP_LOGIN_LOGIN' , [ ] , 'Авторизация' );
		$top_login_registration = $lang->get( 'TOP_LOGIN_REGISTRATION' , [ ] , 'Регистрация' );

		if ( ( $url_allias[ 2 ] ?? '' ) !== 'login' ) {
			if ( $main->user->is_guest( ) === false ) {
				$this->menu->link( 'login' , $top_login_profile  , $link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , [ 'profile' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'profile' );
				$this->menu->link( 'login' , $top_login_settings , $link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , [ 'settings' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'settings' );
				$this->menu->link( 'login' , $top_login_logout   , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'logout' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'logout' );
			} else {
				$this->menu->link( 'login' , $top_login_login        , $link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , [ 'login' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'news' , false , '' , true , true );
				$this->menu->link( 'login' , $top_login_registration , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'remember' ] , use_all_adress_data : false ) , ( $url_base[ 1 ] ?? '' ) === 'reports' , false , '' , true , true );
			}
		}

		# ---- languages ----
		$url_ru = $url_base; $url_ru[ 0 ] = 'ru-ru';
		$url_en = $url_base; $url_en[ 0 ] = 'en-en';
		$url_ch = $url_base; $url_ch[ 0 ] = 'ch-ch';

		$title_languages_rus   = $lang->get( 'TOP_LANGUAGES_RUS' , [ ] , 'Русский' );
		$title_languages_eng   = $lang->get( 'TOP_LANGUAGES_ENG' , [ ] , 'Английский' );
		$title_languages_china = $lang->get( 'TOP_LANGUAGES_CHINA' , [ ] , 'Китайский' );

		$this->menu->link( 'languages' , $title_languages_rus   , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_ru , use_all_adress_data : false ) , $url_base[ 0 ] === 'ru-ru' );
		$this->menu->link( 'languages' , $title_languages_eng   , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_en , use_all_adress_data : false ) , $url_base[ 0 ] === 'en-en' );
		$this->menu->link( 'languages' , $title_languages_china , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_ch , use_all_adress_data : false ) , $url_base[ 0 ] === 'ch-ch' );
	}

	protected function release ( ) : void {
		$root = $this->visuotag->root( );

		# --- siteprojects / login / skins / languages / navigate / subnavigate ---
		$slot = $root->find( class : 'siteprojects' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'projects' ) );

		$slot = $root->find( class : 'toplogo_links_top_login' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'login' ) );

		$slot = $root->find( class : 'toplogo_links_top_skins' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'skins' ) );

		$slot = $root->find( class : 'toplogo_links_top_languages' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'languages' ) );

		$slot = $root->find( class : 'toplogo_links_navigate' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'navigate' ) );

		$slot = $root->find( class : 'topsubmenu_title' );
		if ( $slot !== null ) $slot->append( $this->menu->as_tag( 'subnavigate' ) );

		# --- sitename / siteabout ---
		$slot = $root->find( class : 'sitename' );
		if ( $slot !== null ) {
			$slot->tag( 'a' )->attrs_add( 'href' , $this->main->library->link( )->href( [ $this->main->library->url( )->static_allias[ 0 ] ] , [ ] ) )->content( $this->top_sitename );
		}

		$slot = $root->find( class : 'siteabout' );
		if ( $slot !== null ) $slot->content( $this->top_siteabout );

		$slot = $root->find( class : 'toplogo_links_pagelogo' );
		if ( $slot !== null ) $slot->tag( 'div' , class : 'pagelogo' )->content( '&nbsp;' );
	}
}
<?PHP

namespace damoony;

class alliasglobal_top {
	# vars
	public alliasglobal $alliasglobal;
	public \main $main;
	public \visuorelease $visuorelease;
	public \visuotag $visuotag;
	public array $vts = [ ]; // visuotags
	public array $vtd = [ ]; // visuotags data
	public null|\visuotag $visuotag_title = null;
	public string $alliasglobal_name = 'top';
	public string $allias_accepted = '';
	public bool $settings_on = false;
	public bool $settings_asmenu = false;
	public alliasglobal_menu $alliasglobal_menu;
	public string $top_sitename = 'DAMὸὸNY<span>На страже вашей стабильности</span>';
	public string $top_siteabout = '&nbsp;';
	# methods
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal = $alliasglobal;
		$this->main = $alliasglobal->main;
		$this->visuorelease = $this->main->visuorelease;
		$this->visuotag = new \visuotag( $this->visuorelease );
		$this->allias_accepted = $this->main->main_settings->allias_accepted( );

		$this->alliasglobal_menu = new alliasglobal_menu( $this->alliasglobal , $this->alliasglobal_name );

		$this->init( );
	}
	
	private function init ( ) {
		$localname = $this->alliasglobal_name;
		$alliaslocal_on = $this->main->alliaslocal->$localname->settings_on;
		
		if( $alliaslocal_on === true ){
			$this->visuotag->clear( )->append( $this->main->alliaslocal->$localname->visuotag );
		} else {
			$this->alliasglobal_menu->clear( );
			$this->algoritm( );
		}
	}

	public function algoritm ( ) {
		$this->scheme( );
		$this->data ( );
		$this->release ( );		
	}
	
	public function scheme ( ) {
		$object = $this;
		
		$visuotag = $object->visuotag;
		$visuotag->tag( 'div' , class: 'toplogo' )
			->tag( 'div' , class : 'toplogo_logo' , return : false )
			->tag( 'div' , class : 'toplogo_title' )
				->tag( 'div' , class : 'siteprojects' , return : false )
				->tag( 'div' , class : 'sitename' , return : false )
				->tag( 'div' , class : 'siteabout' , return : false )
			->parent( )
			->tag( 'div' , class: 'toplogo_links' )
				->tag( 'div' , class: 'toplogo_links_top' )
					->tag( 'div' , class : 'toplogo_links_top_login' , return : false )
					->tag( 'div' , class : 'toplogo_links_top_skins' , return : false )
					->tag( 'div' , class : 'toplogo_links_top_languages' , return : false )
				->parent( )->tag( 'div' , class: 'toplogo_links_pagelogo' )
				->parent( )->tag( 'div' , class: 'toplogo_links_navigate' )
			->parent( 2 )->tag( 'div' , class : 'topsubmenu' )
				->tag( 'div' , class : 'topsubmenu_title' );
		
		
		$object->vts[ 'root' ] = $visuotag->root( );
		$root = $object->vts[ 'root' ];
		$object->vts[ 'toplogo' ] = $root->find( class : 'toplogo' );
		$object->vts[ 'topsubmenu' ] = $root->find( class : 'topsubmenu' );
		$object->vts[ 'topsubmenu_title' ] = $root->find( class : 'topsubmenu_title' );
		$object->vts[ 'toplogo_title' ] = $root->find( class : 'toplogo_title' );
		$object->vts[ 'toplogo_title_siteprojects' ] = $root->find( class : 'siteprojects' );
		$object->vts[ 'toplogo_title_sitename' ] = $root->find( class : 'sitename' );
		$object->vts[ 'toplogo_title_siteabout' ] = $root->find( class : 'siteabout' );
		$object->vts[ 'toplogo_links' ] = $root->find( class : 'toplogo_links' );
		$object->vts[ 'toplogo_links_top' ] = $object->vts[ 'toplogo_links' ]->find( class : 'toplogo_links_top' );
		$object->vts[ 'toplogo_links_top_login' ] = $object->vts[ 'toplogo_links' ]->find( class : 'toplogo_links_top_login' );
		$object->vts[ 'toplogo_links_top_skins' ] = $object->vts[ 'toplogo_links' ]->find( class : 'toplogo_links_top_skins' );
		$object->vts[ 'toplogo_links_top_languages' ] = $object->vts[ 'toplogo_links' ]->find( class : 'toplogo_links_top_languages' );
		$object->vts[ 'toplogo_links_pagelogo' ] = $object->vts[ 'toplogo_links' ]->find( class : 'toplogo_links_pagelogo' );
		$object->vts[ 'toplogo_links_navigate' ] = $object->vts[ 'toplogo_links' ]->find( class : 'toplogo_links_navigate' );
		
	}

	public function data ( ) {
		$main = $this->main;
		$lang = $main->language;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		# data__init
		$this->data__init( );

		$accept = $main->main_settings->allias_accepted( );
		
		$data = [ ];
		if( ! is_null( $accept ) ){
			$classglobal_path = __DIR__ . DS;
			$classglobal_path .= $this->alliasglobal_name . '_' . $accept . '.php';
			$classglobal_name = 'alliasglobal_' . $this->alliasglobal_name . '_' . $accept;

			# print 'classglobal_path: ' . $classglobal_path . '<br />';
			# print 'classglobal_name: ' . $classglobal_name . '<br />';

			if( include_once $path = $classglobal_path ){
				if( class_exists( $classglobal_name ) ){
					$classglobal_class = new $classglobal_name( $this );
					# return ;
				}
			} else {
			}
		}
	}

	public function data__init ( ) {
		$main = $this->main;
		
		$lang = $main->language;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;

		$menu = $this->alliasglobal_menu;

		$top_projects_current = $url_allias[ 1 ];
		$top_projects_damoony = $lang->get( 'TOP_PROJECTS_DAMOONY' , [ ] , 'Damoony' );
		$top_projects_asptour = $lang->get( 'TOP_PROJECTS_ASPTOUR' , [ ] , 'Asptour' );
		$top_projects_current_comment = $lang->get( 'TOP_PROJECTS_CURRENT_COMMENT' , [ ] , '' );
		$top_projects_damoony_comment_ahref_login = $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'ru-ru' , 'form' ] , $url_vars );
		$top_projects_damoony_comment_ahref_football = $link->href( [ 'football' , $url_allias[ 0 ] ] , [ ] , $url_vars );
		$top_projects_damoony_comment_ahref_visuo = $link->href( [ 'visuo' , $url_allias[ 0 ] ] , [ ] , $url_vars );
		$top_projects_damoony_comment_ahref_oxydog = $link->href( [ 'oxydog' , $url_allias[ 0 ] ] , [ ] , $url_vars );
		$top_projects_damoony_comment = $lang->get( 'TOP_PROJECTS_DAMOONY_COMMENT' , [ $top_projects_damoony_comment_ahref_login , $top_projects_damoony_comment_ahref_football , $top_projects_damoony_comment_ahref_visuo , $top_projects_damoony_comment_ahref_oxydog ] , '<a href="%s">login</a>
		<a href="%s">football<span>Футбольный менеджер, возьми команду и приведи её к успеху!</span></a>
		<a href="%s">visuo</a>
		<a href="%s">oxydog<span>Фонд благотворительной помощи Оксаны и Искры</span></a>' );
		$top_projects_asptour_comment = '';

		$title_skins_light = $lang->get( 'TOP_SKINS_LIGHT' , [ ] , 'Светлый' );
		$title_skins_dark = $lang->get( 'TOP_SKINS_DARK' , [ ] , 'Темный' );
		$title_skins_light_comment = $lang->get( 'TOP_SKINS_LIGHT_COMMENT' , [ ] , '' );
		$title_skins_dark_comment = $lang->get( 'TOP_SKINS_DARK_COMMENT' , [ ] , '' );
		
		# menu : projects
		! $url_allias[ 1 ] ?: $menu->new( 'projects' )->link( $top_projects_current , $link->href( false , [ $url_base[ 0 ] , 'main' ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'main' , false , $top_projects_current_comment , true);
		$menu->new( 'projects' )->link( $top_projects_damoony , $link->href( false , [ $url_base[ 0 ] , 'news' ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'news' , false , $top_projects_damoony_comment , true );
		$menu->new( 'projects' )->link( $top_projects_asptour , $link->href( false , [ $url_base[ 0 ] , 'profile' ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'reports' , false , $top_projects_asptour_comment , true );
		#menu : skins	
		$menu->new( 'skins' )->link( $title_skins_light , $link->href( $url_domain , $url_base , [ 'skin'=>'default' ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'news' , true );
		$menu->new( 'skins' )->link( $title_skins_dark , $link->href( $url_domain , $url_base , [ 'skin'=>'dark' ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'reports' , true );
		# menu : login
		$top_login_profile = $lang->get( 'TOP_LOGIN_PROFILE' , [ $main->user->profile->data[ 'login' ] ] , '%s' );
		$top_login_settings = $lang->get( 'TOP_LOGIN_SETTINGS' , [ ] , 'Настройки' );
		$top_login_logout = $lang->get( 'TOP_LOGIN_LOGOUT' , [ ] , 'Выход' );
		$top_login_profile_comment = $lang->get( 'TOP_LOGIN_PROFILE_COMMENT' , [ ] , '' );
		$top_login_settings_comment = $lang->get( 'TOP_LOGIN_SETTINGS_COMMENT' , [ ] , '' );
		$top_login_logout_comment = $lang->get( 'TOP_LOGIN_LOGOUT_COMMENT' , [ ] , '' );

		$top_login_login = $lang->get( 'TOP_LOGIN_LOGIN' , [ ] , 'Авторизация' );
		$top_login_registration = $lang->get( 'TOP_LOGIN_REGISTRATION' , [ ] , 'Регистрация' );
		$top_login_login_comment = $lang->get( 'TOP_LOGIN_LOGIN_COMMENT' , [ ] , 'Авторизация' );
		$top_login_registration_comment = $lang->get( 'TOP_LOGIN_REGISTRATION_COMMENT' , [ ] , 'Регистрация' );
		
		if( $url_allias[ 2 ] != 'login' ){
			if( $main->user->is_guest( ) === false ){
				$menu->new( 'login' )->link( $top_login_profile , $link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , [ 'profile' ] ,  [ ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'profile' );
				$menu->new( 'login' )->link( $top_login_settings , $link->href( [ $url_allias[ 1 ] , $url_allias[ 0 ] ] , [ 'settings' ] ,  [ ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'settings' );
				$menu->new( 'login' )->link( $top_login_logout , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'logout' ] ,  [ ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'logout' );
			} else {
				$menu->new( 'login' )->link( $top_login_login , $link->href( [$url_allias[ 1 ] , $url_allias[ 0 ] ] , [ 'login' ] ,  [ ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'news' , targetblank : true );
				$menu->new( 'login' )->link( $top_login_registration , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'remember' ] ,  [ ] , use_all_adress_data : false ) , $url_base[ 1 ] === 'reports' , targetblank : true );
			}
		} else {
			# $this->menu_add_data( 'login' , [ 'ru'=>'football' , 'en'=>'ASPtour' ] , [ 'ru'=>'' , 'en'=>'' ] , 'base' , 2 , 'ru' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'registration' ] ,  [ ] , use_all_adress_data : false ) , true , true );
			# $this->menu_add_data( 'login' , [ 'ru'=>'visuo' , 'en'=>'ASPtour' ] , [ 'ru'=>'' , 'en'=>'' ] , 'base' , 2 , 'ru' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'registration' ] ,  [ ] , use_all_adress_data : false ) , true , true );
			# $this->menu_add_data( 'login' , [ 'ru'=>'faq' , 'en'=>'ASPtour' ] , [ 'ru'=>'' , 'en'=>'' ] , 'base' , 2 , 'ru' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'registration' ] ,  [ ] , use_all_adress_data : false ) , true , true );
		}
		# menu : languages
		# $is_lang = mb_eregi( '^[a-z]{2}-[a-z]{2}$' , $url_base[ 0 ] , $is_lang_matches );
		$url_base_rus = $url_base;
		$url_base_eng = $url_base;
		$url_base_ch = $url_base;
		$url_base_rus[ 0 ] = 'ru-ru';
		$url_base_eng[ 0 ] = 'en-en';
		$url_base_ch[ 0 ] = 'ch-ch';

		$title_languages_rus = $lang->get( 'TOP_LANGUAGES_RUS' , [ ] , 'Русский' );
		$title_languages_eng = $lang->get( 'TOP_LANGUAGES_ENG' , [ ] , 'Английский' );
		$title_languages_china = $lang->get( 'TOP_LANGUAGES_CHINA' , [ ] , 'Китайский' );
		$title_languages_rus_comment = $lang->get( 'TOP_LANGUAGES_RUS_COMMENT' , [ ] , '' );
		$title_languages_eng_comment = $lang->get( 'TOP_LANGUAGES_ENG_COMMENT' , [ ] , '' );
		$title_languages_china_comment = $lang->get( 'TOP_LANGUAGES_CHINA_COMMENT' , [ ] , '' );
		
		$menu->new( 'languages' )->link( $title_languages_rus , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_base_rus , use_all_adress_data : false ) , $url_base[ 0 ] === 'ru-ru' );
		$menu->new( 'languages' )->link( $title_languages_eng , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_base_eng , use_all_adress_data : false ) , $url_base[ 0 ] === 'en-en' );
		$menu->new( 'languages' )->link( $title_languages_china , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_base_ch , use_all_adress_data : false ) , $url_base[ 0 ] === 'ch-ch' );
	}
	// 
	public function release ( ) {
		$main = $this->main;
		$alliasglobal = $this->alliasglobal;
		
		$lang = $main->language;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$ahref_home = $link->href( [ $url_allias[ 0 ] ] , [ ] );
		$this->vts[ 'toplogo_title_sitename' ]->tag( 'a' )->attrs_add( 'href' , $ahref_home )->content( $this->top_sitename );
		$this->vts[ 'toplogo_title_siteabout' ]->content( $this->top_siteabout );
		
		$this->vts[ 'toplogo_links_pagelogo' ]->tag( 'div' , class : 'pagelogo' )->content( '&nbsp;' );
		

		# $this->vts[ 'toplogo_links_top_skins' ] = $this->vts[ 'toplogo_links_top_skins' ]
		# 	->tag( 'div' , class : 'box' )->tag( 'div' , class : 'data' );
		# $this->vts[ 'toplogo_links_top_languages' ] = $this->vts[ 'toplogo_links_top_languages' ]
		# 	->tag( 'div' , class : 'box' );
		
		$vts = $this->vts;
		$vtd = [ ];
		$vtd[ 'toplogo_title_siteprojects' ] = $this->alliasglobal_menu->as_tag( 'projects' );
		$vtd[ 'toplogo_links_top_login' ] = $this->alliasglobal_menu->as_tag( 'login' );
		$vtd[ 'toplogo_links_top_skins' ] = $this->alliasglobal_menu->as_tag( 'skins' );
		$vtd[ 'toplogo_links_top_languages' ] = $this->alliasglobal_menu->as_tag( 'languages' );
		$vtd[ 'toplogo_links_navigate' ] = $this->alliasglobal_menu->as_tag( 'navigate' );
		$vtd[ 'topsubmenu' ] = $this->alliasglobal_menu->as_tag( 'subnavigate' );

		# die( $vtd[ 'toplogo_links_navigate' ]->release( ) );
		$alliasglobal->append( $this , 'toplogo_title_siteprojects' , $vts , $vtd );
		$alliasglobal->append( $this , 'toplogo_links_top_login' , $vts , $vtd );
		$alliasglobal->append( $this , 'toplogo_links_top_skins' , $vts , $vtd );
		$alliasglobal->append( $this , 'toplogo_links_top_languages' , $vts , $vtd );
		$alliasglobal->append( $this , 'toplogo_links_navigate' , $vts , $vtd );
		$alliasglobal->append( $this , 'topsubmenu' , $vts , $vtd );
	}
	
	public function reload ( ) {
		# example from algoritm script:
		// $alliasglobal = $this->algoritm->main->alliasglobal;
		// $alliasglobal->top->alliasglobal_menu->clear( 'navigate' );
		// $alliasglobal->top->alliasglobal_menu->new( 'navigate' )->link( 'test1' , 'test' , true );
		// $alliasglobal->top->alliasglobal_menu->new( 'navigate' )->link( 'test1' , 'test' , true );
		// $alliasglobal->top->alliasglobal_menu->new( 'navigate' )->link( 'test1' , 'test' , true );
		// $alliasglobal->top->reload( );


		$this->visuotag->clear( );
		
		$this->scheme( );
		$this->release( );
	}
	
	public function as_tag ( ) {
		return $this->visuotag->root( );
	}
	
	
	public function asmenu ( bool $showasmenu ) {
		$this->showasmenu = $showasmenu;
		if( $showasmenu === false ){
			$this->visuotag->clear( );
		}
	}
	public function visuotag ( ) {
		return $this->visuotag;
	}
}

?>
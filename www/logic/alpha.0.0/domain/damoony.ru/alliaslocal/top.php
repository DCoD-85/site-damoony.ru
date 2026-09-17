<?PHP

namespace damoony;

class alliaslocal_top {
	# vars
	public alliaslocal $alliaslocal;
	public main $main;
	public visuorelease $visuorelease;
	public visuotag $visuotag;
	public null|visuotag $visuotag_title = null;
	public string $alliasglobal_name = 'top';
	public bool $settings_on = false;
	public string $allias_accepted = '';
	public string $settings_lang = 'en';
	public array $menu = [ ];
	public array $menu_projects = [ ];
	public array $menu_skins = [ ];
	public array $menu_languages = [ ];
	public array $menu_login = [ ];
	public array $menu_level = [ ];
	public array $menu_level1 = [ ];
	# methods
	public function __construct ( alliaslocal $alliaslocal ) {
		$this->alliaslocal = $alliaslocal;

		$this->main = $alliaslocal->main;
		$this->visuorelease = $this->main->visuorelease;
		$this->visuotag = new visuotag( $this->visuorelease );
		$this->allias_accepted = $this->main->main_settings->allias_accepted( );
		$this->settings_lang = $this->main->main_settings->get( 'language' );

		$this->init( );
	}
	
	public function init ( ) {
		
		$classlocal_path = __DIR__ . DS . $this->alliasglobal_name . DS;
		$classlocal_path .= $this->allias_accepted . '.php';
		$classlocal_name = __CLASS__ . '_' . $this->allias_accepted;

		# print '$classlocal_path: ' . $classlocal_path . '<br />';
		# print '$classlocal_name: ' . $classlocal_name . '<br />';
		if( include_once $path = $classlocal_path ){
			if( class_exists( $classlocal_name ) ) {
				$classlocal = new $classlocal_name( $this );
				if( $classlocal->init( ) === true ){
					$this->settings_on = true;
				} else { }
			} else $this->settings_on = false;
		} else $this->settings_on = false;

		# $this->algoritm( );
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
				->parent( )->tag( 'div' , class: 'toplogo_links_subprojects' )
				->parent( )->tag( 'div' , class: 'toplogo_links_links' )
			->parent( 2 )->tag( 'div' , class : 'topsubmenu' )
				->tag( 'div' , class : 'topsubmenu_title' );
		
		$object->root = $visuotag->root( );
		$root = $object->root;
		$object->toplogo = $root->find( class : 'toplogo' );
		$object->topsubmenu = $root->find( class : 'topsubmenu' );
		$object->topsubmenu_title = $root->find( class : 'topsubmenu_title' );
		$object->toplogo_title = $root->find( class : 'toplogo_title' );
		$object->toplogo_title_siteprojects = $root->find( class : 'siteprojects' );
		$object->toplogo_title_sitename = $root->find( class : 'sitename' );
		$object->toplogo_title_siteabout = $root->find( class : 'siteabout' );
		$object->visuotag_links = $root->find( class : 'toplogo_links' );
		$object->visuotag_links_top = $object->visuotag_links->find( class : 'toplogo_links_top' );
		$object->visuotag_links_top_login = $object->visuotag_links->find( class : 'toplogo_links_top_login' );
		$object->visuotag_links_top_skins = $object->visuotag_links->find( class : 'toplogo_links_top_skins' );
		$object->visuotag_links_top_languages = $object->visuotag_links->find( class : 'toplogo_links_top_languages' );
		$object->toplogo_links_subprojects = $object->visuotag_links->find( class : 'toplogo_links_subprojects' );
		$object->visuotag_links_links = $object->visuotag_links->find( class : 'toplogo_links_links' );
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
		
		# 
		
		$accept = $main->main_settings->allias_accepted( );
		
		$data = [ ];
		if( ! is_null( $accept ) ){
			$classglobal_path = __DIR__ . DS . $this->alliasglobal_name . DS . 'global' . DS;
			$classglobal_path .= $this->alliasglobal_name . '_global_' . $accept . '.php';
			$classglobal_name = 'alliasglobal_' . $this->alliasglobal_name . '_global_' . $accept;

			print 'classglobal_path: ' . $classglobal_path . '<br />';
			print 'classglobal_name: ' . $classglobal_name . '<br />';

			if( include_once $path = $classglobal_path ){
				if( class_exists( $classglobal_name ) ){
					new $classglobal_name( $this );
					return ;
				}
			} else {
			}
		}

		$this->data__init( );
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


		$top_projects_current = $url_allias[ 1 ];
		$top_projects_damoony = $lang->get( 'TOP_PROJECTS_DAMOONY' , [ ] , 'Damoony' );
		$top_projects_asptour = $lang->get( 'TOP_PROJECTS_ASPTOUR' , [ ] , 'Asptour' );
		$top_projects_current_comment = $lang->get( 'TOP_PROJECTS_CURRENT_COMMENT' , [ ] , '' );
		$top_projects_damoony_comment_ahref_login = $link->href( [ 'login' , $url_allias[ 0 ] ] , [ ] , $url_vars );
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
		! $url_allias[ 1 ] ?: $this->menu_add_data( 'projects' , $top_projects_current , $top_projects_current_comment , 'domain' , 1 , $url_allias[ 1 ] , $link->href( [ $url_allias[ 1 ] , 'damoony2.ru' ] , [ 'index' ] , use_all_adress_data : false ) );
		$this->menu_add_data( 'projects' , $top_projects_damoony , $top_projects_damoony_comment , 'domain' , 2 , 'visuo' , $link->href( [ 'damoony2.ru' ] , [ 'form' ] , use_all_adress_data : false ) );
		$this->menu_add_data( 'projects' , $top_projects_asptour , $top_projects_asptour_comment , 'domain' , 2 , 'asptour' , $link->href( [ 'asptour2.ru' ] , [ 'registration' ] , use_all_adress_data : false ) );
		
		#menu : skins
		$this->menu_add_data( 'skins' , $title_skins_light , $title_skins_light_comment , 'base' , 2 , 'asptour' , $link->href( false , $url_base ,  [ 'skin'=>'default' ] , use_all_adress_data : false ) );
		$this->menu_add_data( 'skins' , $title_skins_dark , $title_skins_dark_comment , 'base' , 2 , 'asptour' , $link->href( false , $url_base , [ 'skin'=>'dark' ] , use_all_adress_data : false ) );
		
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
		
		if( $url_allias[ 1 ] != 'login' ){
			if( $main->user->is_guest( ) === false ){
				$this->menu_add_data( 'login' , $top_login_profile , $top_login_profile_comment , 'base' , 2 , 'ru' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'profile' ] , use_all_adress_data : false ) , true , true );
				$this->menu_add_data( 'login' , $top_login_settings , $top_login_settings_comment , 'base' , 2 , 'ru' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'settings' ] ,  [ ] , use_all_adress_data : false ) , true , true );
				$this->menu_add_data( 'login' , $top_login_logout , $top_login_logout_comment , 'base' , 2 , 'ru' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'logout' ] ,  [ ] , use_all_adress_data : false ) , true , true );
			} else {
				$this->menu_add_data( 'login' , $top_login_login , $top_login_login_comment , 'base' , 2 , 'ru' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'form' ] ,  [ ] , use_all_adress_data : false ) , true , true );
				$this->menu_add_data( 'login' , $top_login_registration , $top_login_registration_comment , 'base' , 2 , 'ru' , $link->href( [ 'login' , $url_allias[ 0 ] ] , [ 'registration' ] ,  [ ] , use_all_adress_data : false ) , true , true );
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
		$title_languages_eng = $lang->get( 'TOP_LANGUAGES_ENG' , [ ] , 'Англиийский' );
		$title_languages_china = $lang->get( 'TOP_LANGUAGES_CHINA' , [ ] , 'Китайский' );
		$title_languages_rus_comment = $lang->get( 'TOP_LANGUAGES_RUS_COMMENT' , [ ] , '' );
		$title_languages_eng_comment = $lang->get( 'TOP_LANGUAGES_ENG_COMMENT' , [ ] , '' );
		$title_languages_china_comment = $lang->get( 'TOP_LANGUAGES_CHINA_COMMENT' , [ ] , '' );

		$this->menu_add_data( 'languages' , $title_languages_rus , $title_languages_rus_comment , 'base' , 2 , 'ru' , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_base_rus , use_all_adress_data : false ) );
		$this->menu_add_data( 'languages' , $title_languages_eng , $title_languages_eng_comment , 'base' , 2 , 'en' , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_base_eng , use_all_adress_data : false ) );
		$this->menu_add_data( 'languages' , $title_languages_china , $title_languages_china_comment , 'base' , 2 , 'ch' , $link->href( [ $url_allias[ 1 ] ?: null , $url_allias[ 0 ] ] , $url_base_ch , use_all_adress_data : false ) );
	
	}
	// 
	public function release ( ) {
		$main = $this->main;
		$parent = $this->top;
		
		$lang = $main->language;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$ahref_home = $link->href( [ $url_allias[ 0 ] ] , [ ] );
		$this->toplogo_title_sitename->tag( 'a' )->attrs_add( 'href' , $ahref_home )->content( 'oxyDOG' );
		$this->toplogo_title_siteabout->content( 'Спасём этот мир вместе!' );

		$this->menu( 'projects' , $this->toplogo_title_siteprojects );
		$this->menu( 'login' , $this->visuotag_links_top_login );
		
		$this->toplogo_links_subprojects->tag( 'span' )->content( '&nbsp;' );

		$this->visuotag_links_top_skins = $this->visuotag_links_top_skins
			->tag( 'div' , class : 'box' )->tag( 'div' , class : 'data' );
		$this->menu( 'skins' , $this->visuotag_links_top_skins );
		
		$this->visuotag_links_top_languages = $this->visuotag_links_top_languages
			->tag( 'div' , class : 'box' );
		$this->menu( 'languages' , $this->visuotag_links_top_languages );
		$this->menu( 'navigate' , $this->visuotag_links_links );
		$this->menu( 'subnavigate' , $this->topsubmenu );
	}
	
	public function data2 ( ) {
		$main = $this->main;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$domain_real = $main->main_settings->domain_real_name;
		$allias_real = $main->main_settings->allias_real_name;
		
		$accept = $main->main_settings->allias_accepted( );
		
		$data = [ ];
		if( ! is_null( $accept ) ){
			$path = 'top_' . $accept . '.php';
			$class = 'alliasglobal_top_' . $accept;
			( include_once $path ) or warning_file( $path , __FILE__ , __LINE__ );
			if( class_exists( $class ) ) new $class( $this );
		}
	}
	
	public function data_www ( ) {
		$main = $this->main;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );
		$domain = $url->domain( );
		
		$this->menu_add_data( 'navigate' , [ 'ru'=>'О проекте' , 'en'=>'About pro' ] , [ 'ru'=>'' , 'en'=>'' ] , 'base' , 0 , 'index' , $link->href( false , [ 'index' ] , use_all_adress_data : false ) );
		# $this->menu_add_data( true , true , 'navigate' , 0 , , 'title'=>[ 'ru'=>'База данных' , 'en'=>'Data base' ] , 'comment'=>[ 'ru'=>'' , 'en'=>'' ] , 'link_selected'=>'database' , 'link'=>$link->href( false , [ 'database' ] , use_all_adress_data : false ) );
		# $this->menu_add_data( true , true , 'navigate' , 0 , 'engine' , 'title'=>[ 'ru'=>'Движок' , 'en'=>'Engine' ] , 'comment'=>[ 'ru'=>'' , 'en'=>'' ] , 'link_selected'=>'engine' , 'link'=>$link->href( false , [ 'engine' ] , use_all_adress_data : false ) );
		# $this->menu_add_data( true , true , 'navigate' , 0 , 'partners' , 'title'=>[ 'ru'=>'Сотрудничество' , 'en'=>'Partners' ] , 'comment'=>[ 'ru'=>'' , 'en'=>'' ] , 'link_selected'=>'partners' , 'link'=>$link->href( false , [ 'partners' ] , use_all_adress_data : false ) );
		
	}
	
	public function data_login ( ) {
		$main = $this->main;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$vt_login_comment = new visuotag( );
		$vt_login_comment = $vt_login_comment->tag( 'div' );
		$vt_login_comment->tag( 'div' )->tag( 'span' , class : 'span_title' )->content( 'Мы начинаем' );
		$vt_login_comment->tag( 'div' )->tag( 'a' )->content( 'Форма авторизации' );
		$vt_login_comment->tag( 'div' )->tag( 'a' )->content( 'Восстановление пароля' );
		$vt_login_comment->tag( 'div' )->tag( 'a' )->content( 'Помощь' );
		
		$this->menu_add_data( true , true , 'level0' , 0 , 'login' , [ 'ru'=>'Авторизация' , 'en'=>'About pro' ] , [ 'ru'=>$vt_login_comment->root( )->release( ) , 'en'=>'' ] , $link->href( false , [ 'login' ] , use_all_adress_data : false ) );
		$this->menu_add_data( true , true , 'level0' , 0 , 'registration' , [ 'ru'=>'Регистрация' , 'en'=>'Data base' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ 'registration' ] , use_all_adress_data : false ) );
		$this->menu_add_data( true , true , 'level0' , 0 , 'attempt' , [ 'ru'=>'Подать заявку' , 'en'=>'Engine' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ 'attempt' ] , use_all_adress_data : false ) );
		$this->menu_add_data( true , true , 'level0' , 0 , 'situation' , [ 'ru'=>'Спорные ситуации' , 'en'=>'Partners' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ 'situation' ] , use_all_adress_data : false ) );
		
		if( $url_base[ 0 ] == 'login' ){
			$this->menu_add_data( true , true , 'level1' , 1 , 'form' , [ 'ru'=>'Форма авторизации' , 'en'=>'Partners' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ 'login' , 'form' ] , use_all_adress_data : false ) );
			$this->menu_add_data( true , true , 'level1' , 1 , 'remember' , [ 'ru'=>'Восстановить пароль' , 'en'=>'Partners' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ 'login' , 'remember' ] , use_all_adress_data : false ) );
		} else if( $url_base[ 0 ] == 'registration' ){
			$this->menu_add_data( true , true , 'level1' , 1 , 'form' , [ 'ru'=>'Форма регистрации' , 'en'=>'Partners' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ 'registration' , 'form' ] , use_all_adress_data : false ) );
			$this->menu_add_data( true , true , 'level1' , 1 , 'activation' , [ 'ru'=>'Активация регистрации' , 'en'=>'Partners' ] , [ 'ru'=>'' , 'en'=>'' ] , $link->href( false , [ 'registration' , 'activation' ] , use_all_adress_data : false ) );
		}
	}
	
	public function title_set ( visuotag $visuotag_title ) {
		$this->visuotag_title = $visuotag_title;
	}
	
	public function title ( ) {
		$main = $this->main;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$data = [ ];
		$visuotag_title = $this->visuotag_title;
		if( is_null( $visuotag_title ) ){
			$data[ ] = [ 'title'=>[ 'ru'=>$url_allias[ 0 ] , 'en'=>$url_allias[ 0 ] ] , 'link'=>$link->ahref( $url->static_allias , [ ] , use_all_adress_data : false ) ];
			$url_base[ 0 ] ? $data[ ] = [ 'title'=>[ 'ru'=>$url_base[ 0 ] , 'en'=>$url_base[ 0 ] ] , 'link'=>$link->ahref( $url->static_allias , [ $url_base[ 0 ] ] , use_all_adress_data : false ) ] : true;
			$url_base[ 1 ] ? $data[ ] = [ 'title'=>[ 'ru'=>$url_base[ 1 ] , 'en'=>$url_base[ 1 ] ] , 'link'=>$link->ahref( $url->static_allias , [ $url_base[ 0 ] , $url_base[ 1 ]] , use_all_adress_data : false ) ] : true;
		}
		
		$vt = new visuotag( );
		$count = count( $data );
		foreach( $data as $value ){
			$_title = $value[ 'title' ][ $main->main_settings->get( 'language' ) ];
			$_link = $value[ 'link' ];
			$vt->tag( 'a' )->attrs_add( 'href' , $_link )->content( $_title );
			if( --$count != 0 ) $vt->content( ' -- ' );
		}
		
		$visuotag = $this->visuotag;
		
		$topsubmenu_title = $visuotag->root( )->find( class : 'topsubmenu_title' );
		$topsubmenu_title->clear( )->tag( 'div' )->append( $vt );
	}
	
	public function menu_reload ( ) {
		$this->visuotag->clear( );
		$this->init( );
	}
	
	public function menu ( string $menu , visuotag $vt_links ) {
		$main = $this->main;

		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$data = $this->menu[ $menu ];
		
		if( ! empty( $data ) ){
			$vt = new visuotag( );
			$vt = $vt->tag( 'div' , class : $menu );
			if( ! empty( $data ) ){
				$count = count( $data );
				foreach( $data as $key=>$value ){
					$_type = $value[ 'type' ];
					if( $_type == 'link' ){
						$title = $value[ 'title' ]; 
						
						$comment = $value[ 'comment' ];
						$_active = $value[ 'active' ];
						$_islink = $value[ 'islink' ];
						$_link_type = $value[ 'link_type' ];
						$_link_pos = $value[ 'link_pos' ];
						$_link_selected = $value[ 'link_selected' ];
						$_link = $value[ 'link' ];
						
						if( $_link_type === 'domain' ) $_selected = $_link_selected == $url_allias[ $_link_pos ] ? 'selected' : '';
						else if( $_link_type === 'base' ) $_selected = $_link_selected == $url_base[ $_link_pos ] ? 'selected' : '';
						
						$vt2 = $vt;
						if( $_islink === true ){
							
							$vt = $vt->tag( 'span' , class : 'link' . ( ( $comment && $_active ) ? ' link_popup' : '' ) )
								->attrs_class_add( $_selected )
									->tag( 'a' )->attrs_add( 'href' , $_link )->content( $title );
							
									if( $comment && $_active ) $vt->parent( )->tag( 'div' , class : 'link_comment' )
										->tag( 'div' , class : 'link_comment_narrow' )->content( '' )
										->parent( )
										->tag( 'div' , class : 'link_comment_text' )->content( $comment );
						} else {
							$vt = $vt->tag( 'span' , class : 'link unlink' )
								->attrs_class_add( $_selected )
									->content( $title );
						}
						$vt = $vt2;
						
						if( --$count != 0 ){
							$vt->tag( 'span' , class : 'between' )->content( '|' )->parent( );
						}
					} else if( $_type == 'between' ){
						$vt->tag( 'div' , class : 'link_between' );
					}
				}
			}
			$vt_links->append( $vt );
		}
		
	}
	
	public function menu_add_data (
		string $menu ,
		string $title ,
		string $comment ,
		string $link_type ,
		int $link_pos ,
		string $link_selected ,
		string $link ,
		bool $active = true ,
		bool $islink = true
	) {
		# $this->menu_level[ $level ][ ] = [ 'active'=>$active , 'islink'=>$islink , 'type'=>'link' , 'pos'=>$pos , 'selected'=>$selected , 'title'=>$title , 'comment'=>$comment , 'link'=>$link ];
		$this->menu[ $menu ][ ] = [ 'active'=>$active , 'islink'=>$islink , 'type'=>'link' , 'title'=>$title , 'comment'=>$comment , 'link_type'=>$link_type , 'link_pos'=>$link_pos , 'link_selected'=>$link_selected , 'link'=>$link ];
	}
	public function menu_add_between ( string $level , string $separator = '' ) {
		$this->menu_level[ $level ][ ] = [ 'type'=>'between' , 'separator'=>$separator ];
	}
	public function menuback ( string $link ) {
		$this->databack_level0 = [ 'link'=>$link , 'title'=>[ 'ru'=>'Вернуться' , 'en'=>'Comeback' ] ];
	}
	
	public function as_tag ( ) {
		return $this->visuotag->root( );
	}
}

?>
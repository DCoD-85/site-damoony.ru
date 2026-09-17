<?PHP

class subalgoritm_top {
	# vars
	public main $main;
	private visuorelease $visuorelease;
	private visuotag $visuotag;
	public string $settings_lang = 'en';
	# methods
	//
	public function __construct( main $main ) {
		$this->main = $main;
		$this->visuorelease = $main->visuorelease;
		$this->visuotag = new visuotag( $main->visuorelease );
		
		$this->settings_lang = $this->visuorelease->settings( )->get( 'language' ) ?: 'ru';
		
		$this->logo( );
		$this->menu( );
	}
	public function logo ( ) {
		$library = $this->main->library;
		$url = $library->url( );
		$link = $library->link( );
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$projects_links = [
			# [ [ 'ru'=>'Damoony' , 'en'=>'Damoony' ] , $link->href( [ 'index' ] , allias_address : [ $domain[ 0 ] ] , use_all_adress_data : false ) ] ,
			[ [ 'ru'=>'Visuo' , 'en'=>'Visuo' ] , 'visuo' , $link->href( [ 'visuo' , $url_allias[ 0 ] ] , [ 'modules' ] , use_all_adress_data : false ) ] ,
			[ [ 'ru'=>'Football' , 'en'=>'Football' ] , 'football' , $link->href( [ 'football' , $url_allias[ 0 ] ] , [ 'dev' ] , use_all_adress_data : false ) ] ,
		];
		
		$vt_projects = new visuotag( );
		foreach( $projects_links as $value ){
			$_title = $value[ 0 ][ $this->settings_lang ];
			$_selected = $url_allias[ 2 ] == $value[ 1 ] ? 'selected' : '';
			$_link = $value[ 2 ];
			# 
			$vt_projects->tag( 'span' , class : 'link' )->tag( 'a' )->attrs_add( 'href' , $_link )->attrs_class_add( $_selected )->content( $_title );
		}
		
		$logo_links = [
			[ [ 'ru'=>'Домой' , 'en'=>'Home' ] , $link->href( [ 'index' ] , use_all_adress_data : false ) ] ,
			[ [ 'ru'=>'Разработка' , 'en'=>'Development' ] , $link->href( [ 'dev' ] , use_all_adress_data : false ) ] ,
			[ [ 'ru'=>'Модули' , 'en'=>'Modules' ] , $link->href( [ 'modules' ] , use_all_adress_data : false ) ] ,
			[ [ 'ru'=>'Библиотеки' , 'en'=>'Libraries' ] , $link->href( [ 'libraries' ] , use_all_adress_data : false ) ] ,
			[ [ 'ru'=>'Тесты' , 'en'=>'Tests' ] , $link->href( [ 'tests' ] , use_all_adress_data : false ) ] ,
			[ [ 'ru'=>'Вход' , 'en'=>'Login' ] , $link->href( [ 'login' ] , use_all_adress_data : false ) ] ,
		];
		
		
		$visuotag = $this->visuotag;
		$visuotag = $visuotag->tag( 'div' , class: 'toplogo' )
			->tag( 'div' , class : 'toplogo_logo' , return : false )
			->tag( 'div' , class : 'toplogo_title' , return : false )
			->tag( 'div' , class: 'toplogo_links' )
				->tag( 'div' , class: 'toplogo_links_languages' , return : false )
				->tag( 'div' , class: 'toplogo_links_links' );
		
		$ahref_home = $link->href( [ $url_allias[ 0 ] ] , [ ] );
		
		$ahref_en = $link->href( false , $url_base , [ 'language'=>'en' ] );
		$ahref_ru = $link->href( false , $url_base , [ 'language'=>'ru' ] );
		$ahref_ch = $link->href( false , $url_base , [ 'language'=>'ch' ] );
		
		#
		$toplogo_title = $visuotag->root( )->find( class : 'toplogo_title' );
		$toplogo_logo = $visuotag->root( )->find( class : 'toplogo_logo' );
		$visuotag_links = $visuotag->root( )->find( class : 'toplogo_links' );
		$visuotag_links_languages = $visuotag_links->find( class : 'toplogo_links_languages' );
		$visuotag_links_links = $visuotag_links->find( class : 'toplogo_links_links' );
		#
		$toplogo_title
			->tag( 'div' , class : 'siteprojects' )->append( $vt_projects )
			->parent( )->tag( 'div' , class : 'sitename' )->tag( 'a' )->attrs_add( 'href' , $ahref_home )->content( 'DAMOONY' )->parent( )
			->parent( )->tag( 'div' , class : 'siteabout' )->content( 'Национальный сервер' );
		$visuotag_links_languages
			->tag( 'span' )->attrs_add( 'id' , 'lang_ru' )->tag( 'a' )->attrs_add( 'href' , $ahref_ru )->content( 'Русский' )
			->parent( eq : 1 )->tag( 'span' , class : 'between' )->content( '|' )
			->parent( eq : 0 )->tag( 'span' )->attrs_add( 'id' , 'lang_en' )->tag( 'a' )->attrs_add( 'href' , $ahref_en )->content( 'English' )
			->parent( eq : 1 )->tag( 'span' , class : 'between' )->content( '|' )
			->parent( eq : 0 )->tag( 'span' )->attrs_add( 'id' , 'lang_ch' )->tag( 'a' )->attrs_add( 'href' , $ahref_ch )->content( 'Chinese' );
		
		$lang_selected = 'lang_' . $_COOKIE[ 'language' ];
		$toplogo_logo->root( )->find( id : $lang_selected )->attrs_class_add( 'selected' );
		#
		# $visuotag_links = $visuotag->root( )->find( class : 'toplogo_links' );
		
		$count = count( $logo_links );
		foreach( $logo_links as $v ) {
			$title = $v[ 0 ][ $this->settings_lang ];
			$link = $v[ 1 ];
			$selected = $url->url( $link )[ 0 ] == $url->url( )[ 0 ] ? 'selected' : '' ;
			
			$visuotag_links_links
				->tag( 'span' , class : 'link' )->attrs_class_add( $selected )
					->tag( 'a' )->attrs_add( 'href' , $link )
						->content( $title );
			if( --$count != 0 ) $visuotag_links_links->tag( 'span' , class : 'between' )->content( '|' );
		}
	}
	
	public function menu ( ) {
		$library = $this->main->library;
		$url = $library->url( );
		$link = $library->link( );
		
		$visuotag = $this->visuotag;
		
		# $data = $this->ttest_data;
		
		$data = $this->menu_data( );
		
		if( ! empty( $data ) ) {
			$vt_menu = new visuotag( null , null );
			$vt_menu = $vt_menu->tag( 'div' , class: 'topmenu' );
			$vt_menu = $vt_menu->tag( 'div' , class: 'menu' );
			
			$count = count( $data );
			foreach( $data as $k=>$v ) {
				$head = $v[ 'head' ];
				$head_name = $head[ 'name' ];
				$head_title = $head[ 'title' ][ $this->settings_lang];
				$head_comment = $head[ 'comment' ][ $this->settings_lang ];
				$body = $v[ 'body' ];
				
				
				#
				$vt_menu = $vt_menu->tag( 'span' , class: 'menua' )
					# ->attrs_actions_add( 'onmouseover' , '$(this).find( \'.span_popup\' ).eq(0).show( );' )
					# ->attrs_actions_add( 'onmouseout' , '$(this).find( \'.span_popup\' ).eq(0).hide( );' )
					->tag( 'span' , class: 'span_title' )->content( $head_title )
					->parent( )->tag( 'span' , class: 'span_popup' )
						->tag( 'div' , class: 'border' )
							->tag( 'div' , class : 'div' )
								->tag( 'div' , class: 'head_comment' )->content( $head_comment )
									->parent( );
					if( ! empty( $body ) ){
						$vt_menu = $vt_menu->tag( 'div' , class: 'head_links_title' )->content( 'Ссылки:' )->parent( );
						$vt_menu = $vt_menu->tag( 'div' , class: 'head_links' );
						foreach( $body as $v ){
							$body_title = $v[ 'title' ][ $this->settings_lang ];
							$body_link = $v[ 'link' ];
							$vt_menu = $vt_menu->tag( 'a' )
								->attrs_add( 'href' , $body_link )
								->content( $body_title )->parent( );
						}
						$vt_menu = $vt_menu->parent( );
					}
					
				$vt_menu = $vt_menu->parent( )->parent( )->parent( )->parent( );
				
				
				/*
				#
				$vt_menu = $vt_menu->tag( 'span' , class: 'menua' )
					# ->attrs_actions_add( 'onmouseover' , '$(this).find( \'.span_popup\' ).eq(0).show( );' )
					# ->attrs_actions_add( 'onmouseout' , '$(this).find( \'.span_popup\' ).eq(0).hide( );' )
					->tag( 'span' , class: 'span_title' )->content( $head_title )
					->parent( )->tag( 'span' , class: 'span_popup' )
						->tag( 'div' , class: 'border' )
							->tag( 'div' , class : 'div' )
								->tag( 'div' , class: 'head_comment' )->content( $head_comment )
									->parent( );
					if( ! empty( $body ) ){
						$vt_menu = $vt_menu->tag( 'div' , class: 'head_links_title' )->content( 'Ссылки:' )->parent( );
						$vt_menu = $vt_menu->tag( 'div' , class: 'head_links' );
						foreach( $body as $v ){
							$body_title = $v[ 'title' ][ $this->settings_lang ];
							$body_link = $v[ 'link' ];
							$vt_menu = $vt_menu->tag( 'a' )
								->attrs_add( 'href' , $body_link )
								->content( $body_title )->parent( );
						}
						$vt_menu = $vt_menu->parent( );
					}
					
				$vt_menu = $vt_menu->parent( )->parent( )->parent( )->parent( );
				
				*/
				$count--;
				if( $count > 0 ) $vt_menu = $vt_menu->tag( 'span' , class : 'menua_between' )->tag( 'span' , class : 'span_title' )->content( '|' )->parent( )->parent( );
			
			}
			$visuotag->append( $vt_menu );
		}
		
		
		
	}
	
	private function menu_data ( ) {
		$library = $this->main->library;
		$url = $library->url( );
		$link = $library->link( );
		
		$data = [ ];
		switch( $url->url( )[ 1 ] ) {
			case 'index.php' :
		
				$data[ ] = 
					[
						'head'=>[ 'name'=>'name1' , 'title'=>[ 'ru'=>'Это первое, и не единственное меню' , 'en'=>'This is first from more menues' ] , 'comment'=>[ 'ru'=>'Забористый и ошеломительный, очень ошеломительный (!) комментарий про это меню!' , 'en'=>'This is wanderfull expressive commentary about this menu!' ] , 'link'=>'menu1.php' ] ,
						'body'=>[
							[ 'title'=>[ 'ru'=>'Подменю 1 меню 1' , 'en'=>'Sub 1 menu 1' ] , 'comment'=>[ 'ru'=>'Комментарий Подменю 1 меню 1' , 'en'=>'Comment sub 1 menu 1' ] , 'link'=>$link->href( $url->url( ) , [ 'submenu'=>'1' ] ) ] ,
							[ 'title'=>[ 'ru'=>'Подменю 1 меню 2' , 'en'=>'Sub 1 menu 2' ] , 'comment'=>[ 'ru'=>'Комментарий Подменю 1 меню 2' , 'en'=>'Comment sub 1 menu 2' ] , 'link'=>$link->href( $url->url( ) , [ 'submenu'=>'2' ] ) ] ,
							[ 'title'=>[ 'ru'=>'Подменю 1 меню 3' , 'en'=>'Sub 1 menu 3' ] , 'comment'=>[ 'ru'=>'Комментарий Подменю 1 меню 3' , 'en'=>'Comment sub 1 menu 3' ] , 'link'=>$link->href( $url->url( ) , [ 'submenu'=>'3' ] ) ] ,
							[ 'title'=>[ 'ru'=>'Подменю 1 меню 4' , 'en'=>'Sub 1 menu 4' ] , 'comment'=>[ 'ru'=>'Комментарий Подменю 1 меню 4' , 'en'=>'Comment sub 1 menu 4' ] , 'link'=>$link->href( $url->url( ) , [ 'submenu'=>'4' ] ) ] ,
						]
					];
				$data[ ] = [
						'head'=>[ 'name'=>'name2' , 'title'=>[ 'ru'=>'Меню 2' , 'en'=>'Menu 2' ] , 'comment'=>[ 'ru'=>'Комментарий к меню 2' , 'en'=>'Comment name2' ] , 'link'=>'menu2.php' ] ,
						'body'=>[ ]
					];
				$data[ ] = [
						'head'=>[ 'name'=>'name3' , 'title'=>[ 'ru'=>'Меню 3' , 'en'=>'Menu 3' ] , 'comment'=>[ 'ru'=>'Комментарий к меню 3' , 'en'=>'Comment name3' ] , 'link'=>'menu3.php' ] ,
						'body'=>[
							[ 'title'=>[ 'ru'=>'Подменю 3 меню 1' , 'en'=>'Sub 3 menu 1' ] , 'comment'=>[ 'ru'=>'Комментарий Подменю 3 меню 1' , 'en'=>'Comment sub 3 menu 1' ] , 'link'=>'menu1.php?submenu=1' ] ,
							[ 'title'=>[ 'ru'=>'Подменю 3 меню 2' , 'en'=>'Sub 3 menu 2' ] , 'comment'=>[ 'ru'=>'Комментарий Подменю 3 меню 2' , 'en'=>'Comment sub 3 menu 2' ] , 'link'=>'menu1.php?submenu=2' ] ,
						]
					];
				break;
			case 'tests.php' :
				$data[ ] = [
					'head'=>[
						'title'=>[ 'ru'=>'Помощь' , 'en'=>'Home' ] ,
						'comment'=>[ 'ru'=>'Если у Вас возникли трудности, воспользуйтесь одной из следующих опций' , 'en'=>'This is wanderfull expressive commentary about this menu!' ] ,
						'link'=>'menu1.php'
					] , 'body'=>[
					]
				];
				$data[ ] = [
					'head'=>[
						'title'=>[ 'ru'=>'Помощь' , 'en'=>'Development' ] ,
						'comment'=>[ 'ru'=>'Если у Вас возникли трудности, воспользуйтесь одной из следующих опций' , 'en'=>'This is wanderfull expressive commentary about this menu!' ] ,
						'link'=>'menu1.php'
					] , 'body'=>[
					]
				];
				$data[ ] = [
					'head'=>[
						'title'=>[ 'ru'=>'Помощь' , 'en'=>'Libraries' ] ,
						'comment'=>[ 'ru'=>'Если у Вас возникли трудности, воспользуйтесь одной из следующих опций' , 'en'=>'This is wanderfull expressive commentary about this menu!' ] ,
						'link'=>'menu1.php'
					] , 'body'=>[
					]
				];
				$data[ ] = [
					'head'=>[
						'title'=>[ 'ru'=>'Помощь' , 'en'=>'Tests' ] ,
						'comment'=>[ 'ru'=>'Если у Вас возникли трудности, воспользуйтесь одной из следующих опций' , 'en'=>'This is wanderfull expressive commentary about this menu!' ] ,
						'link'=>'menu1.php'
					] , 'body'=>[
					]
				];
				$data[ ] = [
					'head'=>[
						'title'=>[ 'ru'=>'Помощь' , 'en'=>'Login' ] ,
						'comment'=>[ 'ru'=>'Если у Вас возникли трудности, воспользуйтесь одной из следующих опций' , 'en'=>'This is wanderfull expressive commentary about this menu!' ] ,
						'link'=>'menu1.php'
					] , 'body'=>[
					]
				];
				break;
			case 'login.php' :
				$data[ ] = [
					'head'=>[
						'name'=>'name1' ,
						'title'=>[ 'ru'=>'Помощь' , 'en'=>'Help' ] ,
						'comment'=>[ 'ru'=>'Если у Вас возникли трудности, воспользуйтесь одной из следующих опций' , 'en'=>'This is wanderfull expressive commentary about this menu!' ] ,
						'link'=>'menu1.php'
					] , 'body'=>[
						[
							'title'=>[ 'ru'=>'Форма авторизации' , 'en'=>'Authorisation form.' ] ,
							'comment'=>[ 'ru'=>'Комментарий Подменю 1 меню 1' , 'en'=>'Comment sub 1 menu 1' ] ,
							'link'=>$link->href( $url->url( ) , [ 'submenu'=>'1' ] )
						] , [
							'title'=>[ 'ru'=>'Форма восстановления пароля' , 'en'=>'Remember password form' ] ,
							'comment'=>[ 'ru'=>'Комментарий Подменю 1 меню 1' , 'en'=>'Comment sub 1 menu 1' ] ,
							'link'=>$link->href( $url->url( ) , [ 'submenu'=>'1' ] )
						] , [
							'title'=>[ 'ru'=>'Активировать учетную запись' , 'en'=>'Activation user' ] ,
							'comment'=>[ 'ru'=>'Комментарий Подменю 1 меню 2' , 'en'=>'Comment sub 1 menu 2' ] ,
							'link'=>$link->href( $url->url( ) , [ 'submenu'=>'2' ] )
						] ,
					]
				];
				break;
			default :
				break;
		}
		
		return $data;
	}
	public function as_tag ( ) {
		return $this->visuotag;
	}
	// 
	public function release ( ) {
		
	}
}
<?PHP

namespace damoony;

class alliasglobal_right_www {
	# vars
	public \main $main;
	public alliasglobal_right $right;
	# methods
	public function __construct ( alliasglobal_right $right ) {
		$this->main = $right->main;
		$this->right = $right;
		
		$this->algoritm( );
	}
	
	public function algoritm ( ) {
		$main = $this->main;
		$right = $this->right;
		
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		switch( $url_base[ 1 ] ){
			case 'policy' :
				switch( $url_base[ 2 ] ){
					case 'agreement' :
						$method = 'data_' . $url_base[ 1 ] . '_' . $url_base[ 2 ];
						if( method_exists( $this , $method ) ) $this->{$method}( );
						break;
					default :
						$right->menu_add_data( null , false , 1 , 'statisticts' , 'Статистика' , '' , $link->href( false , [ $url_base[ 0 ] , 'overview' ] , use_all_adress_data : false ) );
						break;
				}
				break;
			default :
				break;
		}
	}
	
	public function data_policy_agreement ( ) {
		$main = $this->main;
		$right = $this->right;
		
		$lang = $main->language;
		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		
		#
		$right_login_agreement_1 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_1' , [ ] , '# Термины и определения' );
		$right_login_agreement_2 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_2' , [ ] , '# Статус соглашения' );
		$right_login_agreement_3 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_3' , [ ] , '# Общие условия' );
		$right_login_agreement_4 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_4' , [ ] , '# Обязанности Пользователя' );
		$right_login_agreement_5 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_5' , [ ] , '# Политика конфиденциальности' );
		$right_login_agreement_6 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_6' , [ ] , '# Интеллектуальные права' );
		$right_login_agreement_7 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_7' , [ ] , '# Реклама' );
		$right_login_agreement_8 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_8' , [ ] , '# Разрешение споров' );
		$right_login_agreement_9 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_9' , [ ] , '# Прочие условия' );
		# comment
		$right_login_agreement_comment_1 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_1' , [ ] , '' );
		$right_login_agreement_comment_2 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_2' , [ ] , '' );
		$right_login_agreement_comment_3 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_3' , [ ] , '' );
		$right_login_agreement_comment_4 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_4' , [ ] , '' );
		$right_login_agreement_comment_5 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_5' , [ ] , '' );
		$right_login_agreement_comment_6 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_6' , [ ] , '' );
		$right_login_agreement_comment_7 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_7' , [ ] , '' );
		$right_login_agreement_comment_8 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_8' , [ ] , '' );
		$right_login_agreement_comment_9 = $lang->get( 'RIGHT_LOGIN_AGREEMENT_COMMENT_9' , [ ] , '' );

		#
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_1 , $right_login_agreement_comment_1 , $link->href( anchor : 'tab_1' ) );
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_2 , $right_login_agreement_comment_2 , $link->href( anchor : 'tab_2' ) );
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_3 , $right_login_agreement_comment_3 , $link->href( anchor : 'tab_3' ) );
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_4 , $right_login_agreement_comment_4 , $link->href( anchor : 'tab_4' ) );
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_5 , $right_login_agreement_comment_5 , $link->href( anchor : 'tab_5' ) );
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_6 , $right_login_agreement_comment_6 , $link->href( anchor : 'tab_6' ) );
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_7 , $right_login_agreement_comment_7 , $link->href( anchor : 'tab_7' ) );
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_8 , $right_login_agreement_comment_8 , $link->href( anchor : 'tab_8' ) );
		# $right->menu_add_data( null , true , 1 , 'statisticts' , $right_login_agreement_9 , $right_login_agreement_comment_9 , $link->href( anchor : 'tab_9' ) );
	}
}

?>